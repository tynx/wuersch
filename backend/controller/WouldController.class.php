<?php

class WouldController extends BaseController{

	public function actionRequiresAuth($name){
		return true;
	}

	private function storeWould($idUserWould, $idUser, $would = true){
		$store = new Store();
		$columns = array(
			'id_user_would'=>$idUserWould,
			'id_user'=>$idUser,
			'time'=>time(),
			'would'=>$would,
		);
		return $store->insert('would', $columns);
	}

	public function actionIndex($idUser){
		$store = new Store();
		$otherUser = $store->getById('user', $idUser);
		if($otherUser === null)
			die("not found!");
		var_dump($this->storeWould($this->user->id, $otherUser->id));
		
	}

	public function actionNot($idUser){
		$store = new Store();
		$otherUser = $store->getById('user', $idUser);
		if($otherUser === null)
			die("not found!");
		var_dump($this->storeWould($this->user->id, $otherUser->id, false));
	}
}

?>
