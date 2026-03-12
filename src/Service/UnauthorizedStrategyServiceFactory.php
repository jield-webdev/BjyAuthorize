<?php

declare(strict_types=1);

namespace BjyAuthorize\Service;

use BjyAuthorize\View\UnauthorizedStrategy;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

/**
 * Factory responsible of instantiating {@see \BjyAuthorize\View\UnauthorizedStrategy}
 */
class UnauthorizedStrategyServiceFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     *
     * @see \Laminas\ServiceManager\Factory\FactoryInterface::__invoke()
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): UnauthorizedStrategy|object
    {
        return new UnauthorizedStrategy(template: $container->get('BjyAuthorize\Config')['template']);
    }
}
