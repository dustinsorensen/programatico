<?php
require_once __DIR__.'/../vendor/autoload.php';

$app = new Silex\Application();

$app->get('/test', function () {
    return 'test';
});

$app->run();