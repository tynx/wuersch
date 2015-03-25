<?php

class SiteController extends BaseController{

	public function actionRequiresAuth($name){
		return false;
	}

	public function actionIndex(){
		$this->response->addResponse(array('type'=>'none', 'data'=>'index'));
	}
}

?>
