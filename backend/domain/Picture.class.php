<?php

class Picture{
	public function getPublicData(){
		return array(
			'id'        => $this->id_md5,
			'isDefault' => $this->default,
		);
	}
}

?>
