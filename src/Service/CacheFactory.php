<?php

declare(strict_types=1);

namespace BjyAuthorize\Service;

use Interop\Container\ContainerInterface;
use Laminas\Cache\StorageFactory;
use Laminas\ServiceManager\Factory\FactoryInterface;

/**
 * Factory for building the cache storage
 */
class CacheFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     *
     * @see \Laminas\ServiceManager\Factory\FactoryInterface::__invoke()
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        return StorageFactory::factory($container->get('BjyAuthorize\Config')['cache_options']);
    }
}
