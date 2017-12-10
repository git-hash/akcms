<?php
if(!defined('CORE_ROOT')) exit;

function fixhalt($error) {
	global $db, $dbtype, $tablepre;
	require CORE_ROOT.'install/install.sql.php';

	debug($error);

	if(preg_match("/Table '([^']+)\.([^']+)' doesn't exist/", $error, $match)) {
		$fulltablename = $match[2];
		$tablename = substr($fulltablename, strlen($tablepre) + 1);
		if(!isset($createtablesql[$tablename])) return false;
		
		$value = $createtablesql[$tablename];
		$value['charset'] = $charset;
		$createtablesql = mysql_createtable($tablepre.'_'.$tablename, $value);
		$_sqls = explode(";\n", $createtablesql);
		foreach($_sqls as $_sql) {
			$db->query($_sql);
		}
		foreach($insertsql as $key => $value) {
			if($value['tablename'] != $tablename) continue;
			$db->insert($value['tablename'], $value['value']);
		}
		$tables = $db->getalltables();
		if(in_array($fulltablename, $tables)) {
			return true;
		} else {
			return false;
		}
		
	} elseif(preg_match("/(INSERT|REPLACE).*?([`_a-z0-9]+)\s*?\(.*?Unknown column '([a-z0-9_]+)' in 'field list'/is", $error, $match)) {
		$fulltablename = trim($match[2], '`');
		$tablename = substr($fulltablename, strlen($tablepre) + 1);
		
		$fieldname = $match[3];
	} elseif(preg_match("/UPDATE\s+(\S+)\s+SET.*?Unknown column '([a-z0-9_]+)' in 'field list'/is", $error, $match)) {
		$fulltablename = trim($match[1], '`');
		$tablename = substr($fulltablename, strlen($tablepre) + 1);
		
		$fieldname = $match[2];
	} else {
		return false;
	}
	
	if(isset($fieldname)) {
		if(strpos($dbtype, 'mysql') === false) return false;
		if(!isset($createtablesql[$tablename]) || !isset($createtablesql[$tablename]['fields'][$fieldname])) return false;
		$value = $createtablesql[$tablename]['fields'][$fieldname];
		$t = "{$value['type']}({$value['length']})";
		if($value['type'] == 'text') $t = 'TEXT';
		$db->addfield($tablename, $fieldname, $t);
		return true;
	}
}
?>