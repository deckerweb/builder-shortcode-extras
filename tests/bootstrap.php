<?php
/** Set BSE_WP_TEST_ROOT to a disposable WordPress installation; never run on production. */
$root = getenv( 'BSE_WP_TEST_ROOT' );
if ( ! $root || ! is_file( $root . '/wp-load.php' ) ) {
	fwrite( STDERR, "Set BSE_WP_TEST_ROOT to an isolated WordPress installation.\n" );
	exit( 1 );
}
require $root . '/wp-load.php';
if ( ! defined( 'BSE_PLUGIN_VERSION' ) ) {
	require dirname( __DIR__ ) . '/builder-shortcode-extras.php';
	ddw_bse_setup_plugin();
	ddw_bse_run_integrations();
}
