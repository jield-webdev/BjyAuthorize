<?php

declare(strict_types=1);

namespace BjyAuthorize\Provider\Role;

use Laminas\Permissions\Acl\Role\RoleInterface;

/**
 * Role provider interface, provides existing roles list
 */
interface ProviderInterface
{
    /**
     * @return RoleInterface[]
     */
    public function getRoles();
}
