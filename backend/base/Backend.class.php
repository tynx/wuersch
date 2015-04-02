<?php

/**
 * This is the main class for the backend. This handles request and
 * responses as well as the validation of requests.
 * @author Tim Luginbühl (tynx)
 */
class Backend {

	/**
	 * This is the request-object
	 */
	private $request = null;

	/**
	 * This is the response which will be returned
	 */
	private $response = null;

	/**
	 * The constructor takes a request to handle
	 * @param request a request-object
	 */
	public function __construct($request) {
		$this->request = $request;
	}

	/**
	 * This methods does run the according code for the given request.
	 */
	public function run() {
		if (!$this->request->isValid()) {
			$this->_setError('Your request could not be parsed/understood!');
			return;
		}

		$controllerName = $this->request->getControllerName();
		$actionName = $this->request->getActionName();
		if (!class_exists($controllerName, false)) {
			$this->_setError('The requested controller does not exist!');
			return;
		}

		$controller = new $controllerName();
		if (!is_subclass_of($controller, 'BaseController')) {
			$this->_setError('Check your backend code! Seems the controller aren\'t setup properly.');
			return;
		}

		if (!method_exists($controller, $actionName)) {
			$this->_setError('The requested method does not exist!');
			return;
		}

		if ($controller->actionRequiresAuth($actionName) && !$this->request->isAuthenticated()) {
			$this->_setError('Authentication is required. Not provided or invalid!');
			return;
		}

		if ($this->request->getAuthenticatedUser() !== null) {
			$controller->setUser($this->request->getAuthenticatedUser());
		}

		$controller->setRequest($this->request);

		$arguments = array();
		$rm = new ReflectionMethod($controllerName, $actionName);
		$params = $rm->getParameters();
		foreach ($params as $i => $param) {
			if (!$param->isOptional() && $this->request->getArgument($param->getName()) === null) {
				$this->_setError('Arguments missing: ' . $param->getName());
				return;
			}
			$arguments[] = $this->request->getArgument($param->getName());
		}
		call_user_func_array(array($controller, $actionName), $arguments);
		$this->response = $controller->getResponse();
	}

	/**
	 * Sets an error-response
	 */
	private function _setError($errorMessage) {
		$this->response = new Response();
		$this->response->markAsError($errorMessage);
	}

	/**
	 * Returns the generated response
	 */
	public function getResponse() {
		return $this->response;
	}
}
