<?php

namespace dde;

use JsonMapper;

require_once __DIR__ . "/../../vendor/autoload.php";
require_once __DIR__ . "/../util/PraedicoUtils.php";

class PraedicoConfig {

	static $file = __DIR__ . "/../../../../data/praedico/config.json";
	/**
	 * @var PraedicoConfig
	 */
	static $current;

	public static function current(): PraedicoConfig {
		if (is_null(self::$current)) {
			if (file_exists(self::$file)) {
				$mapper = new JsonMapper();
				$json = json_decode(file_get_contents(self::$file));
				self::$current = $mapper->map($json, new PraedicoConfig());
				self::$current->prepare();
			} else {
				self::$current = new PraedicoConfig();
				self::save();
			}
		}
		return self::$current;
	}

	public static function save() {
		file_put_contents(self::$file, json_encode(self::$current, JSON_PRETTY_PRINT));
	}

	public $mainStorage = __DIR__ . "/../../../../data/praedico/";
	public $userStorage = __DIR__ . "/../../../../data/praedico/users/";

	public function prepare() {
		if (!\PraedicoUtils::str_ends_with($this->mainStorage, "/")) {
			$this->mainStorage = $this->mainStorage . "/";
		}
		if (!file_exists($this->mainStorage)) mkdir($this->mainStorage);

		if (!\PraedicoUtils::str_ends_with($this->userStorage, "/")) {
			$this->mainStorage = $this->userStorage . "/";
		}
		if (!file_exists($this->userStorage)) mkdir($this->userStorage);
	}


}
