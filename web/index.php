<?php

require_once __DIR__.'/../vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Commentsystem\Post;
use Monolog\Logger;

$app = new Silex\Application();

// $app['debug'] = true;

$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
    'db.options' => array(
        'driver'   => 'pdo_sqlite',
        'path'     => __DIR__.'/../app.db',
    ),
));

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/../src/Commentsystem/views',
));

$app->register(new Silex\Provider\MonologServiceProvider(), array(
    'monolog.logfile' => __DIR__.'/../error.log',
    'monolog.level' => Logger::WARNING
));

$app->get('/', function () use ($app) {
    $posts = $app['db']->fetchAll('SELECT * FROM posts');
    return $app['twig']->render('index.twig', ['posts' => $posts]);
});

$app->post('/new', function(Request $request) use ($app) {
    $post = new Post();
    $post->setContainer($app);
    $post->name = $request->get('name');
    $post->email = $request->get('email');
    $post->post = $request->get('post');
    $post->save();
    return $app->redirect('/');
});

$app->run();
