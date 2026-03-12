<?php

declare(strict_types=1);

namespace BjyAuthorize\Controller\Plugin;

use BjyAuthorize\Service\Authorize;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class IsAllowedFactory implements FactoryInterface
{
    /**
     * @param string $requestedName
     * @param array|null $options
     * @return IsAllowed
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): IsAllowed
    {
        $authorize = $container->get(Authorize::class);

        return new IsAllowed(authorizeService: $authorize);
    }
}
