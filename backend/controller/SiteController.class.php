<?php

class SiteController extends BaseController{

	public function actionRequiresAuth($name){
		return false;
	}

	public function actionIndex(){
		echo "nothing to do...";
	}
}

?>
