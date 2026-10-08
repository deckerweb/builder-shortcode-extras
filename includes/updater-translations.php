<?php
/** Translate shared update messages through the host domain. @package BuilderShortcodeExtras */
defined( 'ABSPATH' ) || exit;
/**
 * Translate an engine message at output time in the host locale.
 * @param string $message Fixed English engine message.
 * @return string Localized message, or unchanged English fallback.
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
        default:
            return $message;
    }
};
