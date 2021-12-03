<?php

declare(strict_types=1);

namespace BjyAuthorizeTest\Service;

use BjyAuthorize\Collector\RoleCollector;
use BjyAuthorize\Provider\Identity\ProviderInterface;
use BjyAuthorize\Service\RoleCollectorServiceFactory;
use Interop\Container\ContainerInterface;
use PHPUnit\Framework\TestCase;

/**
 * Test for {@see \BjyAuthorize\Service\RoleCollectorServiceFactory}
 */
class RoleCollectorServiceFactoryTest extends TestCase
{
    /**
     * @covers \BjyAuthorize\Service\RoleCollectorServiceFactory::__invoke
     */
    public function testInvoke()
    {
        $factory          = new RoleCollectorServiceFactory();
        $container        = $this->createMock(ContainerInterface::class);
        $identityProvider = $this->createMock(ProviderInterface::class);

        $container
            ->expects($this->any())
            ->method('get')
            ->with(ProviderInterface::class)
            ->will($this->returnValue($identityProvider));

        $collector = $factory($container, RoleCollectorServiceFactory::class);

        $this->assertInstanceOf(RoleCollector::class, $collector);
    }
}
