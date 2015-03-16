<?php

class Request{

	private $valid = false;
	private $controller = 'site';
	private $action = 'index';
	private $arguments = array();

	public function __construct(){
		$this->parse();
	}

	public function isValid(){
		return $this->valid;
	}

	public function getControllerName(){
		return ucFirst($this->controller) . 'Controller';
	}

	public function getActionName(){
		echo 'action' . ucFirst($this->action);
		return 'action' . ucFirst($this->action);
	}

	public function getArgument($name){
		if(isset($this->arguments[$name]) && !empty($this->arguments[$name]))
			return $this->arguments[$name];
		return null;
	}

	private function parse(){
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
		foreach($_GET as $key => $val){
			$this->arguments[$key] = $val;
		}
		if(is_array($_POST)){
			foreach($_POST as $key => $val){
				$this->arguments[$key] = $val;
			}
		}
		$this->valid = true;
	}
}

?>
