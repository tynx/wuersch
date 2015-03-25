<?php

class PictureController extends BaseController{

	public function actionRequiresAuth($name){
		return true;
	}

	public function actionIndex($idUser){
		$store = new Store();
		$user = $store->getById('user', $iduser);
		$columns = array(
			'id_user'=>$user->id,
			'default'=>1,
		);
		$picture = $store->getByColumns('picture', $columns);
		if($picture === null || count($picture) !== 1)
			die("not found");
		header('Content-type: image/jpeg');
		readfile(WEBROOT . Config::$USER_PICTURES . $picture[0]->id_md5 . '.jpg');
		exit(0);
	}
}

?>
