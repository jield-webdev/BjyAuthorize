<?php

declare(strict_types=1);

namespace BjyAuthorize\Provider\Identity;

use BjyAuthorize\Exception\InvalidRoleException;
use BjyAuthorize\Provider\Role\ProviderInterface as RoleProviderInterface;
use Laminas\Authentication\AuthenticationService;
use Laminas\Permissions\Acl\Role\RoleInterface;
use function is_string;

/**
 * Simple identity provider to handle simply guest|user
 */
class AuthenticationIdentityProvider implements ProviderInterface
{

    protected RoleInterface|string $defaultRole = 'guest';

    protected RoleInterface|string $authenticatedRole = 'user';

    public function __construct(protected AuthenticationService $authService)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function getIdentityRoles(): array
    {
        if (!$identity = $this->authService->getIdentity()) {
            return [$this->defaultRole];
        }

        if ($identity instanceof RoleInterface) {
            return [$identity];
        }

        if ($identity instanceof RoleProviderInterface) {
            return $identity->getRoles();
        }

        return [$this->authenticatedRole];
    }

    /**
     * Get the rule that's used if you're not authenticated
     *
     * @return string|RoleInterface
     */
    public function getDefaultRole(): string|RoleInterface
    {
        return $this->defaultRole;
    }

    /**
     * Set the rule that's used if you're not authenticated
     *
     * @param string|RoleInterface $defaultRole
     * @throws InvalidRoleException
     */
    public function setDefaultRole(string|RoleInterface $defaultRole): void
    {
        if (!$defaultRole instanceof RoleInterface && !is_string(value: $defaultRole)) {
            throw InvalidRoleException::invalidRoleInstance(role: $defaultRole);
        }

        $this->defaultRole = $defaultRole;
    }

    /**
     * Get the role that is used if you're authenticated and the identity provides no role
     *
     * @return string|RoleInterface
     */
    public function getAuthenticatedRole(): string|RoleInterface
    {
        return $this->authenticatedRole;
    }

    /**
     * Set the role that is used if you're authenticated and the identity provides no role
     *
     * @param string|RoleInterface $authenticatedRole
     * @throws InvalidRoleException
     */
    public function setAuthenticatedRole(string|RoleInterface $authenticatedRole): void
    {
        if (!$authenticatedRole instanceof RoleInterface && !is_string(value: $authenticatedRole)) {
            throw InvalidRoleException::invalidRoleInstance(role: $authenticatedRole);
        }

        $this->authenticatedRole = $authenticatedRole;
    }
}
