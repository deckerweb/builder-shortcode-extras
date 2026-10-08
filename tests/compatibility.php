<?php
/** Public shortcode compatibility and escaping checks in a disposable fixture. */
require __DIR__ . '/bootstrap.php';
$checks = 0;
/** Assert the public contract. @param bool $condition Expected outcome. @param string $label Behavior. @return void Throws on failure. */
function bse_compat_assert( $condition, $label ) { global $checks; if ( ! $condition ) { throw new RuntimeException( $label ); } ++$checks; echo "PASS $label\n"; }
wp_set_current_user( 1 );
$post_id = wp_insert_post( array( 'post_title' => 'Compatibility item', 'post_status' => 'publish', 'post_author' => 1 ) );
$private_id = wp_insert_post( array( 'post_title' => 'Hidden compatibility item', 'post_status' => 'private', 'post_author' => 1 ) );
$tag = wp_insert_term( 'Compatibility tag', 'post_tag' );
$menu = wp_create_nav_menu( 'BSE compatibility ' . uniqid() );
$old_url = get_user_meta( 1, 'user_url', true );
$old_user = get_userdata( 1 )->user_url;
try {
 wp_update_user( array( 'ID' => 1, 'user_url' => 'https://example.test/author' ) );
 wp_set_object_terms( $post_id, array( $tag['term_id'] ), 'post_tag' );
 $GLOBALS['post'] = get_post( $post_id ); setup_postdata( $GLOBALS['post'] );
 $names = array( 'copyright', 'site_title', 'site_slogan', 'home_link', 'loginout', 'post_date', 'post_time', 'post_modified_date', 'post_modified_time', 'post_author', 'post_author_link', 'post_author_posts_link', 'post_tags', 'post_categories', 'post_terms', 'post_edit' );
 $original_description = get_option( 'blogdescription' ); update_option( 'blogdescription', 'Compatibility slogan' );
 foreach ( $names as $name ) {
  $defaults_seen = false; $output_seen = false;
  /** Supply an attribute through the existing defaults contract. @param array $defaults Default map. @return array Updated map. */
  $defaults = static function( $defaults ) use ( &$defaults_seen ) { $defaults_seen = true; $defaults['class'] = 'compatibility-class'; return $defaults; };
  /** Observe resolved attributes in the existing output filter. @param string $html Output. @param array $atts Resolved attributes. @return string Output. */
  $output = static function( $html, $atts ) use ( &$output_seen ) { $output_seen = $atts['class'] === 'compatibility-class'; return $html; };
  $hook = $name === 'copyright' ? 'bse_copyright' : $name;
  add_filter( 'bse/filter/shortcode_defaults/' . $name, $defaults ); add_filter( 'bse/filter/shortcode/' . $hook, $output, 10, 2 );
  $callback = 'ddw_bse_shortcode_' . $name;
  $html = $callback( array( 'wrapper' => 'section', 'before' => '<script>label</script>', 'after' => '<img src=x>' ) );
  bse_compat_assert( $defaults_seen && $output_seen, "$name retains defaults and output filters" );
  bse_compat_assert( str_contains( $html, '<section class=' ) && str_contains( $html, 'compatibility-class' ) && ! str_contains( $html, '<script>' ) && ! str_contains( $html, '<img src=x>' ), "$name retains wrapper and safely renders labels" );
  remove_filter( 'bse/filter/shortcode_defaults/' . $name, $defaults ); remove_filter( 'bse/filter/shortcode/' . $hook, $output );
 }
 bse_compat_assert( str_contains( ddw_bse_shortcode_copyright( array( 'first' => '2019' ) ), '©&nbsp;2019–' ), 'Copyright entity and year range render as text' );
 foreach ( array( 'post_date', 'post_time', 'post_modified_date', 'post_modified_time' ) as $name ) {
  wp_set_current_user( 0 ); $callback = 'ddw_bse_shortcode_' . $name;
  bse_compat_assert( $callback( array( 'post_id' => $private_id ) ) === '', "$name withholds private dates" );
 }
 wp_set_current_user( 1 );
 $menu_item = wp_update_nav_menu_item( $menu, 0, array( 'menu-item-title' => 'Audit menu item', 'menu-item-url' => 'https://example.test/', 'menu-item-type' => 'custom', 'menu-item-status' => 'publish' ) );
 $html = ddw_bse_shortcode_nav_menu( array( 'menu' => $menu, 'items_wrap' => '<ul>%3$s %999999999999999s</ul>', 'fallback_cb' => false ) );
 bse_compat_assert( str_contains( $html, 'Audit menu item' ) && str_contains( $html, '%999999999999999s' ), 'Actual menu renders supported directive and treats unsupported directive as text' );
 update_option( 'blogdescription', $original_description );
 echo "$checks compatibility checks passed.\n";
} finally {
 wp_update_user( array( 'ID' => 1, 'user_url' => $old_user ) );
 wp_delete_nav_menu( $menu ); wp_delete_term( $tag['term_id'], 'post_tag' ); wp_delete_post( $post_id, true ); wp_delete_post( $private_id, true ); wp_reset_postdata();
}
