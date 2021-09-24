<?php

declare(strict_types=1);

namespace BjyAuthorize\View\Helper;

use BjyAuthorize\Service\Authorize;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\AbstractPluginManager;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class IsAllowedFactory implements FactoryInterface
{
    /**
     * @param ServiceLocatorInterface|AbstractPluginManager $serviceLocator
     * @return IsAllowed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator->getServiceLocator(), IsAllowed::class);
    }

    /**
     * @param string $requestedName
     * @param array|null $options
     * @return IsAllowed
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        /** @var Authorize $authorize */
        $authorize = $container->get(Authorize::class);

        return new IsAllowed($authorize);
    }
}
