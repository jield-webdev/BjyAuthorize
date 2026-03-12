<?php

declare(strict_types=1);

namespace BjyAuthorize\Guard;

use BjyAuthorize\Exception\UnAuthorizedException;
use BjyAuthorize\Service\Authorize;
use Laminas\Console\Request as ConsoleRequest;
use Laminas\EventManager\EventManagerInterface;
use Laminas\Http\Request as HttpRequest;
use Laminas\Mvc\ApplicationInterface;
use Laminas\Mvc\MvcEvent;
use function class_exists;
use function sprintf;
use function strtolower;

/**
 * Controller Guard listener, allows checking of permissions
 * during {@see \Laminas\Mvc\MvcEvent::EVENT_DISPATCH}
 */
class Controller extends AbstractGuard
{
    /**
     * Marker for invalid route errors
     */
    public const ERROR = 'error-unauthorized-controller';

    /**
     * @return array
     */
    protected function extractResourcesFromRule(array $rule): array
    {
        $results        = [];
        $rule['action'] = isset($rule['action']) ? (array)$rule['action'] : [null];

        foreach ((array)$rule['controller'] as $controller) {
            foreach ($rule['action'] as $action) {
                $results[] = $this->getResourceName(controller: $controller, action: $action);
            }
        }

        return $results;
    }

    /**
     * {@inheritDoc}
     */
    public function attach(EventManagerInterface $events, $priority = 1): void
    {
        $this->listeners[] = $events->attach(eventName: MvcEvent::EVENT_ROUTE, listener: $this->onDispatch(...), priority: -1000);
    }

    /**
     * Retrieves the resource name for a given controller
     */
    public function getResourceName(string $controller, ?string $action = null): string
    {
        if (isset($action)) {
            return sprintf('controller/%s:%s', $controller, strtolower(string: $action));
        }

        return sprintf('controller/%s', $controller);
    }

    /**
     * Event callback to be triggered on dispatch, causes application error triggering
     * in case of failed authorization check
     *
     * @return mixed
     */
    public function onDispatch(MvcEvent $event): mixed
    {
        /** @var Authorize $service */
        $service    = $this->container->get(Authorize::class);
        $match      = $event->getRouteMatch();
        $controller = $match->getParam(name: 'controller');
        $action     = $match->getParam(name: 'action');
        $request    = $event->getRequest();
        $method     = $request instanceof HttpRequest ? strtolower(string: (string)$request->getMethod()) : null;

        $authorized = (class_exists(class: ConsoleRequest::class) && $event->getRequest() instanceof ConsoleRequest)
            || $service->isAllowed(resource: $this->getResourceName(controller: $controller))
            || $service->isAllowed(resource: $this->getResourceName(controller: $controller, action: $action))
            || ($method && $service->isAllowed(resource: $this->getResourceName(controller: $controller, action: $method)));

        if ($authorized) {
            return;
        }

        $event->setError(message: static::ERROR);
        $event->setParam(name: 'identity', value: $service->getIdentity());
        $event->setParam(name: 'controller', value: $controller);
        $event->setParam(name: 'action', value: $action);

        $errorMessage = sprintf("You are not authorized to access %s:%s", $controller, $action);
        $event->setParam(name: 'exception', value: new UnAuthorizedException(message: $errorMessage));

        /** @var ApplicationInterface $app */
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
