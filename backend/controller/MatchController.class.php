<?php

/**
 * This class is for handling all requests regarding match. please not
 * that the actual insert/creation of a match happens in
 * wouldController. This only is for checking if a match between the
 * current and another user is present. also there is a possibility to
 * list all matches.
 * @author Tim Luginbühl (tynx)
 */
class MatchController extends BaseController {

	/**
	 * Which methods need an HMAC-Authentication
	 * in this class: all
	 * @param name the name of the method which needs auth
	 * @return true if the method needs a valid user
	 */
	public function actionRequiresAuth($name) {
		if ($name !== null) {
			return true;
		}
		return false;
	}

	/**
	 * This method checks if given (other) user has a match with the
	 * current user. as it can happen, that the current user is user1
	 * or user2 we have to do 2 queries.
	 * @param idUser the id of the other user
	 */
	public function actionIs($idUser) {
		$otherUser = $this->getStore()->getById('user', $idUser);
		if ($otherUser === null) {
			$this->error('Provided user not found!');
			return;
		}

		// current user is user1
		$columns = array(
			'id_user_1' => $this->user->id,
			'id_user_2' => $otherUser->id,
		);
		$match = $this->getStore()->getByColumns('match', $columns);
		if (is_array($match) && count($match) === 1) {
			$this->addResponse('match', $match[0]->getPublicData());
			return;
		}

		// current user is user2
		$columns = array(
			'id_user_1' => $otherUser->id,
			'id_user_2' => $this->user->id,
		);
		$match = $this->getStore()->getByColumns('match', $columns);
		if (is_array($match) && count($match) === 1) {
			$this->addResponse('match', $match[0]->getPublicData());
			return;
		}
	}

	/**
	 * This method lists all the matches of the current user.
	 */
	public function actionGet() {
		$columns = array(
			'id_user_1' => $this->user->id,
			'id_user_2' => $this->user->id,
		);
		$matches = $this->getStore()->getByColumns('match', $columns, 'OR');
		foreach ($matches as $match) {
			$this->addResponse('match', $match->getPublicData());
		}
	}
}
