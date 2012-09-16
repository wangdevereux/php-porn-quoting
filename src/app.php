<?php
//bootstrap app
require_once __DIR__.'/bootstrap.php';

use Symfony\Component\HttpFoundation\Response;

$app = new Silex\Application();

$app['debug'] = true;

$app->get('/', function() use ($app){
    //return a random quote from http://www.contoerotico.com.br/categorias/heterosexual/

    $response = new Response();
    $response->headers->set('Content-Type', 'application/json');
    $response->setContent(get_a_quote(new ContoErotico()));

    return $response;
})
->bind('main');

return $app;