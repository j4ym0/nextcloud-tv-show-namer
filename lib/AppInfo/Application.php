<?php

namespace OCA\TVShowNamer\AppInfo;

use OCP\AppFramework\App;

class Application extends App {
	public const APP_ID = 'tvshownamer';

	public function __construct() {
		parent::__construct(self::APP_ID);
	}
}
