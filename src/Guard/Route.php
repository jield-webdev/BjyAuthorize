<?php

declare(strict_types=1);

namespace BjyAuthorize\Guard;

use BjyAuthorize\Exception\UnAuthorizedException;
use BjyAuthorize\Service\Authorize;
use Laminas\EventManager\EventManagerInterface;
use Laminas\Mvc\Application;
use Laminas\Mvc\MvcEvent;

/**
 * Route Guard listener, allows checking of permissions
 * during {@see \Laminas\Mvc\MvcEvent::EVENT_ROUTE}
 */
class Route extends AbstractGuard
{
    /**
     * Marker for invalid route errors
     */
    public const string ERROR = 'error-unauthorized-route';

    /**
     * @return string[]
     */
    protected function extractResourcesFromRule(array $rule): array
    {
        return ['route/' . $rule['route']];
    }

    /**
     * {@inheritDoc}
     */
    public function attach(EventManagerInterface $events, $priority = 1): void
    {
        $this->listeners[] = $events->attach(eventName: MvcEvent::EVENT_ROUTE, listener: $this->onRoute(...), priority: -1000);
    }

    /**
     * Event callback to be triggered on dispatch, causes application error triggering
     * in case of failed authorization check
     */
    public function onRoute(MvcEvent $event)
    {
        /** @var Authorize $service */
        $service   = $this->container->get(Authorize::class);
        $match     = $event->getRouteMatch();
        $routeName = $match->getMatchedRouteName();

        //Bypass this function for CLI processes
        if (PHP_SAPI === 'cli') {
            return;
        }

        $event->setError(message: static::ERROR);
        $event->setParam(name: 'route', value: $routeName);
        $event->setParam(name: 'identity', value: $service->getIdentity());
        $event->setParam(
            name: 'exception',
            value: new UnAuthorizedException(message: 'You are not authorized to access ' . $routeName)
        );

        /** @var Application $app */
        $app          = $event->getTarget();
        $eventManager = $app->getEventManager();

        $event->setName(name: MvcEvent::EVENT_DISPATCH_ERROR);
        $results = $eventManager->triggerEvent(event: $event);

        $return = $results->last();
        if (!$return) {
            return $event->getResult();
        }

        return $return;
    }
}
