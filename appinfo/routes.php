<?php
/**
 * Create your routes in here. The name is the lowercase name of the controller
 * without the controller part, the stuff after the hash is the method.
 * e.g. page#index -> OCA\DuplicateFinderBis\Controller\PageController->index()
 *
 * The controller class has to be registered in the application.php file since
 * it's instantiated in there
 */
return [
    'routes' => [
	   ['name' => 'page#index', 'url' => '/', 'verb' => 'GET'],
       ['name' => 'page#home', 'url' => '/home', 'verb' => 'GET'],
       ['name' => 'page#image', 'url' => '/image/{img}', 'verb' => 'GET'],
       ['name' => 'page#scan', 'url' => '/scan', 'verb' => 'POST'],
       ['name' => 'page#rename', 'url' => '/rename', 'verb' => 'POST'],
       ['name' => 'page#save_setting', 'url' => '/save_setting', 'verb' => 'POST'],

    ]
];
