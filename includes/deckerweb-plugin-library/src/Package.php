<?php
namespace Deckerweb\PluginLibrary\V0_2_0;
if ( ! defined( 'ABSPATH' ) ) { exit; }

/** Verify the original release, then normalize a bounded, single-plugin archive. */
final class Package {
	const MAX_DOWNLOAD = 20971520;
	const MAX_EXPANDED = 83886080;
	public static function download( array $entry ) {
		if ( ! class_exists( '\ZipArchive' ) ) { return new \WP_Error( 'dwl_zip', Library::t( 'PHP ZIP support is required.', 'PHP-ZIP-Unterstützung wird benötigt.' ) ); }
		require_once ABSPATH . 'wp-admin/includes/file.php';
		if ( ! Catalog::download_url( $entry['download_url'], $entry['repository'] ) ) { return new \WP_Error( 'dwl_url', 'Untrusted release URL.' ); }
		$file = wp_tempnam( $entry['slug'] . '.zip' );
		if ( ! $file ) { return new \WP_Error( 'dwl_temp', 'Cannot create temporary file.' ); }
		$response = wp_safe_remote_get( $entry['download_url'], [
			'timeout' => 45, 'redirection' => 5, 'stream' => true, 'filename' => $file,
			'limit_response_size' => self::MAX_DOWNLOAD + 1,
			'headers' => [ 'Accept' => 'application/octet-stream' ],
		] );
		if ( is_wp_error( $response ) || wp_remote_retrieve_response_code( $response ) !== 200 ) {
			wp_delete_file( $file );
			return new \WP_Error( 'dwl_download', Library::t( 'The GitHub release could not be downloaded.', 'Das GitHub-Release konnte nicht heruntergeladen werden.' ) );
		}
		$result = self::verify( $file, $entry );
		wp_delete_file( $file );
		return $result;
	}

	/** Return a new safe archive path; callers must delete it after use. */
	public static function verify( string $file, array $entry ) {
		require_once ABSPATH . 'wp-admin/includes/file.php';
		if ( ! class_exists( '\ZipArchive' ) ) { return new \WP_Error( 'dwl_zip', 'PHP ZIP support is required.' ); }
		if ( ! is_file( $file ) || filesize( $file ) > self::MAX_DOWNLOAD || ! hash_equals( $entry['sha256'], (string) hash_file( 'sha256', $file ) ) ) {
			return new \WP_Error( 'dwl_hash', Library::t( 'The release checksum does not match. Installation stopped.', 'Die Prüfsumme des Releases stimmt nicht überein. Installation gestoppt.' ) );
		}
		$zip = new \ZipArchive();
		if ( $zip->open( $file ) !== true ) { return new \WP_Error( 'dwl_zip', 'Invalid ZIP archive.' ); }
		$names = []; $expanded = 0; $seen = [];
		$error = '';
		if ( $zip->numFiles > 3000 ) { $error = 'Archive contains too many files.'; }
		for ( $i = 0; $i < $zip->numFiles && $error === ''; $i++ ) {
			$stat = $zip->statIndex( $i );
			if ( ! is_array( $stat ) ) { $error = 'Cannot read archive entry.'; break; }
			$name = $stat['name'];
			if ( strpos( $name, "\0" ) !== false || strpos( $name, '\\' ) !== false || preg_match( '~(^|/)\.\.?(/|$)~', $name ) || str_starts_with( $name, '/' ) ) { $error = 'Unsafe archive path.'; break; }
			$expanded += (int) $stat['size'];
			if ( $expanded > self::MAX_EXPANDED ) { $error = 'Expanded archive is too large.'; break; }
			$opsys = 0; $attrs = 0;
			$zip->getExternalAttributesIndex( $i, $opsys, $attrs );
			if ( ( ( $attrs >> 16 ) & 0170000 ) === 0120000 ) { $error = 'Archive symlinks are not permitted.'; break; }
			// Old release ZIPs may contain Finder resource forks; never install them.
			if ( str_starts_with( $name, '__MACOSX/' ) || basename( $name ) === '.DS_Store' ) { continue; }
			if ( ! str_starts_with( $name, $entry['slug'] . '/' ) || isset( $seen[$name] ) ) { $error = 'Release must contain exactly the approved plugin directory.'; break; }
			$seen[$name] = true;
			$names[] = $name;
		}
		$main = $zip->getFromName( $entry['plugin_file'] );
		if ( $error === '' && ( ! is_string( $main ) || strlen( $main ) > 2097152 || ! preg_match( '/^[ \t\/*#@]*Version:\s*([^\r\n]+)/mi', substr( $main, 0, 8192 ), $match ) || trim( $match[1] ) !== $entry['version'] ) ) { $error = 'Plugin identity or version does not match the catalog.'; }
		if ( $error === '' && ! preg_match( '/^[ \t\/*#@]*Plugin Name:\s*\S+/mi', substr( $main, 0, 8192 ) ) ) { $error = 'Missing plugin header.'; }
		if ( $error !== '' ) { $zip->close(); return new \WP_Error( 'dwl_package', $error ); }
		$safe_file = wp_tempnam( $entry['slug'] . '-verified.zip' );
		$safe = new \ZipArchive();
		if ( ! $safe_file || $safe->open( $safe_file, \ZipArchive::OVERWRITE ) !== true ) { $zip->close(); if ( $safe_file ) { wp_delete_file( $safe_file ); } return new \WP_Error( 'dwl_temp', 'Cannot create verified archive.' ); }
		foreach ( $names as $name ) {
			if ( str_ends_with( $name, '/' ) ) { $ok = $safe->addEmptyDir( rtrim( $name, '/' ) ); }
			else { $content = $zip->getFromName( $name ); $ok = is_string( $content ) && $safe->addFromString( $name, $content ); }
			if ( ! $ok ) { $error = 'Cannot normalize archive.'; break; }
		}
		$closed = $safe->close(); $zip->close();
		if ( $error !== '' || ! $closed ) { wp_delete_file( $safe_file ); return new \WP_Error( 'dwl_zip', $error ?: 'Cannot finalize archive.' ); }
		return $safe_file;
	}
}
