<?php

use Slim\Http\Request;
use Slim\Http\Response;

// Routes

$app->get('/ahoj', function (Request $request, Response $response, array $args){
    $tplVars = [
      'pokus' => 'Ivo'];

    return $this->view->render($response, 'index.latte', $tplVars);
});

$app->get('/', function (Request $request, Response $response, array $args){
    $stmt = $this->db->query('select * from users');
    print_r($stmt->fetchAll());
    exit;
});

//$app->get('/[{name}]', function (Request $request, Response $response, array $args) {
// Sample log message
//   $this->logger->info("Slim-Skeleton '/' route");
// Render index view
// return $this->renderer->render($response, 'index.phtml', $args);
//});
