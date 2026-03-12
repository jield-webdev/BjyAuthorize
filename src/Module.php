<?php

declare(strict_types=1);

namespace BjyAuthorize;

use BjyAuthorize\Guard\AbstractGuard;
use BjyAuthorize\View\UnauthorizedStrategy;
use Laminas\EventManager\EventInterface;
use Laminas\ModuleManager\Feature\BootstrapListenerInterface;
use Laminas\ModuleManager\Feature\ConfigProviderInterface;
use Laminas\ModuleManager\Feature\DependencyIndicatorInterface;
use Laminas\Mvc\ApplicationInterface;
use Laminas\ServiceManager\ServiceManager;

/**
 * BjyAuthorize Module
 */
class Module implements
    BootstrapListenerInterface,
    ConfigProviderInterface,
    DependencyIndicatorInterface
{
    public function onBootstrap(EventInterface $e): void
    {
        /** @var ApplicationInterface $app */
        $app = $e->getTarget();
        /** @var ServiceManager $serviceManager */
        $serviceManager = $app->getServiceManager();
        $config         = $serviceManager->get(name: 'BjyAuthorize\Config');
        /** @var UnauthorizedStrategy $strategy */
        $strategy = $serviceManager->get(name: $config['unauthorized_strategy']);
        /** @var AbstractGuard[] $guards */
        $guards = $serviceManager->get(name: 'BjyAuthorize\Guards');

        foreach ($guards as $guard) {
            $guard->attach(events: $app->getEventManager());
        }

        $strategy->attach(events: $app->getEventManager());
    }

    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    public function getModuleDependencies(): array
    {
        return [
            'Laminas\Cache',
        ];
    }
}
