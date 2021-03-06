<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link           http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright      Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license        http://framework.zend.com/license/new-bsd New BSD License
 * @package        Zend_Service
 */

namespace BjyAuthorizeTest\Service;

use \PHPUnit\Framework\TestCase;
use BjyAuthorize\Service\RoleCollectorServiceFactory;

/**
 * Test for {@see \BjyAuthorize\Service\RoleCollectorServiceFactory}
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 */
class RoleCollectorServiceFactoryTest extends TestCase
{
    /**
     * @covers \BjyAuthorize\Service\RoleCollectorServiceFactory::createService
     */
    public function testCreateService()
    {
        $factory          = new RoleCollectorServiceFactory();
        $serviceLocator   = $this->createMock('Laminas\\ServiceManager\\ServiceLocatorInterface');
        $identityProvider = $this->createMock('BjyAuthorize\\Provider\\Identity\\ProviderInterface');

        $serviceLocator
            ->expects($this->any())
            ->method('get')
            ->with('BjyAuthorize\Provider\Identity\ProviderInterface')
            ->will($this->returnValue($identityProvider));

        $collector = $factory->createService($serviceLocator);

        $this->assertInstanceOf('BjyAuthorize\\Collector\\RoleCollector', $collector);
    }
}
