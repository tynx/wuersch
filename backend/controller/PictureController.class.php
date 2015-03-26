<?php

class PictureController extends BaseController{

	public function actionRequiresAuth($name){
		return true;
	}

	public function actionIndex($idUser){
		$store = new Store();
		$user = $store->getById('user', $idUser);
		if($user === null){
			$this->markAsError('Provided user not found!');
			return;
		}
		$columns = array(
			'id_user'=>$user->id,
			'default'=>1,
		);
		$picture = $store->getByColumns('picture', $columns);
		if(!is_array($picture) || count($picture) !== 1){
			$this->markAsError('No picture found!');
			return;
		}
		header('Content-type: image/jpeg');
		readfile(WEBROOT . Config::$USER_PICTURES . $picture[0]->id_md5 . '.jpg');
		exit(0);
	}

	public function actionGet(){
		$store = new Store();
		$columns = array('id_user'=>$this->user->id);
		$pictures = $store->getByColumns('picture', $columns);
		foreach($pictures as $picture){
			$this->addResponse('picture', $picture->getPublicData());
		}
	}

	public function actionDefault($idPicture){
		$store = new Store();

		$columns = array('id_user'=>$this->user->id, 'default'=>true);
		$oldDefault = $store->getByColumns('picture', $columns);
		if(!is_array($oldDefault) && count($oldDefault) !== 1){
			$this->markAsError('Error while finding default picture!');
			return;
		}
		$oldDefault = $oldDefault[0];
		if($oldDefault->id_md5 === $idPicture){
			$this->addResponse('picture', $oldDefault->getPublicData(), 'default');
			return;
		}

		$newDefault = $store->getById('picture', $idPicture);
		if($newDefault === null || $newDefault->id_user !== $this->user->id){
			$this->markAsError('Not a valid picture ID provided!');
			return;
		}

		$store->update('picture', $oldDefault->id, array('default'=>false));
		$store->update('picture', $newDefault->id, array('default'=>true));
		$columns = array('id_user'=>$this->user->id, 'id_md5'=>$idPicture);
		$oldDefault = $store->getById('picture', $oldDefault->id);
		$newDefault = $store->getById('picture', $newDefault->id);
		$this->addResponse('picture', $newDefault->getPublicData(), 'new default');
		$this->addResponse('picture', $oldDefault->getPublicData(), 'old default');
	}
}

?>
