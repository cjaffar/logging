<?php

use DI\ContainerBuilder;
use Slim\App;

require_once dirname(__DIR__) . '/vendor/autoload.php';

$containerBuilder = new ContainerBuilder();

// Set up settings
$containerBuilder->addDefinitions( dirname(__DIR__) . '/config/container.php');

// Build PHP-DI Container instance
$container = $containerBuilder->build();

// Create App instance
$app = $container->get(App::class);

// Register routes
(require dirname(__DIR__) . '/config/routes.php')($app);

// Register middleware
(require dirname(__DIR__) . '/config/middleware.php')($app);

return $app;