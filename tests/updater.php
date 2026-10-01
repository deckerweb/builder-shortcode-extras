<?php
require __DIR__.'/bootstrap.php';
require_once ABSPATH.'wp-admin/includes/file.php';
require_once ABSPATH.'wp-admin/includes/plugin.php';
WP_Filesystem();
$repo='https://github.com/deckerweb/builder-shortcode-extras';
$api='https://api.github.com/repos/deckerweb/builder-shortcode-extras';
$release=['tag_name'=>'v1.2.1','draft'=>false,'prerelease'=>false,'body'=>'<script>bad</script> Release notes','published_at'=>'2026-10-01T12:00:00Z','assets'=>[['name'=>'builder-shortcode-extras.zip','browser_download_url'=>$repo.'/releases/download/v1.2.1/builder-shortcode-extras.zip']]];
$stub=static function($pre,$args,$url)use(&$release,$api){return $url===$api.'/releases/latest'?['response'=>['code'=>200],'body'=>wp_json_encode($release),'headers'=>[]]:$pre;};
add_filter('pre_http_request',$stub,100,3);
$shared=new \Deckerweb\GitHubReleaseUpdater\V2\Updater(BSE_PLUGIN_FILE,$repo,'Builder Shortcode Extras','Test',(new \Deckerweb\BuilderShortcodeExtras\GitHubUpdates())->artwork());
$property=new ReflectionProperty($shared,'cache');$property->setAccessible(true);$cache=$property->getValue($shared);delete_site_transient($cache);
$headers=get_plugin_data(BSE_PLUGIN_FILE,false,false);$file=plugin_basename(BSE_PLUGIN_FILE);$update=$shared->update(false,$headers,$file,[]);
if(!is_array($update)||'1.2.1'!==$update['version']||$update['package']!==$release['assets'][0]['browser_download_url']||empty($update['icons']['svg']))throw new RuntimeException('Stable update metadata');
if('other'!==$shared->update('other',$headers,'other/other.php',[]))throw new RuntimeException('Updater scope');
$details=$shared->information(false,'plugin_information',(object)['slug'=>'builder-shortcode-extras']);
if(!is_object($details)||empty($details->banners['high'])||str_contains($details->sections['changelog'],'<script>'))throw new RuntimeException('Localized details and escaped notes');
$release['prerelease']=true;delete_site_transient($cache);if(false!==$shared->update(false,$headers,$file,[]))throw new RuntimeException('Prerelease was offered');
$release['prerelease']=false;$release['assets'][0]['browser_download_url']='https://example.test/malicious.zip';delete_site_transient($cache);if(false!==$shared->update(false,$headers,$file,[]))throw new RuntimeException('Foreign package URL was offered');
$adapter=new \Deckerweb\BuilderShortcodeExtras\GitHubUpdates();
$tmp=WP_CONTENT_DIR.'/bse-updater-fixture/';wp_mkdir_p($tmp);$main=$tmp.basename(BSE_PLUGIN_FILE);$text=file_get_contents(BSE_PLUGIN_FILE);$candidate=str_replace(BSE_PLUGIN_VERSION,'1.2.1',$text);file_put_contents($main,$candidate);
$context=['plugin'=>$file,'type'=>'plugin','action'=>'update'];
set_site_transient('update_plugins',(object)['response'=>[$file=>(object)['new_version'=>'1.2.1']]]);
try{
 if($tmp!==$adapter->validate_source($tmp,null,null,$context))throw new RuntimeException('Valid update package rejected');
 file_put_contents($main,str_replace('Plugin Name:       Builder Shortcode Extras','Plugin Name:       Wrong Plugin',$candidate));if(!is_wp_error($adapter->validate_source($tmp,null,null,$context)))throw new RuntimeException('Wrong identity accepted');
 file_put_contents($main,str_replace('Requires PHP:      8.0','Requires PHP:      99.0',$candidate));if(!is_wp_error($adapter->validate_source($tmp,null,null,$context)))throw new RuntimeException('Incompatible package accepted');
 file_put_contents($main,str_replace('1.2.1','1.2.2',$candidate));if(!is_wp_error($adapter->validate_source($tmp,null,null,$context)))throw new RuntimeException('Unoffered version accepted');
 echo "PASS updater stable metadata, scope, artwork, notes, prerelease/foreign URL rejection, identity, requirements and offered-version checks\n";
}finally{unlink($main);rmdir($tmp);delete_site_transient($cache);delete_site_transient('update_plugins');remove_filter('pre_http_request',$stub,100);}
