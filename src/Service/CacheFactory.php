<?php

declare(strict_types=1);

namespace BjyAuthorize\Service;

use Interop\Container\ContainerInterface;
use Laminas\Cache\Service\StorageAdapterFactoryInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use function is_array;

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
        /** @var StorageAdapterFactoryInterface $storageFactory */
        $storageFactory = $container->get(StorageAdapterFactoryInterface::class);

        $cacheOptions = $container->get('BjyAuthorize\Config')['cache_options'];

        $plugins = [];
        foreach ($cacheOptions['plugins'] as $plugin) {
            if (is_array($plugin)) {
                $plugins[] = $plugin;
            } else {
                $plugins[] = [
                    'name' => $plugin,
                ];
            }
        }

        return $storageFactory->create(
            $cacheOptions['adapter']['name'],
            $cacheOptions['options'] ?? [],
            $plugins
        );
    }
}
