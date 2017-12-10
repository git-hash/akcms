<?php
if(!defined('CORE_ROOT')) exit;
if(!isset($apiparams['app'])) exit('error');
$app = $apiparams['app'];
if(!preg_match("/^[0-9a-zA-Z_]+$/i", $app)) exit('error2');
require_once CORE_ROOT.'include/common.inc.php';
if(file_exists(AK_ROOT.'configs/apps/_dependhook/-'.$app.'.php')) include(AK_ROOT.'configs/apps/_dependhook/-'.$app.'.php');
require_once CORE_ROOT.'include/fore.inc.php';
require_once CORE_ROOT.'include/app.inc.php';
if(file_exists(AK_ROOT.'configs/apps/_dependhook/+'.$app.'.php')) include(AK_ROOT.'configs/apps/_dependhook/+'.$app.'.php');
include_once(APP_PATH.'foreprogram/index.php');
aexit();
?>
