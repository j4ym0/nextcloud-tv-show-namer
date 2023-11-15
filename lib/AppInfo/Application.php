<?php

namespace OCA\TVShowNamer\AppInfo;

use OCP\AppFramework\App;

class Application extends App {
	public const APP_ID = 'tvshownamer';
	public const TMDB_API_KEY = 'ZXlKaGJHY2lPaUpJVXpJMU5pSjkuZXlKaGRXUWlPaUk0WkRjME0yTXlZalZtWVRnek9EazJPR0V3TUdOaVlqTmtNMlJpWmpCaU15SXNJbk4xWWlJNklqWXdaRFl6TldFMk0yRTVPVE0zTURBNE1ERmxOV1ZpTkNJc0luTmpiM0JsY3lJNld5SmhjR2xmY21WaFpDSmRMQ0oyWlhKemFXOXVJam94ZlEud1Y0dS14WWxqNFNBLXQxWU9oNF84RHVZN2ItTlAxT2JrVV94S1BUTm9rZw';
	public const TVDB_API_KEY = 'ZDAwYWYxY2MtNDhlMS00M2YzLThkNWItOTcwMDE4NjBiNGRl';

	public function __construct() {
		parent::__construct(self::APP_ID);
	}

	public static function get_tmdb_api_key(){
		$CHAR_LIST = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/";
		$input = self::TMDB_API_KEY;
		$inputLength = strlen($input);
		$output = "";
		for ($i = 0; $i < $inputLength; $i += 4) {
			$blockValue = 0;
			for ($j = 0; $j < 4; $j++) {
				$char = $input[$i + $j];
				$charValue = strpos($CHAR_LIST, $char);
				$blockValue = ($blockValue << 6) | $charValue;
			}
			for ($j = 0; $j < 3; $j++) {
				$shift = (2 - $j) * 8;
				$output .= chr(($blockValue >> $shift) & 0xFF);
			}
		}
		return $output;
	}
	public static function get_tvdb_api_key(){
		$CHAR_LIST = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/";
		$input = self::TVDB_API_KEY;
		$inputLength = strlen($input);
		$output = "";
		for ($i = 0; $i < $inputLength; $i += 4) {
			$blockValue = 0;
			for ($j = 0; $j < 4; $j++) {
				$char = $input[$i + $j];
				$charValue = strpos($CHAR_LIST, $char);
				$blockValue = ($blockValue << 6) | $charValue;
			}
			for ($j = 0; $j < 3; $j++) {
				$shift = (2 - $j) * 8;
				$output .= chr(($blockValue >> $shift) & 0xFF);
			}
		}
		return $output;
	}
}
