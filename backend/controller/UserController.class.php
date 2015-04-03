<?php

class UserController extends BaseController {

	/**
	 * Which methods need an HMAC-Authentication
	 * in this class: all except register
	 * @param name the name of the method which needs auth
	 * @return true if the method needs a valid user
	 */
	public function actionRequiresAuth($name) {
		if ($name === 'actionRegister') {
			return false;
		}
		return true;
	}

	public function actionCurrent() {
		$this->addResponse('user', $this->user->getPublicData());

		$pictures = $this->getStore()->getByColumns(
			'picture',
			array('id_user' => $this->user->id)
		);
		foreach ($pictures as $picture) {
			$this->addResponse('picture', $picture->getPublicData());
		}

		$woulds = $this->getStore()->getByColumns(
			'would',
			array('id_user_would' => $this->user->id)
		);
		foreach ($woulds as $would) {
			$this->addResponse('would', $would->getPublicData());
		}

		$columns = array(
			'id_user_1' => $this->user->id,
			'id_user_2' => $this->user->id,
		);
		$matches = $this->getStore()->getByColumns('match', $columns, 'OR');
		foreach ($matches as $match) {
			$this->addResponse('match', $match->getPublicData());
		}
	}

	public function actionRegister($secret) {
		$id = $this->getStore()->insert('user', array(
			'register_time' => time(),
			'secret'        => $secret,
		));
		$this->getStore()->updateById('user', $id, array('id_md5' => md5($id)));
		$this->addResponse('registration', array(
				'id'                => md5($id),
				'authenticationURL' => 'http://localhost/wuersch/backend/auth/authenticate?idUser=' . md5($id),
			)
		);
	}

	public function actionSettings() {
		$columns = array();
		if (isset($this->postData['interestedInMale'])) {
			$columns['interested_in_male'] = $this->postData['interestedInMale'];
		}
		if (isset($this->postData['interestedInFemale'])) {
			$columns['interested_in_female'] = $this->postData['interestedInFemale'];
		}
		
		if (count($columns) > 0) {
			$this->getStore()->updateById('user', $this->user->id, $columns);
		}
		$updatedUser = $this->getStore()->getById('user', $this->user->id);
		$this->addResponse('user', $updatedUser->getPublicData());
	}

	public function actionRandom() {
		$query = 'SELECT `user`.`id` FROM `wuersch`.`user` WHERE NOT EXISTS';
		$query .= '(SELECT `would`.`id_user_would` FROM `wuersch`.`would` ';
		$query .= 'WHERE `user`.`id`=`would`.`id_user`) AND `user`.`id`';
		$query .= '!=' . $this->user->id . ' AND ';
		$query .= '`user`.`is_male`=' . $this->user->interested_in_male . ' AND ';
		$query .= '`user`.`is_female`=' . $this->user->interested_in_female;
		$query .= ' AND `user`.`fetch_time` > 0 LIMIT 100;';
		$result = $this->getStore()->getByCustomQuery($query);
		if (count($result) === 0) {
			$this->error("No random user found!");
			return;
		}
		$user = $this->getStore()->getById('user', $result[rand(0, count($result)-1)]['id']);
		$this->addResponse('user', $user->getPublicData());
		$columns = array(
			'id_user' => $user->id,
			'default' => 1,
		);
		$picture = $this->getStore()->getByColumns('picture', $columns);
		if (!is_array($picture) && count($picture) !== 1) {
			$this->error("No picture found for user!");
			return;
		}
		$this->addResponse('picture', $picture[0]->getPublicData());
	}
}
