<?php

namespace BjyAuthorizeTest;

use BjyAuthorize\Guard\Controller;
use BjyAuthorize\Guard\Route;
use BjyAuthorize\Module;
use BjyAuthorize\View\UnauthorizedStrategy;
use Laminas\EventManager\EventManager;
use Laminas\Mvc\Application;
use Laminas\Mvc\MvcEvent;
use Laminas\ServiceManager\ServiceManager;
use PHPUnit\Framework\TestCase;

class ModuleTest extends TestCase
{
    public function testIfModuleConfigIsLoaded()
    {
        $module = new Module();
        $config = $module->getConfig();

        $this->assertIsArray($config);
        $this->assertArrayHasKey('bjyauthorize', $config);
        $this->assertArrayHasKey('service_manager', $config);
        $this->assertArrayHasKey('controller_plugins', $config);
        $this->assertArrayHasKey('view_manager', $config);
        $this->assertArrayHasKey('view_helpers', $config);
        $this->assertArrayHasKey('laminas-developer-tools', $config);
    }

    public function testIfBoostrapRegistersGuardsAndStrategy()
    {
        $module = new Module();
        $event = $this->getMockedBootstrapEvent();

        $module->onBootstrap($event);
    }

    protected function getMockedBootstrapEvent()
    {
        $guard1 = $this->getMockBuilder(Controller::class)
            ->disableOriginalConstructor()
            ->getMock();

        $guard1->expects($this->once())
            ->method('attach');

        $guard2 = $this->getMockBuilder(Route::class)
            ->disableOriginalConstructor()
            ->getMock();

        $guard2->expects($this->once())
            ->method('attach');

        $strategy = $this->getMockBuilder(UnauthorizedStrategy::class)
            ->disableOriginalConstructor()
            ->getMock();

        $strategy->expects($this->once())
            ->method('attach');

        $serviceManager = $this->getMockBuilder(ServiceManager::class)
            ->getMock();

        $serviceManager->expects($this->any())
            ->method('get')
            ->will($this->returnCallback(function(string $name) use ($guard1, $guard2, $strategy) {
                switch ($name) {
                    case 'BjyAuthorize\Config':
                        return ['unauthorized_strategy' => 'my_unauthorized_strategy'];
                    case 'my_unauthorized_strategy':
                        return $strategy;
                    case 'BjyAuthorize\Guards':
                        return [$guard1, $guard2];
                    default:
                        throw new \InvalidArgumentException('Invalid service call.');
                }
            }));

        $eventManager = $this->getMockBuilder(EventManager::class)
            ->getMock();

        $app = $this->getMockBuilder(Application::class)
            ->disableOriginalConstructor()
            ->getMock();

        $app->expects($this->any())
            ->method('getServiceManager')
            ->willReturn($serviceManager);

        $app->expects($this->any())
            ->method('getEventManager')
            ->willReturn($eventManager);

        $event = $this->getMockBuilder(MvcEvent::class)
            ->disableOriginalConstructor()
            ->getMock();

        $event->expects($this->any())
            ->method('getTarget')
            ->willReturn($app);

        return $event;
    }
}
