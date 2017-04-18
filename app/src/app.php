<?php
    //Load Slim App class
    require '../vendor/autoload.php';
    
    //Set up our settings variables
    $slimSettings = require 'settings.php';
    $config = require 'config.php';
    
    //Define our app
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

    //Load stuff
    require 'dbconnection.php';
    require 'dependencies.php';
    require 'routes/routes.php';

    //Run the app
    $app->run();
?>