<?php

declare(strict_types=1);

namespace BjyAuthorize\Service;

use BjyAuthorize\Provider\Identity\LmcUserLaminasDb;
use Interop\Container\ContainerInterface;
use Laminas\Db\TableGateway\TableGateway;
use Laminas\ServiceManager\Factory\FactoryInterface;
use LmcUser\Service\User;

/**
 * Factory responsible of instantiating {@see \BjyAuthorize\Provider\Identity\LmcUserLaminasDb}
 */
class LmcUserLaminasDbIdentityProviderServiceFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     *
     * @see \Laminas\ServiceManager\Factory\FactoryInterface::__invoke()
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        /** @var TableGateway $tableGateway */
        $tableGateway = new TableGateway('user_role_linker', $container->get('lmcuser_laminas_db_adapter'));
        /** @var User $userService */
        $userService = $container->get('lmcuser_user_service');
        $config      = $container->get('BjyAuthorize\Config');

        $provider = new LmcUserLaminasDb($tableGateway, $userService);

        $provider->setDefaultRole($config['default_role']);

        return $provider;
    }
}
