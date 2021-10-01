<?php

declare(strict_types=1);

namespace BjyAuthorizeTest\Service;

use BjyAuthorize\Provider\Role\Config;
use BjyAuthorize\Service\ConfigRoleProviderServiceFactory;
use Interop\Container\ContainerInterface;
use PHPUnit\Framework\TestCase;

/**
 * Test for {@see \BjyAuthorize\Service\ConfigRoleProviderServiceFactory}
 */
class ConfigRoleProviderServiceFactoryTest extends TestCase
{
    /**
     * @covers \BjyAuthorize\Service\ConfigRoleProviderServiceFactory::__invoke
     */
    public function testInvoke()
    {
        $factory   = new ConfigRoleProviderServiceFactory();
        $container = $this->createMock(ContainerInterface::class);
        $config    = [
            'role_providers' => [
                Config::class => [],
            ],
        ];

        $container
            ->expects($this->any())
            ->method('get')
            ->with('BjyAuthorize\Config')
            ->will($this->returnValue($config));

        $guard = $factory($container, ConfigRoleProviderServiceFactory::class);

        $this->assertInstanceOf(Config::class, $guard);
    }
}
