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
    /**
     * {@inheritDoc}
     */
    public function onBootstrap(EventInterface $event): void
    {
        /** @var ApplicationInterface $app */
        $app = $event->getTarget();
        /** @var ServiceManager $serviceManager */
        $serviceManager = $app->getServiceManager();
        $config         = $serviceManager->get(name: 'BjyAuthorize\Config');
        /** @var UnauthorizedStrategy $strategy */
        $strategy = $serviceManager->get(name: $config['unauthorized_strategy']);
        /** @var AbstractGuard[] $guards */
        $guards = $serviceManager->get(name: 'BjyAuthorize\Guards');

        // TODO remove in 3.0.0, fix alias
        if ($serviceManager instanceof ServiceManager && $serviceManager->has(name: 'lmcuser_user_service') === false) {
            $serviceManager->setAllowOverride(flag: true);
            $serviceManager->setAlias(alias: 'lmcuser_user_service', target: 'zfcuser_user_service');
            $serviceManager->setAllowOverride(flag: false);
        }

        foreach ($guards as $guard) {
            $guard->attach(events: $app->getEventManager());
        }

        $strategy->attach(events: $app->getEventManager());
    }

    /**
     * {@inheritDoc}
     */
    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    /**
     * {@inheritDoc}
     */
    public function getModuleDependencies(): array
    {
        return [
            'Laminas\Cache',
        ];
    }
}
