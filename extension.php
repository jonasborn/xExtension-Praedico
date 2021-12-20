<?php

require_once __DIR__ . "/src/user/PraedicoUser.php";
require_once __DIR__ . "/src/user/PraedicoUsers.php";

use dde\PraedicoUser;
use dde\PraedicoUsers;

class HelloWorldExtension extends Minz_Extension {

	/**
	 * @var PraedicoUser
	 */
	private static $user;

	public function init() {

		$username = Minz_Session::param('currentUser', '_');
		self::$user = PraedicoUsers::get($username);

		Minz_View::appendStyle($this->getFileUrl('style.css', 'css'));
		Minz_View::appendScript($this->getFileUrl('script.js', 'js'));

		$this->registerTranslates();

		$this->registerController('praedico');
		$this->registerViews();

		$this->registerHook('entry_before_display', array('HelloWorldExtension', 'before'));
		$this->registerHook('entry_before_insert', array("HelloWorldExtension", "insert"));
	}

	public function handleConfigureAction() {
		$this->registerTranslates();
	}

	public static function setHelloWorldContentHook($entry) {
		$entry->_content('Hello world!');
		return $entry;
	}

	/**
	 * @param $entry FreshRSS_Entry
	 * @return mixed
	 */
	public static function before($entry) {
		$result = self::$user->predict($entry);

		$base = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http")
			. "://$_SERVER[HTTP_HOST]" . strtok($_SERVER["REQUEST_URI"],'?');

		$links = [];
		$props = ["1" => 0, "2" => 0, "3" => 0, "4" => 0, "5" => 0];
		$label = "0";
		if (!is_null($result)) {
			$props = $result["probability"];
			$label = $result["label"];
		}

		//if ($label < 2) return null;

		$values = [];


		for ($i = 1; $i <= 5; $i++) {
			if (!isset($props[$i])) $props[$i] = 0;
			$links[$i] = $base . "?c=praedico&a=evaluate&evaluation=$i&id=" . $entry->id();
			$values[$i] =  number_format(round($props[$i], 2), 2, '.', '');
		}

		$overview = "";
		$header = "<pre>Praedico\n";

		for ($i = 1; $i <= 5; $i++) {
			$overview = $overview . $values[$i] . "|";
			$header  = $header . '<a href="' . $links[$i] . '">' . $values[$i] . "</a>|";
		}
		$header = substr($header, 0,  -1);

		$entry->_title( " (" . $label . "/5) " . $entry->title());
		$entry->_content( $header . "</pre><br><br>". $entry->content());
		return $entry;
	}

	/**
	 * @param $entry FreshRSS_Entry
	 */
	public static function insert($entry) {
		$link = PraedicoUtils::extractLink($entry->content());
		if (is_null($link)) {
			return $entry;
		} else {
			$extracted = PraedicoUtils::extractContent($link);
			$entry->_content($extracted["content"]);
			return $entry;
		}
	}

	public static function noMoreFeedsHook($feed) {
		$feedDAO = FreshRSS_Factory::createFeedDao();
		$feeds = $feedDAO->listFeeds();

		if (count($feeds) >= 10) {
			return null;
		} else {
			return $feed;
		}
	}
}
