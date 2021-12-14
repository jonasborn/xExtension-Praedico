<?php

namespace dde;

require_once __DIR__ . "vendor/autoload.php";

class PraedicoConfig {

	static $file = __DIR__ . "config.json";
	/**
	 * @var PraedicoConfig
	 */
	static $current;

	public static function current() {
		if (is_null(self::$current)) {
			if (file_exists(self::$file)) {
				$mapper = new \JsonMapper();
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
		file_put_contents(self::$file, json_encode(self::$current));
	}

	public $storage = __DIR__ . "../../data/praedico/";

	public function prepare() {
		if (!\PradecioUtils::str_ends_with($this->storage, "/")) {
			$this->storage = $this->storage . "/";
		}
	}


}
