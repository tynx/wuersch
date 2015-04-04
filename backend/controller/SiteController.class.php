<?php

/**
 * This class is for handling all requests that are basically valid, but
 * we don't have an actual request to a API-Call.
 * @author Tim Luginbühl (tynx)
 */
class SiteController extends BaseController {

	/**
	 * Which methods need an HMAC-Authentication
	 * in this class: none
	 * @param name the name of the method which needs auth
	 * @return true if the method needs a valid user
	 */
	public function actionRequiresAuth($name) {
		if ($name !== null) {
			return false;
		}
		return false;
	}

	/**
	 * This is the default method/action called by the Backend-class.
	 * We don't do anything useful but want to tell the user/client
	 * of the backend, that he has reached us, even though we couldn't
	 * make anything out of it.
	 */
	public function actionIndex() {
		$this->getLogger()->info('Probably just empty request. Sending error to client.');
		$this->error('Provide controller/action! Invalid request!');
	}
}
