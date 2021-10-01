<?php

declare(strict_types=1);

namespace BjyAuthorize\Service;

/**
 * Factory responsible of building a set of {@see \BjyAuthorize\Guard\GuardInterface}
 */
class GuardsServiceFactory extends BaseProvidersServiceFactory
{
    public const PROVIDER_SETTING = 'guards';
}
