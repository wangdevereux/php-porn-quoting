<?php
//bootstrap app
require_once __DIR__.'/bootstrap.php';

$app = new Silex\Application();

$app['debug'] = true;

$app->get('/', function() use ($app){
    //return the quote here
})
->bind('main');

return $app;