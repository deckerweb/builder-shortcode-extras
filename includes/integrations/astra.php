<?php

// includes/integrations/astra

/**
 * Prevent direct access to this file.
 *
 * @since 1.0.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Sorry, you are not allowed to access this file directly.' );
}


add_filter( 'bse/filter/integrations/all', 'ddw_bse_register_integration_astra_custom_layouts' );
/**
 * Register Astra Custom Layouts, via Astra Pro plugin.
 *
 * @since 1.0.0
 *
 * @param array $integrations Holds array of all registered integrations.
 * @return array Tweaked array of registered integrations.
 */
function ddw_bse_register_integration_astra_custom_layouts( array $integrations ) {

	$integrations[ 'astra-custom-layouts' ] = array(
		'label'         => __( 'Astra Custom Layouts', 'builder-shortcode-extras' ),
		'post_type'     => 'astra-advanced-hook',
		'shortcode_tag' => 'astra-layout',
	);

	return $integrations;

}  // end function
