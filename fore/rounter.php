<?php
require_once CORE_ROOT.'include/common.inc.php';
require_once CORE_ROOT.'include/forecache.func.php';
$forecache = getforecache($currenturl);
require_once CORE_ROOT.'include/fore.inc.php';
if(empty($get_filename)) fore404();
$filename = $get_filename;
if($html = $db->get_by('*', 'filenames', "filename='".$db->addslashes($filename)."'")) {
	$id = $html['id'];
} else {
	fore404();
}

$ver = 1;
if(!empty($_GET['ver'])) $ver = $_GET['ver'];
if(!a_is_int($ver)) $ver = 1;

$itempage = 0;
if(!empty($_GET['itempage'])) $itempage = $_GET['itempage'];
if(!a_is_int($itempage)) $itempage = 0;

$variables = get_item_data($id, '', array('ver' => $ver, 'itempage' => $itempage));
if(empty($variables)) fore404();
if(!empty($settings['uniqueurl'])) {
	if(substr($currenturl, 1) != $variables['currenturl'] && $ver == 1) {
		header('HTTP/1.1 301 Moved Permanently');
		header('location:'.$variables['currenturl']);
		exit;
	}
}

if(!empty($variables['category'])) {
	$category = getcategorycache($variables['category']);
	if($category === false) fore404();
	$modules = getcache('modules');
	$module = $modules[$category['module']];
	if($module['data']['page'] == '-1') fore404();
}
if(!isset($template)) $template = $variables['template'];
$html = foretemplate($template, $variables);
if($forecache === false) setforecache($currenturl, $html);
if(substr($html, 0, 5) == '<?xml') header('Content-Type:text/xml;charset='.$header_charset);
echo $html;
require_once(CORE_ROOT.'include/exit.php');
?>