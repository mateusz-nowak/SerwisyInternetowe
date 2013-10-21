<?php

use SiBundle\Bootstrap;
use SiBundle\DependencyContainer;

include_once '../vendor/autoload.php';

$app = new Bootstrap();
$container = new DependencyContainer();

$app->get('/', function() use ($container) {
    $template = $container->get('template');

    return $template->render('src/BlogBundle/views/main/index.html', array(
        'title' => 'Hello world!'
    ));
});

$app->run();
