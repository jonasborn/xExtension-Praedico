<?php

namespace dde;

require_once __DIR__ . "/../config/PraedicoConfig.php";

use Minz_Session;

class PraedicoUsers {

	static $file;

	public function prepare() {
		if (is_null(self::$file)) {
			$file = PraedicoConfig::current()->mainStorage . "users.csv";
		}
	}

	public function listUsers() {
		$handle = fopen(self::$file, "r");
		$users = [];
		while (($data = fgetcsv($handle)) !== FALSE) {
			$user = new PraedicoUser();
			$user->name = $data[0];
			array_push($users, $user);
		}
		fclose($handle);
	}

	public function addUser() {
		$handle = fopen(self::$file, "w");
	}

	//TODO ADD USER TO MAIN DB
	static function get($username) {
		$config = PraedicoConfig::current();
		$current_user = Minz_Session::param("currentUser", '');
		if (empty($current_user)) return null;
		$file = $config->mainStorage . $current_user;
		if (file_exists($file)) {
			$content = json_decode(file_get_contents($file));
			$mapper = new \JsonMapper();
			return $mapper->map($content, new PraedicoUser());
		} else {
			$user = new PraedicoUser();
			$user->name = $username;
			return $user;
		}
	}

}
