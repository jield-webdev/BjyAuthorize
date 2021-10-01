<?php

declare(strict_types=1);

namespace BjyAuthorizeTest\Service;

use BjyAuthorize\Service\Authorize;
use BjyAuthorize\Service\AuthorizeFactory;
use Laminas\ServiceManager\ServiceManager;
use PHPUnit\Framework\TestCase;

/**
 * Test for {@see \BjyAuthorize\Service\AuthorizeFactory}
 */
class AuthorizeFactoryTest extends TestCase
{
    /**
     * @covers \BjyAuthorize\Service\AuthorizeFactory::__invoke
     */
    public function testInvokeSetCacheOptionsIfCacheIsEnabledAndAdapterOptionsAreProvided()
    {
        $serviceManager = new ServiceManager();
        $serviceManager->setService('BjyAuthorize\Config', ['cache_key' => 'bjyauthorize_acl']);

        $authorizeFactory = new AuthorizeFactory();

        $authorize = $authorizeFactory($serviceManager, AuthorizeFactory::class);

        $this->assertInstanceOf(Authorize::class, $authorize);
    }
}
