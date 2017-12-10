<?php
class ak_request {
	function __construct() {
		$this->file = httpget('file');
		$this->action = httpget('action');
	}
	function __destruct() {
		$content = ob_get_contents();
		$hook = $this->file.'_'.$this->action;
		if(file_exists(actionhookfile($hook))) include(actionhookfile($hook));
	}
}
?>