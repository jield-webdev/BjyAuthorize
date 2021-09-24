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
    protected function extractResourcesFromRule(array $rule)
    {
        $results        = [];
        $rule['action'] = isset($rule['action']) ? (array) $rule['action'] : [null];

        foreach ((array) $rule['controller'] as $controller) {
            foreach ($rule['action'] as $action) {
                $results[] = $this->getResourceName($controller, $action);
            }
        }

        return $results;
    }

    /**
     * {@inheritDoc}
     */
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_ROUTE, [$this, 'onDispatch'], -1000);
    }

    /**
     * Retrieves the resource name for a given controller
     *
     * @param string $controller
     * @param string $action
     * @return string
     */
    public function getResourceName($controller, $action = null)
    {
        if (isset($action)) {
            return sprintf('controller/%s:%s', $controller, strtolower($action));
        }

        return sprintf('controller/%s', $controller);
    }

    /**
     * Event callback to be triggered on dispatch, causes application error triggering
     * in case of failed authorization check
     *
     * @return mixed
     */
    public function onDispatch(MvcEvent $event)
    {
        /** @var Authorize $service */
        $service    = $this->container->get(Authorize::class);
        $match      = $event->getRouteMatch();
        $controller = $match->getParam('controller');
        $action     = $match->getParam('action');
        $request    = $event->getRequest();
        $method     = $request instanceof HttpRequest ? strtolower($request->getMethod()) : null;

        $authorized = (class_exists(ConsoleRequest::class) && $event->getRequest() instanceof ConsoleRequest)
            || $service->isAllowed($this->getResourceName($controller))
            || $service->isAllowed($this->getResourceName($controller, $action))
            || ($method && $service->isAllowed($this->getResourceName($controller, $method)));

        if ($authorized) {
            return;
        }

        $event->setError(static::ERROR);
        $event->setParam('identity', $service->getIdentity());
        $event->setParam('controller', $controller);
        $event->setParam('action', $action);

        $errorMessage = sprintf("You are not authorized to access %s:%s", $controller, $action);
        $event->setParam('exception', new UnAuthorizedException($errorMessage));

        /** @var ApplicationInterface $app */
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
