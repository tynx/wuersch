<?php

class WouldController extends BaseController {

	/**
	 * Which methods need an HMAC-Authentication
	 * in this class: all
	 * @param name the name of the method which needs auth
	 * @return true if the method needs a valid user
	 */
	public function actionRequiresAuth($name) {
		return true;
	}

	private function _storeWould($idUserWould, $idUser, $would = true) {
		$columns = array(
			'id_user_would' => $idUserWould,
			'id_user'       => $idUser,
		);

		$storedWould = $this->getStore()->getByColumns('would', $columns);
		$columns['time'] = time();
		$columns['would'] = $would;
		if (is_array($storedWould) && count($storedWould) === 1) {
			$this->getStore()->updateById('would', $storedWould[0]->id, $columns);
			return $storedWould[0]->id;
		} else {
			return $this->getStore()->insert('would', $columns);
		}
	}

	public function actionIndex($idUser) {
		$otherUser = $this->getStore()->getById('user', $idUser);
		if ($otherUser === null) {
			die("not found!");
		}
		$id = $this->_storeWould($this->user->id, $otherUser->id);
		$would = $this->getStore()->getById('would', $id);
		$this->addResponse('would', $would->getPublicData());
		$columns = array(
			'would'         => true,
			'id_user_would' => $otherUser->id,
			'id_user'       => $this->user->id,
		);
		$woulds = $this->getStore()->getByColumns('would', $columns);
		if (is_array($woulds) && count($woulds) === 1) {
			$columns = array(
				'id_user_1' => $this->user->id,
				'id_user_2' => $otherUser->id,
				'time'      => time(),
			);
			$this->getStore()->insert('match', $columns);
		}
	}

	public function actionNot($idUser) {
		$otherUser = $this->getStore()->getById('user', $idUser);
		if ($otherUser === null) {
			die("not found!");
		}
		$id = $this->_storeWould($this->user->id, $otherUser->id, false);
		$would = $this->getStore()->getById('would', $id);
		$this->addResponse('would', $would->getPublicData());
		$columns = array(
			'id_user_1' => $this->user->id,
			'id_user_2' => $this->user->id,
		);
		$matches = $this->getStore()->getByColumns('match', $columns, 'OR');
		if (is_array($matches) && count($matches) === 1) {
			$this->getStore()->deleteById('match', $matches[0]->id);
		}
	}

	public function actionGet() {
		$columns = array('id_user_would' => $this->user->id);
		$woulds = $this->getStore()->getByColumns('would', $columns);
		foreach ($woulds as $would) {
			$this->addResponse('would', $would->getPublicData());
		}
	}
}
