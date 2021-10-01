<?php

declare(strict_types=1);

namespace BjyAuthorize\Service;

use BjyAuthorize\Service\Authorize;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Initializer\InitializerInterface;

/**
 * Initializer that injects a {@see \BjyAuthorize\Service\Authorize} in
 * objects that are instances of {@see \BjyAuthorize\Service\AuthorizeAwareInterface}
 */
class AuthorizeAwareServiceInitializer implements InitializerInterface
{
    /**
     * {@inheritDoc}
     *
     * @see \Laminas\ServiceManager\Initializer\InitializerInterface::__invoke()
     */
    public function __invoke(ContainerInterface $container, $instance)
    {
        if (! $instance instanceof AuthorizeAwareInterface) {
            return;
        }

        /** @var Authorize $authorize */
        $authorize = $container->get(Authorize::class);

        $instance->setAuthorizeService($authorize);
    }
}
