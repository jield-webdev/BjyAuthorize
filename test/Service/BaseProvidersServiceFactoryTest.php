<?php

declare(strict_types=1);

namespace BjyAuthorizeTest\Service;

use BjyAuthorize\Provider\Resource\ProviderInterface;
use BjyAuthorize\Service\BaseProvidersServiceFactory;
use BjyAuthorizeTest\Service\MockProvider;
use Interop\Container\ContainerInterface;
use PHPUnit\Framework\TestCase;

use function array_filter;
use function array_shift;
use function in_array;

/**
 * Test for {@see \BjyAuthorize\Service\ResourceProvidersServiceFactory}
 */
class BaseProvidersServiceFactoryTest extends TestCase
{
    /**
     * @covers \BjyAuthorize\Service\BaseProvidersServiceFactory::__invoke
     */
    public function testInvoke()
    {
        $factory   = $this->getMockForAbstractClass(BaseProvidersServiceFactory::class);
        $container = $this->createMock(ContainerInterface::class);
        $foo       = $this->createMock(ProviderInterface::class);
        $bar       = $this->createMock(ProviderInterface::class);
        $config    = [
            'providers' => [
                'foo'                            => [],
                'bar'                            => [],
                __NAMESPACE__ . '\\MockProvider' => ['option' => 'value'],
            ],
        ];

        $container
            ->expects($this->any())
            ->method('has')
            ->will(
                $this->returnCallback(
                    function ($serviceName) {
                        return in_array($serviceName, ['foo', 'bar'], true);
                    }
                )
            );

        $container
            ->expects($this->any())
            ->method('get')
            ->with($this->logicalOr('BjyAuthorize\\Config', 'foo', 'bar'))
            ->will(
                $this->returnCallback(
                    function ($serviceName) use ($foo, $bar, $config) {
                        if ('BjyAuthorize\\Config' === $serviceName) {
                            return $config;
                        }

                        if ('foo' === $serviceName) {
                            return $foo;
                        }

                        return $bar;
                    }
                )
            );

        $providers = $factory($container, BaseProvidersServiceFactory::class);

        $this->assertCount(3, $providers);
        $this->assertContains($foo, $providers);
        $this->assertContains($bar, $providers);

        $invokableProvider = array_filter(
            $providers,
            function ($item) {
                return $item instanceof MockProvider;
            }
        );

        $this->assertCount(1, $invokableProvider);

        /** @var MockProvider $invokableProvider */
        $invokableProvider = array_shift($invokableProvider);

        $this->assertInstanceOf(__NAMESPACE__ . '\\MockProvider', $invokableProvider);

        $this->assertSame(['option' => 'value'], $invokableProvider->options);
        $this->assertSame($container, $invokableProvider->container);
    }
}
