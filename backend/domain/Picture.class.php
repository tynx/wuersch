<?php

class Picture {
	public function getPublicData() {
		return array(
			'id'        => $this->id_md5,
			'isDefault' => (boolean)$this->default,
			'time'      => (int)$this->time,
		);
	}
}
