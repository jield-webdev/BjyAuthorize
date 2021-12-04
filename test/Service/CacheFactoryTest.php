<?php

declare(strict_types=1);

namespace BjyAuthorizeTest\Service;

use BjyAuthorize\Service\CacheFactory;
use Interop\Container\ContainerInterface;
use Laminas\Cache\Service\StorageAdapterFactoryInterface;
use Laminas\Cache\Storage\Adapter\Memory;
use Laminas\ServiceManager\ServiceManager;
use PHPUnit\Framework\TestCase;

/**
 * PHPUnit tests for {@see \BjyAuthorize\Service\CacheFactory}
 */
class CacheFactoryTest extends TestCase
{
    /**
     * @covers \BjyAuthorize\Service\CacheFactory::__invoke
     */
    public function testInvoke()
    {
        $config    = [
            'cache_options' => [
                'adapter' => [
                    'name' => 'memory',
                ],
                'plugins' => [
                    [
                        'name' => 'serializer',
                    ]
                ],
            ],
        ];

        $container = new ServiceManager();
        $container->setService('BjyAuthorize\Config', $config);
        $container->setService(
            'Laminas\Cache\Service\StorageAdapterFactoryInterface',
            $this->getMockBuilder(StorageAdapterFactoryInterface::class)
                ->getMock()
        );

        $factory = new CacheFactory();

        $this->assertIsObject($factory($container, CacheFactory::class));
    }
}
