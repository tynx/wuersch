<?php

class PictureController extends BaseController {

	/**
	 * Which methods need an HMAC-Authentication
	 * in this class: all
	 * @param name the name of the method which needs auth
	 * @return true if the method needs a valid user
	 */
	public function actionRequiresAuth($name) {
		return true;
	}

	public function actionIndex($idUser) {
		$user = $this->getStore()->getById('user', $idUser);
		if ($user === null) {
			$this->markAsError('Provided user not found!');
			return;
		}
		$columns = array(
			'id_user' => $user->id,
			'default' => 1,
		);
		$picture = $this->getStore()->getByColumns('picture', $columns);
		if (!is_array($picture) || count($picture) !== 1) {
			$this->markAsError('No picture found!');
			return;
		}
		header('Content-type: image/jpeg');
		readfile(WEBROOT . Config::USER_PICTURES . $picture[0]->id_md5 . '.jpg');
		exit(0);
	}

	public function actionGet() {
		$columns = array('id_user' => $this->user->id);
		$pictures = $this->getStore()->getByColumns('picture', $columns);
		foreach ($pictures as $picture) {
			$this->addResponse('picture', $picture->getPublicData());
		}
	}

	public function actionDefault($idPicture) {
		$columns = array(
			'id_user' => $this->user->id,
			'default' => true
		);
		$oldDefault = $this->getStore()->getByColumns('picture', $columns);
		if (!is_array($oldDefault) && count($oldDefault) !== 1) {
			$this->markAsError('Error while finding default picture!');
			return;
		}
		$oldDefault = $oldDefault[0];
		if ($oldDefault->id_md5 === $idPicture) {
			$this->addResponse('picture', $oldDefault->getPublicData(), 'default');
			return;
		}

		$newDefault = $this->getStore()->getById('picture', $idPicture);
		if ($newDefault === null || $newDefault->id_user !== $this->user->id) {
			$this->markAsError('Not a valid picture ID provided!');
			return;
		}

		$this->getStore()->updateById(
			'picture',
			$oldDefault->id,
			array('default' => false)
		);
		$this->getStore()->updateById(
			'picture',
			$newDefault->id,
			array('default' => true)
		);
		$columns = array(
			'id_user' => $this->user->id,
			'id_md5' => $idPicture
		);
		$oldDefault = $this->getStore()->getById('picture', $oldDefault->id);
		$newDefault = $this->getStore()->getById('picture', $newDefault->id);
		$this->addResponse('picture', $newDefault->getPublicData(), 'new default');
		$this->addResponse('picture', $oldDefault->getPublicData(), 'old default');
	}
}
