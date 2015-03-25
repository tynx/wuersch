<?php

class Backend{
	private $request = null;
	private $response = null;

	public function __construct($request){
		$this->request = $request;
	}

	public function run(){
		if(!$this->request->isValid()){
			$this->setError('Your request could not be parsed/understood!');
			return;
		}
		$controllerName = $this->request->getControllerName();
		$actionName = $this->request->getActionName();
		if(!class_exists($controllerName, false)){
			$this->setError('The requested controller does not exist!');
			return;
		}

		$controller = new $controllerName();
		if (!is_subclass_of($controller, 'BaseController')){
			$this->setError('Check your backend code! Seems the controller aren\'t setup properly.');
			return;
		}

		if (!method_exists($controller, $actionName)){
			$this->setError('The requested method does not exist!');
			return;
		}

		if($controller->actionRequiresAuth($actionName) && !$this->request->isAuthenticated()){
			$this->setError('Authentication is required. Not provided or invalid!');
			return;
		}

		if($this->request->getAuthenticatedUser() !== null)
			$controller->setUser($this->request->getAuthenticatedUser());

		$controller->setRequest($this->request);

		$arguments = array();
		$rm = new ReflectionMethod($controllerName, $actionName);
		$params = $rm->getParameters();
		foreach ($params as $i => $param){
			if (!$param->isOptional() && $this->request->getArgument($param->getName()) === null) {
				$this->setError('Arguments missing: ' . $param->getName());
				return;
			}
			$arguments[] = $this->request->getArgument($param->getName());
		}
		call_user_func_array(array($controller, $actionName), $arguments);
		$this->response = $controller->getResponse();
	}

	private function setError($errorMessage){
		$this->response = new Response();
		$this->response->markAsError($errorMessage);
	}

	public function getResponse(){
		return $this->response;
	}
}

?>
