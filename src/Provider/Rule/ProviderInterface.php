<?php

declare(strict_types=1);

namespace BjyAuthorize\Provider\Rule;

/**
 * Rule provider interface, allows specifying that an object
 * can provide ACL rules
 */
interface ProviderInterface
{
    /**
     * @return array
     */
    public function getRules();
}
