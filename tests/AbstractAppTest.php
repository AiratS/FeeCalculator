<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

abstract class AbstractAppTest extends TestCase
{
    protected static ContainerBuilder $container;

    public static function setUpBeforeClass(): void
    {
        self::setContainer();
    }

    protected static function setContainer()
    {
        self::$container = new ContainerBuilder();
        $loader = new YamlFileLoader(self::$container, new FileLocator(__DIR__));
        $loader->load('../config/services.yaml');
    }

    protected static function getContainer(): ContainerBuilder
    {
        return self::$container;
    }
}
