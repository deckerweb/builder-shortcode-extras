<?php
/** Additional behavioral checks for output boundaries and content privacy. */
require __DIR__.'/bootstrap.php';
$checks=0;
/** Assert a public behavior without depending on its implementation. @param bool $ok Expected condition. @param string $label Behavior under test. @return void Throws on failure. */
function bse_audit_assert($ok,$label){global $checks;if(!$ok)throw new RuntimeException($label);$checks++;echo "PASS $label\n";}
$ids=array();
try{
 $unsafe='<img src=x onerror=alert(1)>';
 bse_audit_assert(!str_contains(ddw_bse_shortcode_home_link(array('text'=>$unsafe)),'<img src=x'),'Home-link text cannot inject HTML');
 wp_set_current_user(0);bse_audit_assert(!str_contains(ddw_bse_shortcode_loginout(array('login_text'=>$unsafe)),'<img src=x'),'Login-link text cannot inject HTML');
 $id=wp_insert_post(array('post_title'=>'Public audit item','post_content'=>'Readable body','post_status'=>'publish','comment_status'=>'open'));$ids[]=$id;
 bse_audit_assert(str_contains(do_shortcode('[bse-post-link id="'.$id.'" rel="nofollow"]'),'rel="nofollow"'),'Link rel works without target');
 bse_audit_assert(str_contains(ddw_bse_metadata_link_attributes(array('target'=>'','rel'=>'nofollow')),'rel="nofollow"'),'Metadata rel works without target');
 $menu=wp_create_nav_menu('BSE container ID audit');wp_update_nav_menu_item($menu,0,array('menu-item-title'=>'Audit','menu-item-url'=>home_url('/'),'menu-item-status'=>'publish'));
 bse_audit_assert(str_contains(ddw_bse_shortcode_nav_menu(array('menu'=>$menu,'container_id'=>'bse-container-audit')),'id="bse-container-audit"'),'Menu forwards container ID');wp_delete_nav_menu($menu);
 $private=wp_insert_post(array('post_title'=>'Hidden audit item','post_status'=>'private'));$ids[]=$private;
 wp_set_current_user(0);
 bse_audit_assert(!str_contains(do_shortcode('[bse-version type="constant" constant="AUTH_KEY"]'),AUTH_KEY),'Version shortcode withholds authentication key');
 bse_audit_assert(str_contains(do_shortcode('[bse-version type="constant" constant="BSE_PLUGIN_VERSION"]'),BSE_PLUGIN_VERSION),'Documented version constant remains available');
 bse_audit_assert(''===do_shortcode('[bse-post-link id="'.$private.'"]'),'Private post link does not expose title or URL to visitors');
 $form=ddw_bse_shortcode_comment_form(array('post_id'=>$id,'title_reply'=>'<img src=x onerror=alert(1)>','label_submit'=>'<svg onload=alert(1)>'));
 bse_audit_assert(!str_contains($form,'<img src=x')&&!str_contains($form,'<svg onload'),'Comment form labels cannot inject active HTML');
 bse_audit_assert(''===ddw_bse_shortcode_comment_form(array('post_id'=>$private)),'Comment form respects private content access');
 $tag=wp_insert_term('Audit tag','post_tag');if(is_wp_error($tag))throw new RuntimeException($tag->get_error_message());
 wp_set_object_terms($id,array($tag['term_id']),'post_tag');$GLOBALS['post']=get_post($id);setup_postdata($GLOBALS['post']);
 $terms=ddw_bse_shortcode_post_terms(array('taxonomy'=>'post_tag','before'=>'<script>alert(1)</script>','sep'=>'<img src=x onerror=alert(1)>'));
 bse_audit_assert(!str_contains($terms,'<script>')&&!str_contains($terms,'<img src=x'),'Taxonomy labels and separators cannot inject HTML');
 $GLOBALS['bse_audit_fallback']=false;
 /** Mark an unexpected callback. @param array $args Menu arguments. @return string Marker output. */
 function bse_audit_menu_callback($args){$GLOBALS['bse_audit_fallback']=true;return 'UNSAFE-CALL';}
 $menu=ddw_bse_shortcode_nav_menu(array('menu'=>'missing-bse-audit-menu','fallback_cb'=>'bse_audit_menu_callback','walker'=>'arbitrary-string'));
 bse_audit_assert(!str_contains(ddw_bse_shortcode_nav_menu(array('menu'=>'missing-bse-audit-menu','items_wrap'=>'%999999999999999s')),'<script>'),'Unsupported menu format directives are handled without a fatal');
 bse_audit_assert(!$GLOBALS['bse_audit_fallback']&&!str_contains($menu,'UNSAFE-CALL'),'Content authors cannot invoke arbitrary menu callbacks');
 /** Configure a trusted developer callback. @param array $defaults Menu defaults. @return array Amended defaults. */
 $filter=static function($defaults){$defaults['fallback_cb']='bse_audit_menu_callback';return $defaults;};
 add_filter('bse/filter/shortcode_defaults/nav_menu',$filter);ddw_bse_shortcode_nav_menu(array('menu'=>'missing-bse-audit-menu'));remove_filter('bse/filter/shortcode_defaults/nav_menu',$filter);
 bse_audit_assert($GLOBALS['bse_audit_fallback'],'Existing PHP default filters retain trusted menu extension');
 bse_audit_assert(ddw_bse_human_time_diff(0,3661,3)==='1 hour, 1 minute and 1 second','Relative-depth interval formatting preserved');
 bse_audit_assert(ddw_bse_human_time_diff('bad',3600)==='','Invalid interval has controlled empty output');
 $oldpost=$GLOBALS['post'];ddw_bse_get_post_id_by_slug('missing-bse-audit-slug');bse_audit_assert($GLOBALS['post']===$oldpost,'Slug lookup leaves the global loop unchanged');
 bse_audit_assert(!str_contains(ddw_bse_info_values()['url_newsletter'],'MERGE'),'Newsletter link sends no prefilled user information');
 wp_delete_term($tag['term_id'],'post_tag');
 echo "$checks additional behavioral checks passed.\n";
}finally{foreach($ids as $id)wp_delete_post($id,true);wp_reset_postdata();wp_set_current_user(0);}
