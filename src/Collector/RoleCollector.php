<?php

declare(strict_types=1);

namespace BjyAuthorize\Collector;

use BjyAuthorize\Provider\Identity\ProviderInterface;
use Laminas\DeveloperTools\Collector\CollectorInterface;
use Laminas\Mvc\MvcEvent;
use Laminas\Permissions\Acl\Role\RoleInterface;
use Serializable;
use Traversable;

use function is_array;
use function serialize;
use function unserialize;

/**
 * Role collector - collects the role during dispatch
 */
class RoleCollector implements CollectorInterface, Serializable
{
    public const NAME = 'bjy_authorize_role_collector';

    public const PRIORITY = 150;

    /** @var array|string[] collected role ids */
    protected $collectedRoles = [];

    /** @var ProviderInterface|null */
    protected $identityProvider;

    public function __construct(ProviderInterface $identityProvider)
    {
        $this->identityProvider = $identityProvider;
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return static::NAME;
    }

    /**
     * {@inheritDoc}
     */
    public function getPriority()
    {
        return static::PRIORITY;
    }

    /**
     * {@inheritDoc}
     */
    public function collect(MvcEvent $mvcEvent)
    {
        if (! $this->identityProvider) {
            return;
        }

        $roles = $this->identityProvider->getIdentityRoles();

        if (! is_array($roles) && ! $roles instanceof Traversable) {
            $roles = (array) $roles;
        }

        foreach ($roles as $role) {
            if ($role instanceof RoleInterface) {
                $role = $role->getRoleId();
            }

            if ($role) {
                $this->collectedRoles[] = (string) $role;
            }
        }
    }

    /**
     * @return array|string[]
     */
    public function getCollectedRoles()
    {
        return $this->collectedRoles;
    }

    /**
     * {@inheritDoc}
     * TODO remove with php74+
     */
    public function serialize()
    {
        return serialize($this->collectedRoles);
    }

    /**
     * {@inheritDoc}
     * TODO remove with php74+
     */
    public function unserialize($serialized)
    {
        $this->collectedRoles = unserialize($serialized);
    }

    /**
     * {@inheritDoc}
     */
    public function __serialize()
    {
        return [
            'collectedRoles' => $this->collectedRoles,
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function __unserialize(array $serialized)
    {
        $this->collectedRoles = $serialized['collectedRoles'];
    }
}
