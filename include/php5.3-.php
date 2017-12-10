<?php
function render_item_data($data) {
	if(preg_match("/\[#@([a-z0-9A-Z_\-]+\.htm)(\/\/.*)?#\]/", $data, $match)) {
		$data = preg_replace_callback("/\[#@([a-z0-9A-Z_\-]+\.htm)(\/\/.*)?#\]/", 'render_item_data_callback1', $data);
	}
	if(preg_match("/\[#\\\$([a-z0-9A-Z_\-]+)(\/\/.*)?#\]/", $data, $match)) {
		$data = preg_replace_callback("/\[#\\\$([a-z0-9A-Z_\-]+)(\/\/.*)?#\]/", 'render_item_data_callback2', $data);
	}
	if(preg_match("/\[#([a-z0-9]+)(.*?)#\]/", $data, $match)) {
		$data = preg_replace_callback("/\[#([a-z0-9]+)(.*?)#\]/", 'render_item_data_callback3', $data);
	}
	return $data;
}

function render_item_data_callback1($matches) {
	$_data = render_template($matches[1], array('subtemplate' => 1));
	return preg_replace("/<!--akcms-->.*?<\/script>/", '', $_data);
}

function render_item_data_callback2($matches) {
	global $globalvariables;
	if(!isset($globalvariables[$matches[1]])) return '';
	return $globalvariables[$matches[1]];
}

function render_item_data_callback3($matches) {
	$function = $matches[1];
	if(!function_exists($function)) return "<!--$function {$matches[2]} error-->";
	$p = htmlspecialchars_decode($matches[2], ENT_QUOTES).' ';
	preg_match_all("/\s*([a-z0-9]+)=['\"](.+?)['\"][\\$\s]/", $p, $m);
	$params = array();
	foreach($m[1] as $id => $value) {
		$params[$value] = $m[2][$id];
	}
	$params['return'] = 1;
	return $function($params);
}
?>