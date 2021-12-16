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

		if (is_null($result)) {
			$info = "Unable to detect, missing data";
			$detail = "?|?";
		} else {
			$pos = is_nan($result["probability"]["1"]) ? 0 : $result["probability"]["1"]* 100;
			$neg = is_nan($result["probability"]["0"]) ? 0 : $result["probability"]["0"] * 100;
			$detail = "" . round($pos, 2) . "%|" . round($neg, 2) . "%";

			if ($result["label"] == 0) {
				$info = "Detected as uninteresting (" . $detail . ")";
			} else if ($result["label"] == 1) {
				$info = "Detected as interesting (" . $detail. ")";
			}
		}



		$base = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http")
			. "://$_SERVER[HTTP_HOST]" . strtok($_SERVER["REQUEST_URI"],'?');

		$pos = $base . "?c=praedico&a=evaluate&evaluation=pos&id=" . $entry->id();
		$neg = $base . "?c=praedico&a=evaluate&evaluation=neg&id=" . $entry->id();

		$header = "<p>$info</p>";
		$header = $header . "<a href='$pos'>Mark this article as interesting</a><br>";
		$header = $header . "<a href='$neg'>Mark this article as uninteresting</a><br><br>";
		$entry->_title($entry->title() . " (" . $detail . ")");
		$entry->_content($header . $entry->content());
		return $entry;
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
