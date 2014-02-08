<?php
/**
 * Created by Dumitru Russu.
 * User: developer
 * Date: 4/1/13
 * Time: 10:58 PM
 * To change this template use File | Settings | File Templates.
 */
require_once '../lib/RJson.php';


class DataProcess {
	public static function getData() {
		$data = file_get_contents('init_array.txt');
		return RJson::pack(unserialize($data), true);
	}

	public static function saveData($data) {
		$compactData = RJson::unpack($data);
		return file_put_contents('final_array_unpacked.txt', serialize($compactData));
	}
}

//Call function
if(isset($_GET['action'])) {
	call_user_func($_GET['action'], (isset($_POST['pack_data']) ? $_POST['pack_data'] : null));
}

function get_data() {
	print DataProcess::getData();
	exit;
}

function save_data($data) {
	if (DataProcess::saveData($data)) {
		print 'With Success data unpack and saved!';
		exit;
	}
	print "Data can't unpack save!";
	exit;
}
