<?php
require __DIR__ . '/bootstrap.php';
$count = 0;
function check( $condition, $name ) {
	global $count;
	if ( ! $condition ) { throw new RuntimeException( $name ); }
	++$count; echo "PASS $name\n";
}
wp_set_current_user( 0 );
$one = wp_insert_post( array( 'post_title' => 'First', 'post_content' => 'First body', 'post_status' => 'publish', 'post_date' => '2020-01-01 10:00:00' ) );
$two = wp_insert_post( array( 'post_title' => 'Second', 'post_name' => 'bse-second-' . $one, 'post_content' => '<!-- wp:paragraph --><p>Second body</p><!-- /wp:paragraph -->', 'post_status' => 'publish', 'post_date' => '2022-02-02 12:00:00' ) );
$private = wp_insert_post( array( 'post_title' => 'Private', 'post_content' => 'PRIVATE_SECRET', 'post_status' => 'private' ) );
$draft = wp_insert_post( array( 'post_title' => 'Draft', 'post_content' => 'DRAFT_SECRET', 'post_status' => 'draft' ) );
$locked = wp_insert_post( array( 'post_title' => 'Locked', 'post_content' => 'LOCKED_SECRET', 'post_status' => 'publish', 'post_password' => 'secret' ) );
$ids = array( $one, $two, $private, $draft, $locked );
try {
	$GLOBALS['post'] = get_post( $one ); setup_postdata( $GLOBALS['post'] );
	check( str_contains( do_shortcode( '[bse-post-date post_id="' . $two . '" format="Y-m-d"]' ), '2022-02-02' ), 'Explicit date uses selected post, not loop post' );
	check( str_contains( do_shortcode( '[bse-post-date format="Y-m-d"]' ), '2020-01-01' ), 'Current post default preserved' );
	check( preg_match( '/[0-9]+.*ago/', do_shortcode( '[bse-post-date post_id="' . $two . '" format="relative"]' ) ), 'Relative dates contain elapsed time' );
	check( ! str_contains( do_shortcode( '[bse-copyright first="<img src=x>" copyright="<script>bad</script>"]' ), '<script>' ), 'Copyright text escaped' );
	check( str_contains( do_shortcode( '[bse-site-title class="alpha beta"]' ), 'alpha beta' ), 'Multiple CSS classes preserved' );
	check( str_contains( do_shortcode( '[bse-site-title wrapper="script"]' ), '<span' ), 'Unsafe wrapper falls back to span' );
	check( ! str_contains( do_shortcode( '[bse-post-date label="<script>bad</script>"]' ), '<script>' ), 'Date labels escaped' );
	$block_output = do_shortcode( '[bse-item-content id="' . $two . '"]' );
	check( str_contains( $block_output, 'Second body</p>' ) && ! str_contains( $block_output, '<!-- wp:' ), 'Native block content rendered' );
	check( '' === do_shortcode( '[bse-item-content id="999999"]' ), 'Missing item does not output its ID' );
	foreach ( array( $private, $draft, $locked ) as $id ) { check( '' === do_shortcode( '[bse-item-content id="' . $id . '"]' ), 'Hidden content withheld: ' . get_post_status($id) ); }
	wp_update_post( array( 'ID' => $one, 'post_content' => 'A[bse-item-content id="' . $one . '"]Z' ) );
	check( 'AZ' === do_shortcode( '[bse-item-content id="' . $one . '"]' ), 'Direct recursion stopped' );
	wp_update_post( array( 'ID' => $one, 'post_content' => 'A[bse-item-content id="' . $two . '"]' ) );
	wp_update_post( array( 'ID' => $two, 'post_content' => 'B[bse-item-content id="' . $one . '"]' ) );
	check( 'AB' === do_shortcode( '[bse-item-content id="' . $one . '"]' ), 'Indirect recursion stopped' );
	check( ! shortcode_exists( 'bse-no-shortcode-tag' ), 'No placeholder integration registered' );
	check( shortcode_exists( 'bse-wpblock' ), 'Synced-pattern shortcode registered' );
	$filter = static function($all) { $all['Unsafe Key!'] = array('label'=>'<b>Title</b>', 'post_type'=>'POST!', 'shortcode_tag'=>'CUSTOM!'); $all['broken']=array('post_type'=>array()); return $all; };
	add_filter( 'bse/filter/integrations/all', $filter ); $integrations = ddw_bse_get_integrations(); remove_filter( 'bse/filter/integrations/all', $filter );
	check( isset($integrations['unsafekey']) && 'post' === $integrations['unsafekey']['post_type'] && 'custom' === $integrations['unsafekey']['shortcode_tag'] && 'Title' === $integrations['unsafekey']['label'] && !isset($integrations['broken']), 'Integration normalization persisted and malformed entries skipped' );
	check( 'false' !== do_shortcode('[bse-version type="constant" constant="BSE_MISSING_TEST_CONSTANT"]'), 'Undefined constant handled without fatal' );
	check( str_contains(do_shortcode('[bse-post-count status="not-real"]'), '>0<'), 'Unknown count status handled' );
	$slug = get_post_field('post_name',$two);
	check( str_contains(do_shortcode('[bse-post-link slug="'.$slug.'" post_type="post"]'),esc_url(get_permalink($two))), 'Slug links resolve requested item' );
	check( '' === do_shortcode('[bse-post-link slug="bse-missing-item" post_type="post"]'), 'Missing slug does not link to loop post' );
	check( !str_contains(do_shortcode('[bse-post-link id="'.$two.'" text="<script>bad</script>"]'),'<script>'), 'Link text escaped' );
	check( false === ddw_bse_boolean('false') && false === ddw_bse_boolean('0') && false === ddw_bse_boolean('no') && true === ddw_bse_boolean('true'), 'Boolean strings parsed consistently' );
	$uid=wp_create_user('bse-fixture-'.time(),'unused-test-pass','bse-fixture@example.test');
	wp_update_user(array('ID'=>$uid,'display_name'=>'<script>bad</script>'));
	check( !str_contains(do_shortcode('[bse-user user_id="'.$uid.'" field="display_name"]'),'<script>'), 'User value escaped' );
	check( !str_contains(do_shortcode('[bse-user user_id="999999" default="<img src=x onerror=bad>"]'),'<img'), 'User fallback escaped' );
	require_once ABSPATH.'wp-admin/includes/user.php';wp_delete_user($uid);
	wp_set_current_user(1);
	check( 'PRIVATE_SECRET' === do_shortcode('[bse-item-content id="'.$private.'"]'), 'Authorized private embed retained' );
	check( str_contains( \Deckerweb\BuilderShortcodeExtras\Guide::content(), '<table>' ), 'Local guide renders reference table' );
	check( ! str_contains( \Deckerweb\BuilderShortcodeExtras\Guide::content(), '<script>' ), 'Local guide text does not introduce scripts' );
	$registry=\Deckerweb\BuilderShortcodeExtras\Catalog::all();
	$generated = array_filter($registry,static fn($entry)=>!empty($entry['fields']));
	check( 5 === count($generated), 'Generator limited to five use cases' );
	foreach($registry as $tag=>$entry){ if(!$entry['integration']) check(shortcode_exists($tag),'Catalog core shortcode exists: '.$tag); }
	$updater=new \Deckerweb\BuilderShortcodeExtras\GitHubUpdates();check(is_array($updater->artwork()),'Updater has local artwork');
	check( isset($GLOBALS['deckerweb_library_runtime_v1']) && '0.2.0' === $GLOBALS['deckerweb_library_candidates_v1'][0]['version'], 'Library version' );
} finally {
	foreach($ids as $id) wp_delete_post($id,true);
	wp_reset_postdata();
}
echo "$count regression checks passed.\n";
