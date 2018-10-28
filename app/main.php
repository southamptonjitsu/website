<?php

$app = new \Slim\App();

$app->get('/', function ($request, $response) {
    $parsedown = new Parsedown();
    return $response->write(
        $parsedown->text(file_get_contents(__DIR__ . '/../resources/pages/home/home.md'))
    );
});

$app->run();
