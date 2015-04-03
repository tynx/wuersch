<?php

/**
 * This class is for handling all requests regarding pictures. It allows
 * to view the "profile"-pic of another user (and only that currently).
 * It allows as well as setting your new default-picture. it provides
 * an call to list all the pictures provided through the FB-API and that
 * are stored on this server.
 * Take note: to actually manage/see pictures first call auth/fetch!
 * @author Tim Luginbühl (tynx)
 */
class PictureController extends BaseController {

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
	 * This method does overwrite any normal behaviour as we want to
	 * sent back an image. It does sent back the default image of the
	 * provided user.
	 * @param idUser the id of the user to show the picture
	 */
	public function actionIndex($idUser) {
		$user = $this->getStore()->getById('user', $idUser);
		if ($user === null) {
			$this->error('Provided user not found!');
			return;
		}
		$columns = array(
			'id_user' => $user->id,
			'default' => 1,
		);
		$picture = $this->getStore()->getByColumns('picture', $columns);
		if (!is_array($picture) || count($picture) !== 1) {
			$this->error('No picture found!');
			return;
		}
		header('Content-type: image/jpeg');
		readfile(WEBROOT . Config::USER_PICTURES . $picture[0]->id_md5 . '.jpg');
		exit(0);
	}

	/**
	 * This method does overwrite any normal behaviour as we want to
	 * sent back an image.
	 * It only allows to show a picture of the current user. But
	 * only one of his own.
	 * @param idPicture the id of the picture to show
	 */
	public function actionOwn($idPicture) {
		$columns = array(
			'id_md5' => $idPicture,
			'id_user' => $this->user->id,
		);
		$pictures = $this->getStore()->getByColumns('picture', $columns);
		if (!is_array($pictures) || count($pictures) !== 1) {
			$this->error('No valid picture found!');
			return;
		}
		header('Content-type: image/jpeg');
		readfile(WEBROOT . Config::USER_PICTURES . $pictures[0]->id_md5 . '.jpg');
		exit(0);
	}

	/**
	 * This method retuns all the pictures of the current user
	 */
	public function actionGet() {
		$columns = array('id_user' => $this->user->id);
		$pictures = $this->getStore()->getByColumns('picture', $columns);
		foreach ($pictures as $picture) {
			$this->addResponse('picture', $picture->getPublicData());
		}
	}

	/**
	 * This method allows to set a new default picture. Provide the new
	 * id and it will be set.
	 * @param idPicture the id of the new default picture
	 */
	public function actionDefault($idPicture) {
		$columns = array(
			'id_user' => $this->user->id,
			'default' => true
		);
		$oldDefault = $this->getStore()->getByColumns('picture', $columns);
		if (!is_array($oldDefault) && count($oldDefault) !== 1) {
			$this->error('Error while finding default picture!');
			return;
		}
		$oldDefault = $oldDefault[0];
		if ($oldDefault->id_md5 === $idPicture) {
			$this->addResponse('picture', $oldDefault->getPublicData(), 'default');
			return;
		}

		$newDefault = $this->getStore()->getById('picture', $idPicture);
		if ($newDefault === null || $newDefault->id_user !== $this->user->id) {
			$this->error('Not a valid picture ID provided!');
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
