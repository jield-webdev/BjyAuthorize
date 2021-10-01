<?php

declare(strict_types=1);

namespace BjyAuthorize\Acl;

use BjyAuthorize\Exception;
use BjyAuthorize\Exception\InvalidRoleException;
use Laminas\Permissions\Acl\Role\RoleInterface;

use function is_string;

/**
 * Base role object
 */
class Role implements RoleInterface, HierarchicalRoleInterface
{
    /** @var string */
    protected $roleId;

    /** @var RoleInterface */
    protected $parent;

    /**
     * @param string|null $roleId
     * @param RoleInterface|string|null $parent
     */
    public function __construct($roleId = null, $parent = null)
    {
        if (null !== $roleId) {
            $this->setRoleId($roleId);
        }
        if (null !== $parent) {
            $this->setParent($parent);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getRoleId()
    {
        return $this->roleId;
    }

    /**
     * @param string $roleId
     * @return self
     */
    public function setRoleId($roleId)
    {
        $this->roleId = (string) $roleId;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param RoleInterface|string|null $parent
     * @throws InvalidRoleException
     * @return self
     */
    public function setParent($parent)
    {
        if (null === $parent) {
            $this->parent = null;

            return $this;
        }

        if (is_string($parent)) {
            $this->parent = new Role($parent);

            return $this;
        }

        if ($parent instanceof RoleInterface) {
            $this->parent = $parent;

            return $this;
        }

        throw Exception\InvalidRoleException::invalidRoleInstance($parent);
    }
}
