<?php

declare(strict_types=1);

namespace BjyAuthorize\Provider\Identity;

use Laminas\Permissions\Acl\Role\RoleInterface;

/**
 * Interface for identity providers, which are objects capable of
 * retrieving an active identity's role
 */
interface ProviderInterface
{
    /**
     * Retrieve roles for the current identity
     *
     * @return string[]|RoleInterface[]
     */
    public function getIdentityRoles();
}
