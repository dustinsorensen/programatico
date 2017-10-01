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

$app->get('/conversations/{sequence}/{part}', function ($sequence, $part) use ($app) {
  return $app['twig']->render('conversation.html.twig', array('sequence' => $sequence, 'part' => $part));
});

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
      $speaking[] = [
        'speaker' => $speaker,
        'text'    => $what_they_said
      ];
    }

    $new_convo['conversation'] = $speaking;

    $conversations[] = $new_convo;
  }
}

$app->get('/api/v1/conversations', function () use ($app, $conversations) {
  return $app->json($conversations);
});

$app->get('/api/v1/verify', function (Request $request) use ($app) {
  file_put_contents('/tmp/original', $request->get('original'));
  file_put_contents('/tmp/translated', $request->get('translated'));

  // $diff_response = shell_exec('comm -23 /tmp/original /tmp/translated');

  // $diff_response = similar_text($request->get('original'), $request->get('translated'), $percentage);

  $diff_response = levenshtein($request->get('original'), $request->get('translated'));

  return $app->json([$diff_response]);

});

