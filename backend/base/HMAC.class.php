<?php

class HMAC{

	private $id = null;
	private $hash = null;
	private $valid = true;

	public function __construct($hmac){
		$parts = explode(':', $hmac);
		var_dump($parts);
		if(count($parts) !== 2){
			$this->valid = false;
		}else{
			$this->id = $parts[0];
			$this->hash = $parts[1];
		}
	}

	public function isValid(){
		return $this->valid;
	}

	public function getId(){
		return $this->id;
	}

	public function compareHash($secret, $date, $method, $urlPath, $content){
		$toHash = $date . "\n" . strtoupper($method) . "\n" . $urlPath . "\n" . md5($content);
		if(sha1_hmac($secret, $toHash) == $this->hash)
			return true;
		return false;
	}
}

?>
