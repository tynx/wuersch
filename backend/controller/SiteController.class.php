<?php

class SiteController extends BaseController {

	/**
	 * Which methods need an HMAC-Authentication
	 * in this class: none
	 * @param name the name of the method which needs auth
	 * @return true if the method needs a valid user
	 */
	public function actionRequiresAuth($name) {
		return false;
	}

	public function actionIndex() {
		$this->error('Provide controller/action! Invalid request!');
	}
}
