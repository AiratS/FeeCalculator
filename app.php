#!/usr/bin/env php
<?php

declare(strict_types=1);

require './vendor/autoload.php';

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use App\Core\Application;

try {
    $containerBuilder = new ContainerBuilder();
    $loader = new YamlFileLoader($containerBuilder, new FileLocator(__DIR__));
    $loader->load('config/services.yaml');

    $containerBuilder->compile();
    $app = $containerBuilder->get(Application::class);
    $app->run();
} catch (Exception $exception) {
    echo $exception->getMessage();
}
