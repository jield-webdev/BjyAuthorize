<?php

declare(strict_types=1);

namespace BjyAuthorize\Service;

/**
 * Factory responsible of a set of {@see \BjyAuthorize\Provider\Rule\ProviderInterface}
 */
class RuleProvidersServiceFactory extends BaseProvidersServiceFactory
{
    public const PROVIDER_SETTING = 'rule_providers';
}
