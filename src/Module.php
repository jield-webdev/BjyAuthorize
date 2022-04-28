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
    public function onBootstrap(EventInterface $event)
    {
        /** @var ApplicationInterface $app */
        $app = $event->getTarget();
        /** @var ServiceManager $serviceManager */
        $serviceManager = $app->getServiceManager();
        $config         = $serviceManager->get('BjyAuthorize\Config');
        /** @var UnauthorizedStrategy $strategy */
        $strategy = $serviceManager->get($config['unauthorized_strategy']);
        /** @var AbstractGuard[] $guards */
        $guards = $serviceManager->get('BjyAuthorize\Guards');

        // TODO remove in 3.0.0, fix alias
        if ($serviceManager instanceof ServiceManager && $serviceManager->has('lmcuser_user_service') === false) {
            $serviceManager->setAllowOverride(true);
            $serviceManager->setAlias('lmcuser_user_service', 'zfcuser_user_service');
            $serviceManager->setAllowOverride(false);
        }

        foreach ($guards as $guard) {
            $guard->attach($app->getEventManager());
        }

        $strategy->attach($app->getEventManager());
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
    public function getModuleDependencies() {
        return [
            'Laminas\Cache',
        ];
    }
}
