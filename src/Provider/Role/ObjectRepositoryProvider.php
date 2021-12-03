<?php

declare(strict_types=1);

namespace BjyAuthorize\Provider\Role;

use BjyAuthorize\Acl\HierarchicalRoleInterface;
use BjyAuthorize\Acl\Role;
use Doctrine\Persistence\ObjectRepository;
use Laminas\Permissions\Acl\Role\RoleInterface;

use function array_values;

/**
 * Role provider based on a {@see \Doctrine\Persistence\ObjectRepository}
 */
class ObjectRepositoryProvider implements ProviderInterface
{
    /** @var ObjectRepository */
    protected $objectRepository;

    public function __construct(ObjectRepository $objectRepository)
    {
        $this->objectRepository = $objectRepository;
    }

    /**
     * {@inheritDoc}
     */
    public function getRoles()
    {
        $result = $this->objectRepository->findAll();
        $roles  = [];

        // Pass One: Build each object
        foreach ($result as $role) {
            if (! $role instanceof RoleInterface) {
                continue;
            }

            $roleId = $role->getRoleId();
            $parent = null;

            if ($role instanceof HierarchicalRoleInterface && $parent = $role->getParent()) {
                $parent = $parent->getRoleId();
            }

            $roles[$roleId] = new Role($roleId, $parent);
        }

        // Pass Two: Re-inject parent objects to preserve hierarchy
        /** @var Role $roleObj */
        foreach ($roles as $roleObj) {
            $parentRoleObj = $roleObj->getParent();

            if ($parentRoleObj && $parentRoleObj->getRoleId()) {
                $roleObj->setParent($roles[$parentRoleObj->getRoleId()]);
            }
        }

        return array_values($roles);
    }
}
