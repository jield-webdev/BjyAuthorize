<?php

declare(strict_types=1);

namespace BjyAuthorize\Service;

use BjyAuthorize\Exception\InvalidArgumentException;
use BjyAuthorize\Provider\Role\ObjectRepositoryProvider;
use Doctrine\Persistence\ObjectManager;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

/**
 * Factory responsible of instantiating {@see \BjyAuthorize\Provider\Role\ObjectRepositoryProvider}
 */
class ObjectRepositoryRoleProviderFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): ObjectRepositoryProvider
    {
        $config = $container->get('BjyAuthorize\Config');

        if (!isset($config['role_providers'][ObjectRepositoryProvider::class])) {
            throw new InvalidArgumentException(
                message: 'Config for "BjyAuthorize\Provider\Role\ObjectRepositoryProvider" not set'
            );
        }

        $providerConfig = $config['role_providers'][ObjectRepositoryProvider::class];

        if (!isset($providerConfig['role_entity_class'])) {
            throw new InvalidArgumentException(message: 'role_entity_class not set in the bjyauthorize role_providers config.');
        }

        if (!isset($providerConfig['object_manager'])) {
            throw new InvalidArgumentException(message: 'object_manager not set in the bjyauthorize role_providers config.');
        }

        /** @var ObjectManager $objectManager */
        $objectManager = $container->get($providerConfig['object_manager']);

        return new ObjectRepositoryProvider(objectRepository: $objectManager->getRepository($providerConfig['role_entity_class']));
    }
}
