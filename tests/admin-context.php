<?php
/** Run this separately for site admin and network admin. */
define('WP_ADMIN', true);
define('WP_NETWORK_ADMIN', 'network' === getenv('BSE_TEST_ADMIN_CONTEXT'));
require __DIR__.'/bootstrap.php';
if(shortcode_exists('bse-copyright') || shortcode_exists('bse-item-content')) throw new RuntimeException('Admin shortcodes must be opt-in');
if(!function_exists('ddw_bse_shortcode_item_content')) throw new RuntimeException('Callbacks must remain available');
add_filter('bse/filter/shortcodes_in_admin','__return_true');
ddw_bse_register_shortcode('bse-copyright','ddw_bse_shortcode_copyright');
ddw_bse_register_shortcode('bse-item-content','ddw_bse_shortcode_item_content');
if(!shortcode_exists('bse-copyright') || shortcode_exists('bse-item-content')) throw new RuntimeException('Admin opt-in allows text helpers only');
wp_set_current_user(0);ob_start();\Deckerweb\BuilderShortcodeExtras\Tools::page();$output=ob_get_clean();
if('' !== $output) throw new RuntimeException('Tools page must require manage_options');
wp_set_current_user(1);ob_start();\Deckerweb\BuilderShortcodeExtras\Tools::page();$output=ob_get_clean();
if(!str_contains($output,'bse-generator-heading') || !str_contains($output,'bse-document-changelog')) throw new RuntimeException('Tools page / changelog markup missing');
echo 'PASS '.(WP_NETWORK_ADMIN?'network':'site').' admin: registration, opt-in, permission and tools page'.PHP_EOL;
