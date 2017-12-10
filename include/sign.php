<?php
function api_sign($params, $secret, $time = 'time', $sign = 'sign', $skip = '') {
	$string = $secret;
	$params[$time] = thetime();
	ksort($params);
	foreach($params as $key => $value) {
		if($skip != '' && $key == $skip && substr($value, 0, 1) == '@') continue;
		if(is_array($value)) continue;
		$string .= $value;
	}
	$params[$sign] = md5($string);
	return $params;
}

function api_verify($params, $secret, $time = 'time', $sign = 'sign', $skip = '') {
	if(!isset($params[$time]) || !isset($params[$sign])) return false;
	if(thetime() - $params[$time] > 86400) return false;
	$string = $secret;
	ksort($params);
	$signvalue = $params[$sign];
	unset($params[$sign]);
	foreach($params as $key => $value) {
		if($skip != '' && $key == $skip) continue;
		if(is_array($value)) continue;
		$string .= $value;
	}
	if($signvalue != md5($string)) {
		return false;
	}
	unset($params[$time]);
	return $params;
}
?>
