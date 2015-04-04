<?php

/**
 * This class represents an HTTP-Request to the backend.
 * @author Tim Luginbühl (tynx)
 */
class Request {

	/**
	 * instance of logging for perfoming log-messages
	 */
	private $logger = null;

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

	/**
	 * This is the constructor which just parses the current request
	 * from the global vars and tries to authenticate automatically.
	 */
	public function __construct() {
		$this->logger = 	new Logger();
		$this->_parse();
		$this->_authenticate();
	}

	/**
	 * Returns true if the current request is valid
	 * @return true if valid request
	 */
	public function isValid() {
		return $this->valid;
	}

	/**
	 * Returns true if the current request is authenticated and login
	 * was successful.
	 * @return true if the request was successfully authenticated
	 */
	public function isAuthenticated() {
		return $this->authenticated;
	}

	/**
	 * Returns the authenticated user if the authentication was
	 * successful
	 * @return the user authenticated and if non-existent null
	 */
	public function getAuthenticatedUser() {
		if ($this->isAuthenticated()) {
			return $this->user;
		}
		return null;
	}

	/**
	 * Returns the method of the request
	 * @return the method of the request
	 */
	public function getMethod() {
		return $this->method;
	}

	/**
	 * Returns the controller which should be called.
	 * @return formatted Controller-name
	 */
	public function getControllerName() {
		return ucFirst($this->controller) . 'Controller';
	}

	/**
	 * Returns the action which should be called within the controller
	 * @return formatted Action-name
	 */
	public function getActionName() {
		return 'action' . ucFirst($this->action);
	}

	/**
	 * Returns the scope of the request, only used for logging
	 * @return the scope of the request
	 */
	public function getScope() {
		return $this->controller . '/' . $this->action;
	}

	/**
	 * Returns the get-argument provided by key.
	 * @param name the key of the get-argument
	 * @return the value of the GET-Argument or null if non-existent.
	 */
	public function getArgument($name) {
		if (isset($this->arguments[$name]) &&
			!empty($this->arguments[$name])) {
			return $this->arguments[$name];
		}
		return null;
	}

	/**
	 * Returns true if the request has the header-field set.
	 * @param name the name of the header field
	 * @return true if header-field exists otherwise false
	 */
	public function hasHeaderField($name) {
		if (isset($this->headers[$name])) {
			return true;
		}
		return false;
	}

	/**
	 * Returns the header field
	 * @param name the name of the header field
	 * @return the header field or null if not found
	 */
	public function getHeaderField($name) {
		if ($this->hasHeaderField($name)) {
			return $this->headers[$name];
		}
		return null;
	}

	/**
	 * Returns the unparsed POST-data (body of post-request).
	 * @return the body of POST-request
	 */
	public function getPostData() {
		return $this->post;
	}

	/**
	 * Authenticates the user based on the header fields provided.
	 * It is HMAC-based with following format:
	 * _GET_
	 * sha1_hmac($time . "\nget\nuser/current\n" . md5(null) . "\n")
	 * _POST_
	 * sha1_hmac($time . "\npost\nuser/settings\n" . md5($body) . "\n")
	 */
	private function _authenticate() {
		if (!$this->hasHeaderField('hmac')) {
			$this->logger->debug('No hmac header found, so no auth is performed!');
			return;
		}
		if (!$this->hasHeaderField('timestamp')) {
			$this->logger->warning('No timestamp header found, so invalid auth!');
			return;
		}
		if ((int)$this->getHeaderField('timestamp') + Config::AUTH_TIME_THRESHOLD < time()) {
			$this->logger->warning('Auth is out of time threshold.');
			return;
		}
		$parts = explode(':', $this->getHeaderField('hmac'));
		if (count($parts) !== 2) {
			$this->logger->warning('Invalid content in hmac-auth field');
			return;
		}
		
		$store = new Store();
		$user = $store->getById('user', $parts[0]);
		if ($user === null) {
			$this->logger->debug('User not found. No auth performed.');
			return;
		}
		$toHash = $this->getHeaderField('timestamp') . "\n";
		$toHash .= $this->getMethod() . "\n";
		$toHash .= $this->path . "\n";
		$toHash .= md5($this->post) . "\n";
		$calcedHash = sha1_hmac($user->secret, $toHash);
		if ($calcedHash === $parts[1]) {
			$this->logger->debug('User authenticated.');
			$this->authenticated = true;
			$this->user = $user;
			$columns = array(
				'last_seen' => time(),
				'last_ip' => $_SERVER['REMOTE_ADDR'],
			);
			$store->updateById('user', $this->user->id, $columns);
		}
	}

	/**
	 * This method parses various things provided by the global vars
	 * like $_SERVER, $_GET and so on. It tries to get following
	 * information:
	 * controller (xxx/) and action (/yyy) from the structure (xxx/yyy)
	 * header-fields and authentication
	 * get-arguments and post-body
	 */
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
			$this->logger->warning('We found an invalid count of arguments to be a valid request.');
			$this->valid = false;
			return;
		}
		foreach (getallheaders() as $key => $val) {
			$this->logger->debug('New header for request: ' . $key . ' => ' . $val);
			$this->headers[$key] = $val;
		}
		foreach ($_GET as $key => $val) {
			$this->logger->debug('New argument for request: ' . $key . ' => ' . $val);
			$this->arguments[$key] = $val;
		}
		if ($this->getMethod() === 'post') {
			$this->logger->debug('Reading post from php://input');
			$this->post = file_get_contents('php://input');
		}
		$this->valid = true;
	}
}
