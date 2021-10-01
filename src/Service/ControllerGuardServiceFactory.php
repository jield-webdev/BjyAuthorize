<?php

declare(strict_types=1);

namespace BjyAuthorize\Service;

use BjyAuthorize\Guard\Controller;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

/**
 * Factory responsible of instantiating {@see \BjyAuthorize\Guard\Controller}
 */
class ControllerGuardServiceFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     *
     * @see \Laminas\ServiceManager\Factory\FactoryInterface::__invoke()
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        return new Controller(
            $container->get('BjyAuthorize\Config')['guards'][Controller::class],
            $container
        );
    }
}
