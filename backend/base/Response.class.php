<?php

class Response{
	private $body = null;
	private $contentType = 'text/html';
	
	public function getBody(){
		return $this->body;
	}

	public function getContentType(){
		return $this->contentType;
	}

	public function setBody($body){
		$this->body = $body;
	}

	public function setContentType($contentType){
		$this->contentType = $contentType;
	}
}

?>
