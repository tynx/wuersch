<?php

class JSONResponse extends Response{

	private $jsonArray = array();

	public function __construct(){
		$this->setContentType('application/json');
	}

	public function put($key, $object){
		$this->jsonArray[$key] = $object;
	}

	public function getBody(){
		return json_encode($this->jsonArray);
	}
}

?>
