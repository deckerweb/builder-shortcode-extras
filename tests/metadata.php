<?php
/** Integration tests for 1.3.0 metadata and opt-in presentation. */
require __DIR__ . '/bootstrap.php';
$checks = 0;
/** Assert a feature contract without copying the implementation. @param bool $ok Condition. @param string $label Contract. @return void Throws on failure. */
function metadata_check( $ok, $label ) { global $checks; if ( ! $ok ) { throw new RuntimeException( $label ); } ++$checks; echo "PASS $label\n"; }
$ids = array(); $old = $GLOBALS['post'] ?? null;
try {
 $id = wp_insert_post(array('post_title'=>'Title <strong>test</strong>','post_content'=>'<!-- wp:paragraph --><p>One two three four five six.</p><!-- /wp:paragraph -->[bse-test-side-effect]','post_excerpt'=>'Selected manual excerpt with five words','post_status'=>'publish','post_date'=>'2026-02-03 14:30:00')); $ids[]=$id;
 $private=wp_insert_post(array('post_title'=>'Private secret','post_content'=>'Hidden prose','post_status'=>'private'));$ids[]=$private;
 $password=wp_insert_post(array('post_title'=>'Password secret','post_status'=>'publish','post_password'=>'secret'));$ids[]=$password;
 $empty=wp_insert_post(array('post_title'=>'','post_content'=>'<p></p>','post_status'=>'publish'));$ids[]=$empty;
 wp_set_current_user(0);$GLOBALS['post']=get_post($id);
 metadata_check(str_contains(do_shortcode('[bse-post-title]'),'Title &lt;strong&gt;test&lt;/strong&gt;'),'Title honors current item and escapes markup');
 metadata_check(!str_contains(do_shortcode('[bse-post-title wrapper="none"]'),'<span'),'Wrapper-free title has no outer span');
 metadata_check(do_shortcode('[bse-site-title wrapper="none"]')===esc_html(get_bloginfo('name')),'Existing site-title supports wrapper-free output');
 metadata_check(str_contains(do_shortcode('[bse-post-excerpt words="3"]'),'Selected manual excerpt'),'Explicit excerpt takes priority');
 metadata_check(!str_contains(do_shortcode('[bse-post-excerpt words="3"]'),'with five'),'Excerpt obeys word limit');
 $GLOBALS['bse_metadata_side_effect']=false;add_shortcode('bse-test-side-effect',function(){ $GLOBALS['bse_metadata_side_effect']=true;return 'SIDE EFFECT'; });
 wp_update_post(array('ID'=>$id,'post_excerpt'=>''));
 metadata_check(str_contains(do_shortcode('[bse-post-excerpt words="3"]'),'One two three'),'Stored native paragraph supplies excerpt');
 metadata_check(!str_contains(do_shortcode('[bse-post-excerpt]'),'SIDE EFFECT'),'Excerpt never executes embedded shortcodes');
 metadata_check(str_contains(do_shortcode('[bse-reading-time]'),'1 minute'),'Short text has minimum one-minute estimate');
 metadata_check(!$GLOBALS['bse_metadata_side_effect'],'Reading time never executes embedded shortcodes');
 wp_update_post(array('ID'=>$id,'post_content'=>str_repeat('word ',401)));
 metadata_check(str_contains(do_shortcode('[bse-reading-time wpm="200"]'),'3 minutes'),'Reading time rounds up');
 metadata_check(str_contains(do_shortcode('[bse-reading-time wpm="100"]'),'5 minutes'),'Reading speed is configurable');
 foreach(array('bse-post-title','bse-post-excerpt','bse-post-image','bse-reading-time') as $tag){
  metadata_check(do_shortcode('['.$tag.' post_id="'.$private.'"]')==='','Private content is withheld: '.$tag);
  metadata_check(do_shortcode('['.$tag.' post_id="'.$password.'"]')==='','Password content is withheld: '.$tag);
  metadata_check(do_shortcode('['.$tag.' post_id="999999999" fallback="&lt;script&gt;"]')==='&lt;script&gt;','Missing content fallback remains escaped: '.$tag);
 }
 metadata_check(do_shortcode('[bse-post-title post_id="'.$empty.'" fallback="Unavailable"]')==='Unavailable','Empty title uses fallback');
 metadata_check(do_shortcode('[bse-post-image fallback="No image"]')==='No image','Missing featured image uses fallback');
 metadata_check(str_contains(do_shortcode('[bse-post-date semantic="true" wrapper="none"]'),'datetime="2026-02-03T14:30:00'),'Semantic date contains machine-readable local datetime');
 metadata_check(!str_contains(do_shortcode('[bse-post-date semantic="true" wrapper="none"]'),'<span'),'Semantic date supports wrapper-free text');
 metadata_check(str_contains(do_shortcode('[bse-site-updated semantic="true" wrapper="none"]'),'datetime="'),'Site modification date supports semantic markup');
 metadata_check(substr_count(do_shortcode('[bse-post-date semantic="true"]'),'<time')===1,'Semantic date preserves one native time element');
 metadata_check(do_shortcode('[bse-post-date post_id="'.$private.'" semantic="true" fallback="Unavailable"]')==='Unavailable','Semantic fallback does not reveal private date');
 $upload=wp_upload_bits('bse-test-image.png',null,base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+aR3sAAAAASUVORK5CYII='));
 if($upload['error'])throw new RuntimeException($upload['error']);
 $image=wp_insert_attachment(array('post_mime_type'=>'image/png','post_title'=>'Image'),$upload['file'],$id);$ids[]=$image;
 wp_update_attachment_metadata($image,array('width'=>1,'height'=>1,'file'=>_wp_relative_upload_path($upload['file'])));update_post_meta($image,'_wp_attachment_image_alt','A "quoted" image');set_post_thumbnail($id,$image);
 metadata_check(str_contains(do_shortcode('[bse-post-image]'),'<img'),'Featured image uses native image markup');
 metadata_check(str_contains(do_shortcode('[bse-post-image]'),'alt="A &quot;quoted&quot; image"'),'Featured-image alt text is escaped');
 wp_set_current_user(1);metadata_check(str_contains(do_shortcode('[bse-post-title post_id="'.$private.'"]'),'Private secret'),'Authorized private content remains accessible');
 $catalog=\Deckerweb\BuilderShortcodeExtras\Catalog::all();metadata_check(count(array_filter($catalog,static fn($e)=>!!$e['fields']))===9,'All nine generator cases available');
 echo "$checks metadata checks passed.\n";
}finally{foreach($ids as $id){if('attachment'===get_post_type($id)){wp_delete_attachment($id,true);}else{wp_delete_post($id,true);}}remove_shortcode('bse-test-side-effect');$GLOBALS['post']=$old;wp_set_current_user(0);}
