<?php
define('WEBROOT', __DIR__);
include('../include.php');

$store = new Store();

var_dump($store->getByColumns('user', array('id_md5'=>'c4ca4238a0b923820dcc509a6f75849b')));
var_dump($store->getByColumns('user', array('id_md5'=>'c4ca4238a0b923820dcc509a6f75849b', 'secret'=>'secret'))[0]->getPublicData());
var_dump($store->getById('user', 'c4ca4238a0b923820dcc509a6f75849b')->getPublicData());


var_dump($store->insert('user', array('name'=>'adsfadsf', 'secret'=>'secret')));
var_dump($store->insert('user', array('name'=>'adsfadsf', 'secret'=>'secret')));
var_dump($store->insert('user', array('name'=>'adsfadsf', 'secret'=>'secret')));

var_dump($store->updateByColumns('user', array('name'=>'newbbName', 'secret'=>'secret'), array('id'=>5)));

$store->updateById('user', 16, array('name'=>'newbbName', 'secret'=>'secret'));

var_dump($store->deleteByColumns('user', array('name'=>'storeTest', 'secret'=>'secret')));
var_dump($store->deleteById('user', 16));
?>
