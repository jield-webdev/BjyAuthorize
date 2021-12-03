<?php

declare(strict_types=1);

namespace BjyAuthorize\Service;

/**
 * Factory responsible of a set of {@see \BjyAuthorize\Provider\Resource\ProviderInterface}
 */
class ResourceProvidersServiceFactory extends BaseProvidersServiceFactory
{
    public const PROVIDER_SETTING = 'resource_providers';
}
