<?php


class PradecioUtils {

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

}
