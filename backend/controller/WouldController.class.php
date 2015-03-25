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
		$id = $this->storeWould($this->user->id, $otherUser->id);
		$would = $store->getById('would', $id);
		$this->response->addResponse(array('type'=>'would', 'data'=>$would->getPublicData()));
		
	}

	public function actionNot($idUser){
		$store = new Store();
		$otherUser = $store->getById('user', $idUser);
		if($otherUser === null)
			die("not found!");
		$id = $this->storeWould($this->user->id, $otherUser->id, false);
		$would = $store->getById('would', $id);
		$this->response->addResponse(array('type'=>'would', 'data'=>$would->getPublicData()));
		
	}

	public function actionGet(){
		$store = new Store();
		$columns = array('id_user_would'=>$this->user->id);
		$woulds = $store->getByColumns('would', $columns);
		foreach($woulds as $would){
			$this->response->addResponse(array(
				'type'=>'would',
				'data'=>$would->getPublicData(),
			));
		}
	}
}

?>
