<?php
    //Load Slim App class
    require '../vendor/autoload.php';
    
    $slimSettings = require 'settings.php';
    $config = require 'config.php';
    $app = new \Slim\App($slimSettings);

    $container = $app->getContainer();

    $container['view'] = function ($container) {
        $view = new \Slim\Views\Twig('../templates', [
            'cache' => false
        ]);
        
        $basePath = rtrim(str_ireplace('index.php', '', $container['request']->getUri()->getBasePath()), '/');
        $view->addExtension(new Slim\Views\TwigExtension($container['router'], $basePath));

        return $view;
    };

    require 'dbconnection.php';
    require 'dependencies.php';
    require './routes/routes.php';

    $app->run();
?>