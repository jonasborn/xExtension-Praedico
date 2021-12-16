<?php

namespace dde;

require_once __DIR__ . "/../config/PraedicoConfig.php";

class PraedicoFiles {

	public static function main() {
		$config = PraedicoConfig::current();
		return $config->mainStorage;
	}

	public static function mainDatabase() {
		$mainDir = self::main();
		return $mainDir . "database.sqlite";
	}

	public static function user($username): string {
		$config = PraedicoConfig::current();
		$dir = $config->userStorage . "/$username/";
		if (!file_exists($dir)) mkdir($dir, 0777, true);
		return $dir;
	}

	public static function userDataset($username) {
		$userDir = self::user($username);
		return $userDir . "dataset.cls";
	}

	public static function userDatabase($username) {
		$userDir = self::user($username);
		return $userDir . "database.sqlite";
	}

}
