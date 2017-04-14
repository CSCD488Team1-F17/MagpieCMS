<?php
    //Load Slim App class
    require '../vendor/autoload.php';
    
    $settings = require '../src/settings.php';
    $app = new \Slim\App($settings);

    $container = $app->getContainer();

    $container['view'] = function ($container) {
        $view = new \Slim\Views\Twig('../templates', [
            'cache' => false
        ]);
        
        $basePath = rtrim(str_ireplace('index.php', '', $container['request']->getUri()->getBasePath()), '/');
        $view->addExtension(new Slim\Views\TwigExtension($container['router'], $basePath));

        return $view;
    };

    require '../src/dbconnection.php';
    require '../src/dependencies.php';
    require '../src/routes.php';
	
	require '../phpqrcode/qrlib.php';

    $app->run();
?>