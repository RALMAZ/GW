<?php
use Templates\Template;
use Routers\Router;

// List routes


// Example
Router::get('/', function() {
	// Init PDO
	$db = DB::getInstance();

	// Init template engine
	$tpl = new Template("../tpl/test.html");
	$tpl->TEST = 'Name';
	$tpl->show();
});




// 404 & end routes
Router::error(function() {
    $tpl = new Template("../tpl/404.html");
    $tpl->show();
});

Router::dispatch();
?>