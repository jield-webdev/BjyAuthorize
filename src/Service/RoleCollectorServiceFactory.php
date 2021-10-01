<?php

declare(strict_types=1);

namespace BjyAuthorize\Service;

use BjyAuthorize\Collector\RoleCollector;
use BjyAuthorize\Provider\Identity\ProviderInterface;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

/**
 * Factory responsible of instantiating {@see \BjyAuthorize\Collector\RoleCollector}
 */
class RoleCollectorServiceFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     *
     * @see \Laminas\ServiceManager\Factory\FactoryInterface::__invoke()
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        /** @var ProviderInterface $identityProvider */
        $identityProvider = $container->get(ProviderInterface::class);

        return new RoleCollector($identityProvider);
    }
}
