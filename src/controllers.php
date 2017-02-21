<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

//Request::setTrustedProxies(array('127.0.0.1'));

$app->get('/', function () use ($app) {
    return $app['twig']->render('index.html.twig', array());
})
->bind('homepage');

$data_dir = __DIR__ . '/../data/';

$conversations = [];
foreach (scandir($data_dir) as $filename) {
    if (is_file($data_dir . $filename)) {
        $new_convo = [];

        $raw_file = file_get_contents($data_dir . $filename);
        
        list($convo_num, $convo_part, $language) = explode('_', $filename);
        $new_convo['sequence'] = $convo_num;
        $new_convo['part']     = $convo_part;
        $new_convo['language'] = $language;

        $speaking = [];
        foreach (explode(PHP_EOL, $raw_file) as $cur_line) {
            list($speaker, $what_they_said) = explode('–', $cur_line);
            $speaking[] = [$speaker => $what_they_said];
        }

        $new_convo['conversation'] = $speaking;

        $conversations[] = $new_convo;
    }
}

$app->get('/api/v1/conversations', function () use ($app, $conversations) {
    return $app->json($conversations);
});

