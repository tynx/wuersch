<?php

/**
 * This class represents an HTTP-Request to the backend.
 */
class Request {

	/**
	 * Whetever the current request is valid
	 */
	private $valid = false;

	/**
	 * If the request has valid authentication information this is true
	 */
	private $authenticated = false;

	/**
	 * The HTTP-Method (only get and post currently supported)
	 */
	private $method = null;

	/**
	 * The path of the request, relevant for authentication
	 */
	private $path = null;

	/**
	 * The name of the controller to run an action in
	 */
	private $controller = 'site';

	/**
	 * The name of the action to run
	 */
	private $action = 'index';

	/**
	 * Relevant headers of the HTTP-Request
	 */
	private $headers = array();

	/**
	 * Get-arguments (?xxxx)
	 */
	private $arguments = array();

	/**
	 * The post data (if available) is stored in here
	 */
	private $post = null;

	/**
	 * If authentication was succesful the user-object will be stored in
	 * here
	 */
	private $user = null;

	public function __construct() {
		$this->_parse();
		$this->authenticate();
	}

	public function isValid() {
		return $this->valid;
	}

	public function isAuthenticated() {
		return $this->authenticated;
	}

	public function getAuthenticatedUser() {
		if ($this->authenticated) {
			return $this->user;
		}
		return null;
	}

	private function authenticate() {
		if (!$this->hasHeaderField('hmac')) {
			return;
		}
		if (!$this->hasHeaderField('timestamp')) {
			return;
		}
		if ((int)$this->getHeaderField('timestamp') + Config::AUTH_TIME_THRESHOLD < time()) {
			return;
		}
		$parts = explode(':', $this->getHeaderField('hmac'));
		if (count($parts) !== 2) {
			return;
		}
		$store = new Store();
		$user = $store->getById('user', $parts[0]);
		if ($user === null) {
			return;
		}
		$toHash = $this->getHeaderField('timestamp') . "\n";
		$toHash .= $this->getMethod() . "\n";
		$toHash .= $this->path . "\n";
		$toHash .= md5($this->post) . "\n";
		$calcedHash = sha1_hmac($user->secret, $toHash);
		if ($calcedHash === $parts[1]) {
			$this->authenticated = true;
			$this->user = $user;
			$columns = array(
				'last_seen' => time(),
				'last_ip' => $_SERVER['REMOTE_ADDR'],
			);
			$store->updateById('user', $this->user->id, $columns);
		}
	}

	public function getMethod() {
		return $this->method;
	}

	public function getControllerName() {
		return ucFirst($this->controller) . 'Controller';
	}

	public function getActionName() {
		return 'action' . ucFirst($this->action);
	}

	public function getArgument($name) {
		if (isset($this->arguments[$name]) &&
			!empty($this->arguments[$name])) {
			return $this->arguments[$name];
		}
		return null;
	}

	public function hasHeaderField($name) {
		if (isset($this->headers[$name])) {
			return true;
		}
		return false;
	}

	public function getHeaderField($name) {
		if ($this->hasHeaderField($name)) {
			return $this->headers[$name];
		}
		return null;
	}

	public function getPostData() {
		return $this->post;
	}

	private function _parse() {
		$this->method = strtolower($_SERVER['REQUEST_METHOD']);
		$this->path = str_replace(Config::BASE_LOCATION, '', $_SERVER['REQUEST_URI']);
		$request = explode('?', strtolower($this->path))[0];
		if (!empty($request)) {
			$parts = explode('/', $request);
		} else {
			$parts = array();
		}

		if (count($parts) === 1) {
			$this->controller = $parts[0];
		} elseif (count($parts) === 2) {
			$this->controller = $parts[0];
			if (!empty($parts[1])) {
				$this->action = $parts[1];
			}
		} elseif (count($parts) > 2) {
			$this->valid = false;
			return;
		}
		foreach (getallheaders() as $key => $val) {
			$this->headers[$key] = $val;
		}
		foreach ($_GET as $key => $val) {
			$this->arguments[$key] = $val;
		}
		if ($this->getMethod() === 'post') {
			$this->post = file_get_contents('php://input');
		}
		$this->valid = true;
	}
}
