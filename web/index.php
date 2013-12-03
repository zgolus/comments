<?php

require_once __DIR__.'/../vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Commentsystem\Post;
use Monolog\Logger;

$app = new Silex\Application();

$app['debug'] = true;

$app->before(function (Request $request) {
    if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
        $data = json_decode($request->getContent(), true);
        $request->request->replace(is_array($data) ? $data : array());
    }
});

$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
    'db.options' => array(
        'driver'   => 'pdo_sqlite',
        'path'     => __DIR__.'/../app.db',
    ),
));

$app->register(new Silex\Provider\MonologServiceProvider(), array(
    'monolog.logfile' => __DIR__.'/../error.log',
    'monolog.level' => Logger::WARNING
));

$app->get('/posts', function () use ($app) {
    $posts = $app['db']->fetchAll('SELECT * FROM posts');
    return $app->json($posts);
});

$app->post('/posts', function (Request $request) use ($app) {
    $post = new Post();
    $post->setContainer($app);
    $post->name = $request->get('name');
    $post->email = $request->get('email');
    $post->post = $request->get('post');
    if ($post->save()) {
        return $app->json($post, 201);
    } else {
        return $app->json(null, 500);
    }
});

$app->put('/posts/{id}', function (Request $request, $id) use ($app) {
    $post = new Post();
    $post->setContainer($app);
    $post->id = (int)$id;
    $post->name = $request->get('name');
    $post->email = $request->get('email');
    $post->post = $request->get('post');
    if ($post->save()) {
        return $app->json($post, 200);
    } else {
        return $app->json(null, 500);
    }
});

$app->delete('/posts/{id}', function ($id) use ($app) {
    $post = new Post();
    $post->setContainer($app);
    $post->id = (int)$id;
    if ($post->delete()) {
        return $app->json(null, 200);
    } else {
        return $app->json(null, 500);
    }
});

$app->run();
