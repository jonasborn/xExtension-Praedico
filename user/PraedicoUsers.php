<?php


namespace dde;


use Minz_Session;

class PraedicoUsers {

	static function get($username) {
		$config = PraedicoConfig::current();
		$current_user = Minz_Session::param("currentUser", '');
		if (empty($current_user)) return null;
		$file = $config->storage . $current_user;
		if (file_exists($file)) {
			$content = json_decode(file_get_contents($file));
			$mapper = new \JsonMapper();
			return $mapper->map($content, new PraedicoUser());
		} else {
			$user = new PraedicoUser();
			$user->name = $username;
			file_put_contents($file, json_encode($user));
			return $user;
		}
	}

}
