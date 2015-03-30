<?php

class MatchController extends BaseController{

	public function actionRequiresAuth($name){
		return true;
	}

	public function actionIs($idUser){
		$otherUser = $this->getStore()->getById('user', $idUser);
		if($otherUser === null){
			$this->markAsError('Provided user not found!');
			return;
		}
		$columns = array(
			'id_user_1'=>$this->user->id,
			'id_user_2'=>$otherUser->id,
		);
		$match = $this->getStore()->getByColumns('match', $columns);
		if(is_array($match) && count($match) === 1){
			$this->addResponse('match', $match[0]->getPublicData());
			return;
		}
		$columns = array(
			'id_user_1'=>$otherUser->id,
			'id_user_2'=>$this->user->id,
		);
		$match = $this->getStore()->getByColumns('match', $columns);
		if(is_array($match) && count($match) === 1){
			$this->addResponse('match', $match[0]->getPublicData());
			return;
		}
	}

	public function actionGet(){
		$columns = array(
			'id_user_1'=>$this->user->id,
			'id_user_2'=>$this->user->id,
		);
		$matches = $this->getStore()->getByColumns('match', $columns, 'OR');
		foreach($matches as $match)
			$this->addResponse('match', $match->getPublicData());
	}
}

?>
