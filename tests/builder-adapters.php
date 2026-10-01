<?php
namespace Elementor {
	class Plugin {
		public $frontend;
		private static $instance;
		public static function instance() {
			if (!self::$instance) { self::$instance = new self(); self::$instance->frontend = new class {
				public function get_builder_content_for_display($id,$css) {
					if ('throw' === get_post_meta($id,'_bse_test',true)) throw new \RuntimeException('Adapter exception');
					return 'ELEMENTOR:'.$id.':'.($css?'CSS':'PLAIN');
				}
			}; }
			return self::$instance;
		}
	}
}
namespace Elementor\TemplateLibrary { class Source_Local { const CPT='elementor_library'; } }
namespace {
	define('ELEMENTOR_VERSION','3.0.0');
	class FLBuilder {}
	require __DIR__.'/bootstrap.php';
	wp_set_current_user(1);
	$normal=wp_insert_post(['post_title'=>'Normal fixture','post_status'=>'publish','post_content'=>'NORMAL']);
	$elementor=wp_insert_post(['post_title'=>'Elementor fixture','post_status'=>'publish','post_content'=>'ELEMENTOR_CONTENT']);
	$beaver=wp_insert_post(['post_title'=>'Beaver fixture','post_status'=>'publish','post_content'=>'BEAVER_CONTENT']);
	update_post_meta($elementor,'_elementor_edit_mode','builder');update_post_meta($beaver,'_fl_builder_enabled',1);
	add_shortcode('fl_builder_insert_layout',static fn($atts)=>'BEAVER:'.($atts['id']??''));
	try {
		if('NORMAL'!==ddw_bse_render_item_content($normal)) throw new RuntimeException('Installed Beaver must not override normal content');
		if('BEAVER:'.$beaver!==ddw_bse_render_item_content($beaver)) throw new RuntimeException('Beaver adapter missing');
		if('ELEMENTOR:'.$elementor.':PLAIN'!==do_shortcode('[bse-elementor-template id="'.$elementor.'" css="false"]')) throw new RuntimeException('Elementor plain');
		if('ELEMENTOR:'.$elementor.':CSS'!==do_shortcode('[bse-elementor-template id="'.$elementor.'" css="true"]')) throw new RuntimeException('Elementor CSS');
		update_post_meta($elementor,'_bse_test','throw');try{ddw_bse_render_item_content($elementor);}catch(RuntimeException $e){}
		delete_post_meta($elementor,'_bse_test');if('ELEMENTOR:'.$elementor.':PLAIN'!==ddw_bse_render_item_content($elementor))throw new RuntimeException('Stack cleanup after exception');
		echo "PASS representative Elementor / Beaver adapters, CSS flag, normal-content routing and exception cleanup\n";
	} finally { foreach([$normal,$elementor,$beaver] as $id)wp_delete_post($id,true); }
}
