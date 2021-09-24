<?php

declare(strict_types=1);

namespace BjyAuthorize\Service;

/**
 * Factory responsible of a set of {@see \BjyAuthorize\Provider\Role\ProviderInterface}
 */
class RoleProvidersServiceFactory extends BaseProvidersServiceFactory
{
    public const PROVIDER_SETTING = 'role_providers';
}
