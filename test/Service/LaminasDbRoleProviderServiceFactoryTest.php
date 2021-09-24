<?php

declare(strict_types=1);

namespace BjyAuthorizeTest\Service;

use BjyAuthorize\Provider\Role\LaminasDb;
use BjyAuthorize\Service\LaminasDbRoleProviderServiceFactory;
use Interop\Container\ContainerInterface;
use PHPUnit\Framework\TestCase;

/**
 * Test for {@see \BjyAuthorize\Service\LaminasDbRoleProviderServiceFactory}
 */
class LaminasDbRoleProviderServiceFactoryTest extends TestCase
{
    /**
     * @covers \BjyAuthorize\Service\LaminasDbRoleProviderServiceFactory::__invoke
     */
    public function testInvoke()
    {
        $factory   = new LaminasDbRoleProviderServiceFactory();
        $container = $this->createMock(ContainerInterface::class);
        $config    = [
            'role_providers' => [
                LaminasDb::class => [],
            ],
        ];

        $container
            ->expects($this->any())
            ->method('get')
            ->with('BjyAuthorize\Config')
            ->will($this->returnValue($config));

        $guard = $factory($container, LaminasDbRoleProviderServiceFactory::class);

        $this->assertInstanceOf(LaminasDb::class, $guard);
    }
}
