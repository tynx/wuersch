<?php

/**
 * This class is for handling all requests regarding users. It allows to
 * register and make settings to an account. It provides an easy way
 * to gather a random account (for client useful).  There is a method
 * called debug, which dumps the current user. Be careful as this
 * information may be cut due to the potential size of a users (in the
 * sense of matches/woulds/picture). Also as it requests ALL the data of
 * the user it should only be used for debugging. For all the
 * information there is an appropriate action/method in the according
 * controller.
 * @author Tim Luginbühl (tynx)
 */
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

	/**
	 * This method returns the information of the current user.
	 */
	public function actionCurrent() {
		$this->addResponse('user', $this->user->getPublicData());
	}

	/**
	 * This method is only for debugging! Do NOT use it on a regular
	 * basis!
	 * Returns everything of a user.
	 */
	public function actionDebug() {
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

	/**
	 * This method is for registering a new user. You have to provide a
	 * secret for the HMAC-Auth. Make sure u implement a good
	 * randomness!
	 * @param secret the secret of the client
	 */
	public function actionRegister($secret) {
		$id = $this->getStore()->insert('user', array(
			'register_time' => time(),
			'secret'        => $secret,
		));
		$this->getStore()->updateById('user', $id, array('id_md5' => md5($id)));
		$this->addResponse('registration', array(
				'id'                => md5($id),
				'authenticationURL' => Config::FACEBOOK_APP_AUTH_URL . '?idUser=' . md5($id),
			)
		);
	}

	/**
	 * This method allows the set the settings of a user. Currently only
	 * what choices of gender is supported and needed.
	 */
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

	/**
	 * This method returns a random user of interest.
	 */
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
