<?php

class PictureController extends BaseController{

	public function actionRequiresAuth($name){
		return true;
	}

	public function actionIndex($id){
		$store = new Store();
		$picture = $store->getById('picture', $id);
		//var_dump($picture);
		if($picture === null)
			die("not found");
		
		header('Content-type: image/jpeg');
		readfile(WEBROOT . Config::$USER_PICTURES . $id . '.jpg');
		exit(0);
	}
}

?>
