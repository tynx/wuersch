<?php

include('vendor/vendorsAutoload.php');
require_once('base/Config.class.php');
require_once('base/Request.class.php');
require_once('base/Response.class.php');
require_once('base/JSONResponse.class.php');
require_once('base/Store.class.php');
require_once('base/HMAC.class.php');
require_once('domain/User.class.php');
require_once('domain/Picture.class.php');
require_once('controller/BaseController.class.php');
require_once('controller/SiteController.class.php');
require_once('controller/AuthController.class.php');
require_once('controller/UserController.class.php');
require_once('controller/PictureController.class.php');


function sha1_hmac($key,$data,$blockSize=64,$opad=0x5c,$ipad=0x36) {
	// Keys longer than blocksize are shortened
	if (strlen($key) > $blockSize) {
		$key = sha1($key,true);	
	}
	// Keys shorter than blocksize are right, zero-padded (concatenated)
	$key       = str_pad($key,$blockSize,chr(0x00),STR_PAD_RIGHT);
	$o_key_pad = $i_key_pad = '';
	for($i = 0;$i < $blockSize;$i++) {
		$o_key_pad .= chr(ord(substr($key,$i,1)) ^ $opad);
		$i_key_pad .= chr(ord(substr($key,$i,1)) ^ $ipad);
	}
	return sha1($o_key_pad.sha1($i_key_pad.$data,true));
}


?>
