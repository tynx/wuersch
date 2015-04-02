<?php

class SiteController extends BaseController {

	public function actionRequiresAuth($name) {
		return false;
	}

	public function actionIndex() {
		$this->error('Provide controller/action! Invalid request!');
	}
}
