<?php

/**
 * This class represents a picture from the mysql-table picture.
 * @author Tim Luginbühl (tynx)
 */
class Picture {

	/**
	 * This method returns the public data of a picture.
	 * @return array of the all the data which can be made public.
	 */
	public function getPublicData() {
		return array(
			'id'        => $this->id_md5,
			'isDefault' => (boolean)$this->default,
			'time'      => (int)$this->time,
		);
	}
}
