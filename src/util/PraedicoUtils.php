<?php


class PraedicoUtils {

	static $replacements = [
		'/<a.*<\/a>/m',
		'/<img.*<\/img>/m'
	];

	public static function getBaseUrl($url) {
		parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
	}

	public static function str_ends_with($haystack, $needle) {
		if (! function_exists('str_ends_with')) {
			function str_ends_with(string $haystack, string $needle): bool
			{
				$needle_len = strlen($needle);
				return ($needle_len === 0 || 0 === substr_compare($haystack, $needle, - $needle_len));
			}
		} else {
			return str_ends_with($haystack, $needle);
		}
	}



	public static function clean($content) {
		foreach (self::$replacements as $replacement) {
			$content = preg_replace($replacement, "", $content);
		}
		$content = strip_tags($content);
		$content = trim($content);
		return $content;
	}

}
