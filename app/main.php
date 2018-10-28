<?php

$app = new \Slim\App();

$app->get('/', function ($request, $response) {
    return $response->write('Hello world');
});

$app->run();
