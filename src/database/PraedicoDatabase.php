<?php

namespace dde;

require_once __DIR__ . "/../file/PraedicoFiles.php";

use dde\PraedicoConfig;
use SQLite3;

class PraedicoDatabase {

	static $file;
	static $storage;

	/**
	 * @return SQLite3
	 */
	public static function main():SQLite3 {
		$mainConfig = PraedicoFiles::mainDatabase();
		if (!file_exists($mainConfig)) {
			$db = new SQLite3($mainConfig);
			return $db;
		} else {
			return new SQLite3($mainConfig);
		}
	}

	/**
	 * @param $username
	 * @return SQLite3
	 */
	public static function user($username): SQLite3 {
		$userDatabase = PraedicoFiles::userDatabase($username);
		if (!file_exists($userDatabase)) {
			$db = new SQLite3($userDatabase);
			$db->query("CREATE TABLE evaluations (id TEXT PRIMARY KEY,class INTEGER, creation INTEGER);");
			return $db;
		} else {
			return new SQLite3($userDatabase);
		}
	}

}
