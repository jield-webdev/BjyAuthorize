<?php

declare(strict_types=1);

namespace BjyAuthorize\Guard;

use BjyAuthorize\Exception\UnAuthorizedException;
use BjyAuthorize\Service\Authorize;
use Laminas\Console\Request as ConsoleRequest;
use Laminas\EventManager\EventManagerInterface;
use Laminas\Mvc\Application;
use Laminas\Mvc\MvcEvent;

use function class_exists;

/**
 * Route Guard listener, allows checking of permissions
 * during {@see \Laminas\Mvc\MvcEvent::EVENT_ROUTE}
 */
class Route extends AbstractGuard
{
    /**
     * Marker for invalid route errors
     */
    public const ERROR = 'error-unauthorized-route';

    /**
     * @return string[]
     */
    protected function extractResourcesFromRule(array $rule)
    {
        return ['route/' . $rule['route']];
    }

    /**
     * {@inheritDoc}
     */
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_ROUTE, [$this, 'onRoute'], -1000);
    }

    /**
     * Event callback to be triggered on dispatch, causes application error triggering
     * in case of failed authorization check
     *
     * @return mixed
     */
    public function onRoute(MvcEvent $event)
    {
        /** @var Authorize $service */
        $service   = $this->container->get(Authorize::class);
        $match     = $event->getRouteMatch();
        $routeName = $match->getMatchedRouteName();

        if (
            $service->isAllowed('route/' . $routeName)
            || (class_exists(ConsoleRequest::class)
            && $event->getRequest() instanceof ConsoleRequest)
        ) {
            return;
        }

        $event->setError(static::ERROR);
        $event->setParam('route', $routeName);
        $event->setParam('identity', $service->getIdentity());
        $event->setParam(
            'exception',
            new UnAuthorizedException('You are not authorized to access ' . $routeName)
        );

        /** @var Application $app */
        $app          = $event->getTarget();
        $eventManager = $app->getEventManager();

        $event->setName(MvcEvent::EVENT_DISPATCH_ERROR);
        $results = $eventManager->triggerEvent($event);

        $return = $results->last();
        if (! $return) {
            return $event->getResult();
        }

        return $return;
    }
}
