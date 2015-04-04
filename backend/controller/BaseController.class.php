<?php

/**
 * This class represents an controller. Every controller of the backend
 * _needs_ to implement/extend this base abstract class. It does handle
 * somoe basic overhead.
 * @author Tim Luginbühl (tynx)
 */
abstract class BaseController {

	/**
	 * The post-data of the request (body of http-post)
	 */
	protected $postData = null;

	/**
	 * The request object to access header-fields and similiar
	 */
	protected $request = null;

	/**
	 * The current authenticated user is stored in here. Shortcut for
	 * the controllers.
	 */
	protected $user = null;

	/**
	 * The response which will be returned by the backend-class.
	 */
	private $response = null;

	/**
	 * All subclasses should use the same store-object. This is the
	 * place it's stored. accessed by getStore().
	 */
	private $store = null;

	/**
	 * The instance of the logger all subControllers should use.
	 */
	private $logger = null;

	/**
	 * The constructor initializes the response and the store for all
	 * it's subclasses.
	 */
	public final function __construct() {
		$this->response = new Response();
		$this->store = new Store();
	}

	/**
	 * Which methods need an HMAC-Authenticatoin
	 * @param name the name of the method which needs auth
	 * @return true if the method needs a valid user
	 */
	public abstract function actionRequiresAuth($name);

	/**
	 * This method is to be called imediatelly after the construct so
	 * the BaseController and all other controllers know what to do.
	 * @param request the current request to process
	 */
	public final function setRequest($request) {
		if ($request->getHeaderField('Content-Type') === 'application/json') {
			$this->postData = json_decode($request->getPostData(), true);
		}
		$this->request = $request;
	}

	/**
	 * This method is for setting the instance of the logger which the
	 * all the controllers should use within this request.
	 * @param logger the logger-instance to use
	 */
	public final function setLogger($logger) {
		$this->logger = $logger;
	}

	/**
	 * Returns the instance of the logger which all controllers should
	 * use.
	 * @return the instance of the logger to use
	 */
	protected final function getLogger() {
		return $this->logger;
	}

	/**
	 * This sets the current user of the request. Needs to be set in
	 * case of authentication-based requests.
	 * @param user the current(and authenticated) user
	 */
	public final function setUser($user) {
		$this->user = $user;
	}

	/**
	 * This returns the generated response by the according controller.
	 * @return the response to print to client
	 */
	public final function getResponse() {
		return $this->response;
	}

	/**
	 * This is for all the other controllers. We only want one Store-
	 * object. In case of a new implementation we can massively reduce
	 * the refactoring.
	 * @return the initialized and usable store
	 */
	protected final function getStore() {
		return $this->store;
	}

	/**
	 * This method allows other controllers to produce an error in a
	 * very simple way.
	 * @param errorMessage the errorMessage which should be setnt to the
	 * client
	 */
	protected final function error($errorMessage) {
		$this->response->markAsError($errorMessage);
	}

	/**
	 * This method allows an easy way to append response-object to
	 * the overall response. The type should declare what kind of object
	 * is to be found in data (only relevant for clients). If needed
	 * a controller can append additional information with
	 * additionalStatus.
	 * @param type the type of the data of the response (e.g user)
	 * @param data the actual data which is sent to the client
	 * @param additionalStatus you wish to addiontally share information
	 * with the client.
	 */
	protected final function addResponse($type, $data, $additionalStatus = null) {
		$response = array(
			'type' => $type,
			'data' => $data,
		);
		if ($additionalStatus !== null) {
			$response['additionalStatus'] = $additionalStatus;
		}
		$this->response->addResponse($response);
	}
}
