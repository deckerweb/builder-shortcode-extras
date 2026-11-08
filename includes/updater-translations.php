<?php
/** Copy into the host adapter and replace the builder-shortcode-extras textdomain with its literal domain. */
defined( 'ABSPATH' ) || exit;
/**
 * Resolve an updater source string in the host textdomain.
 * @param string $message English updater message.
 * @return string Translated host message, or original source text.
 */
return static function ( string $message ): string {
    // Literal calls let the host's normal translation extractor collect every source string.
    switch ( $message ) {
        case 'Private mode must be boolean.':
            return __( 'Private mode must be boolean.', 'builder-shortcode-extras' );
        case 'Invalid authentication provider.':
            return __( 'Invalid authentication provider.', 'builder-shortcode-extras' );
        case 'The plugin must be installed in a stable slug directory.':
            return __( 'The plugin must be installed in a stable slug directory.', 'builder-shortcode-extras' );
        case 'Invalid GitHub repository URL.':
            return __( 'Invalid GitHub repository URL.', 'builder-shortcode-extras' );
        case 'The private update could not be authorized. Check the repository credentials and refresh updates.':
            return __( 'The private update could not be authorized. Check the repository credentials and refresh updates.', 'builder-shortcode-extras' );
        case 'Could not create the update download file.':
            return __( 'Could not create the update download file.', 'builder-shortcode-extras' );
        case 'The private update download failed. Check credentials and try again.':
            return __( 'The private update download failed. Check credentials and try again.', 'builder-shortcode-extras' );
        case 'Could not access the update filesystem.':
            return __( 'Could not access the update filesystem.', 'builder-shortcode-extras' );
        case 'GitHub release does not contain the plugin main file.':
            return __( 'GitHub release does not contain the plugin main file.', 'builder-shortcode-extras' );
        case 'Could not prepare the GitHub release package.':
            return __( 'Could not prepare the GitHub release package.', 'builder-shortcode-extras' );
        case 'See the release on GitHub.':
            return __( 'See the release on GitHub.', 'builder-shortcode-extras' );
        case 'Git Updater is active. If a deckerweb installation or update fails, temporarily deactivate Git Updater and retry. Reactivate it afterwards if other plugins or themes need it. Only leave it disabled when those extensions have another reliable update provider.':
            return __( 'Git Updater is active. If a deckerweb installation or update fails, temporarily deactivate Git Updater and retry. Reactivate it afterwards if other plugins or themes need it. Only leave it disabled when those extensions have another reliable update provider.', 'builder-shortcode-extras' );
        case 'Manage installed plugins':
            return __( 'Manage installed plugins', 'builder-shortcode-extras' );
        case 'Dismiss this notice':
            return __( 'Dismiss this notice', 'builder-shortcode-extras' );
        case 'Permission denied.':
            return __( 'Permission denied.', 'builder-shortcode-extras' );
        case 'Public catalog callbacks require both providers and cannot be used in private mode.':
            return __( 'Public catalog callbacks require both providers and cannot be used in private mode.', 'builder-shortcode-extras' );
        case 'The approved release could not be verified.':
            return __( 'The approved release could not be verified.', 'builder-shortcode-extras' );
        case 'The approved release changed. Refresh updates first.':
            return __( 'The approved release changed. Refresh updates first.', 'builder-shortcode-extras' );
        case 'Catalog diagnostics require a public catalog provider.':
            return __( 'Catalog diagnostics require a public catalog provider.', 'builder-shortcode-extras' );
        case 'Catalog approval':
            return __( 'Catalog approval', 'builder-shortcode-extras' );
        case 'Package download':
            return __( 'Package download', 'builder-shortcode-extras' );
        case 'Checksum verification':
            return __( 'Checksum verification', 'builder-shortcode-extras' );
        case 'Archive verification':
            return __( 'Archive verification', 'builder-shortcode-extras' );
        case 'The update server could not be reached. Try again later.':
            return __( 'The update server could not be reached. Try again later.', 'builder-shortcode-extras' );
        case 'The update request timed out. Try again later.':
            return __( 'The update request timed out. Try again later.', 'builder-shortcode-extras' );
        case 'The update server address could not be resolved.':
            return __( 'The update server address could not be resolved.', 'builder-shortcode-extras' );
        case 'The secure connection to the update server failed.':
            return __( 'The secure connection to the update server failed.', 'builder-shortcode-extras' );
        case 'The update server returned an unsuccessful HTTP response.':
            return __( 'The update server returned an unsuccessful HTTP response.', 'builder-shortcode-extras' );
        case 'The update provider did not return valid update data.':
            return __( 'The update provider did not return valid update data.', 'builder-shortcode-extras' );
        case 'The package does not match the approved checksum. Installation was stopped.':
            return __( 'The package does not match the approved checksum. Installation was stopped.', 'builder-shortcode-extras' );
        case 'The archive or plugin identity could not be verified. Installation was stopped.':
            return __( 'The archive or plugin identity could not be verified. Installation was stopped.', 'builder-shortcode-extras' );
        case 'Update stopped at %1$s: %2$s':
            /* translators: 1: update stage, 2: fixed explanation. */
            return __( 'Update stopped at %1$s: %2$s', 'builder-shortcode-extras' );
        case 'HTTP status: %d.':
            /* translators: %d: HTTP error status. */
            return __( 'HTTP status: %d.', 'builder-shortcode-extras' );
        case 'Invalid private download limits.':
            return __( 'Invalid private download limits.', 'builder-shortcode-extras' );
        case 'Invalid package cleanup provider.':
            return __( 'Invalid package cleanup provider.', 'builder-shortcode-extras' );
        case 'Safe package cleanup is unavailable for this update provider.':
            return __( 'Safe package cleanup is unavailable for this update provider.', 'builder-shortcode-extras' );
        case 'The update package identity, version or requirements could not be verified.':
            return __( 'The update package identity, version or requirements could not be verified.', 'builder-shortcode-extras' );
        default:
            return $message;
    }
};
