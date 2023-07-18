<?php
/**
 * Load Javascript
 */
use OCP\Util;
$eventDispatcher = \OC::$server->getEventDispatcher();
$eventDispatcher->addListener('OCA\Files::loadAdditionalScripts', function(){
    Util::addScript('tvshownamer', 'rightclick' );
    Util::addStyle('tvshownamer', 'rightclick' );
});