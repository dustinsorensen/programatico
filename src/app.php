<?php

use Silex\Application;
use Silex\Provider\TwigServiceProvider;


$app = new Application();

$app->register(new TwigServiceProvider(), array(
	'twig.path' => __DIR__.'/../templates',
));

$app['twig'] = $app->extend('twig', function ($twig, $app) {
    // add custom globals, filters, tags, ...

    return $twig;
});

return $app;