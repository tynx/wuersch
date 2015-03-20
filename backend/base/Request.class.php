<?php

class Request{

	private $method = null;
	private $valid = false;
	private $controller = 'site';
	private $action = 'index';
	private $headers = array();
	private $arguments = array();
	private $post = null;

	public function __construct(){
		$this->parse();
	}

	public function isValid(){
		if(!$this->valid)
			return false;
		if($this->hasHeaderField('hmac')){
			if(!$this->hasHeaderField('date'))
				return false;
		}
		return true;
	}

	public function getMethod(){
		return $this->method;
	}

	public function getControllerName(){
		return ucFirst($this->controller) . 'Controller';
	}

	public function getActionName(){
		return 'action' . ucFirst($this->action);
	}

	public function getArgument($name){
		if(isset($this->arguments[$name]) && !empty($this->arguments[$name]))
			return $this->arguments[$name];
		return null;
	}

	public function hasHeaderField($name){
		if(isset($this->headers[$name]))
			return true;
		return false;
	}

	public function getHeaderField($name){
		if($this->hasHeaderField($name)){
			return $this->headers[$name];
		}
		return null;
	}

	public function getPostData(){
		return $this->post;
	}

	private function parse(){
		$this->method = strtolower($_SERVER['REQUEST_METHOD']);
		$unparsed = str_replace(Config::$BASE_LOCATION, '', $_SERVER['REQUEST_URI']);
		$request = explode('?', strtolower($unparsed))[0];
		if(!empty($request))
			$parts = explode('/', $request);
		else
			$parts = array();

		if(count($parts)==1){
			$this->controller = $parts[0];
		}elseif(count($parts) == 2){
			$this->controller = $parts[0];
			if(!empty($parts[1]))
			$this->action = $parts[1];
		}elseif(count($parts) > 2){
			$this->valid = false;
			return;
		}
		foreach(getallheaders() as $key=>$val){
			$this->headers[$key] = $val;
		}
		foreach($_GET as $key => $val){
			$this->arguments[$key] = $val;
		}
		if($this->getMethod() === 'post'){
			$this->post = file_get_contents('php://input');
		}
		$this->valid = true;
	}
}

?>
