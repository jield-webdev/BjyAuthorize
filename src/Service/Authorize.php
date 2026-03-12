<?php

declare(strict_types=1);

namespace BjyAuthorize\Service;

use BjyAuthorize\Acl\Role;
use BjyAuthorize\Guard\GuardInterface;
use BjyAuthorize\Provider\Identity\ProviderInterface as IdentityProvider;
use BjyAuthorize\Provider\Resource\ProviderInterface as ResourceProvider;
use BjyAuthorize\Provider\Role\ProviderInterface as RoleProvider;
use BjyAuthorize\Provider\Rule\ProviderInterface as RuleProvider;
use Closure;
use Interop\Container\ContainerInterface;
use Laminas\Cache\Storage\StorageInterface;
use Laminas\Permissions\Acl\Acl;
use Laminas\Permissions\Acl\Exception\InvalidArgumentException;
use Laminas\Permissions\Acl\Resource\GenericResource;
use Laminas\Permissions\Acl\Resource\ResourceInterface;
use Traversable;
use function count;
use function is_array;
use function is_int;
use function is_string;
use function print_r;

/**
 * Authorize service
 */
class Authorize
{
    public const string TYPE_ALLOW = 'allow';

    public const string TYPE_DENY = 'deny';

    protected ?Acl $acl = null;

    /** @var RoleProvider[] */
    protected array $roleProviders = [];

    /** @var ResourceProvider[] */
    protected array $resourceProviders = [];

    /** @var RuleProvider[] */
    protected array $ruleProviders = [];

    protected IdentityProvider $identityProvider;

    /** @var GuardInterface[] */
    protected array $guards = [];

    protected ?Closure $loaded = null;

    protected ContainerInterface $serviceLocator;

    /** @var array */
    protected array $config;

    /**
     * @param array $config
     */
    public function __construct(array $config, ContainerInterface $serviceLocator)
    {
        $this->config         = $config;
        $this->serviceLocator = $serviceLocator;
        $that                 = $this;
        $this->loaded         = static function () use ($that) {
            $that->load();
        };
    }

    public function addRoleProvider(RoleProvider $provider): static
    {
        $this->loaded && $this->loaded->__invoke();

        $this->roleProviders[] = $provider;

        return $this;
    }

    public function addResourceProvider(ResourceProvider $provider): static
    {
        $this->loaded && $this->loaded->__invoke();

        $this->resourceProviders[] = $provider;

        return $this;
    }

    public function addRuleProvider(RuleProvider $provider): static
    {
        $this->loaded && $this->loaded->__invoke();

        $this->ruleProviders[] = $provider;

        return $this;
    }

    public function setIdentityProvider(IdentityProvider $provider): static
    {
        $this->loaded && $this->loaded->__invoke();

        $this->identityProvider = $provider;

        return $this;
    }

    public function getIdentityProvider(): IdentityProvider
    {
        $this->loaded && $this->loaded->__invoke();

        return $this->identityProvider;
    }

    public function addGuard(GuardInterface $guard): static
    {
        $this->loaded && $this->loaded->__invoke();

        $this->guards[] = $guard;

        if ($guard instanceof ResourceProvider) {
            $this->addResourceProvider(provider: $guard);
        }

        if ($guard instanceof RuleProvider) {
            $this->addRuleProvider(provider: $guard);
        }

        return $this;
    }

    public function getGuards(): array
    {
        $this->loaded && $this->loaded->__invoke();

        return $this->guards;
    }

    public function getIdentity()
    {
        $this->loaded && $this->loaded->__invoke();

        return 'bjyauthorize-identity';
    }

    /**
     * @return Acl
     */
    public function getAcl(): Acl
    {
        $this->loaded && $this->loaded->__invoke();

        return $this->acl;
    }

    /**
     * @param string|ResourceInterface $resource
     * @param string|null $privilege
     * @return bool
     */
    public function isAllowed(ResourceInterface|string $resource, ?string $privilege = null): bool
    {
        $this->loaded && $this->loaded->__invoke();

        try {
            return $this->acl->isAllowed(role: $this->getIdentity(), resource: $resource, privilege: $privilege);
        } catch (InvalidArgumentException) {
            return false;
        }
    }

    /**
     * Initializes the service
     *
     * @return void
     * @internal
     *
     */
    public function load(): void
    {
        if (null === $this->loaded) {
            return;
        }

        $this->loaded = null;

        /** @var StorageInterface $cache */
        $cache = $this->serviceLocator->get('BjyAuthorize\Cache');

        /** @var callable $cacheKeyGenerator */
        $cacheKeyGenerator = $this->serviceLocator->get('BjyAuthorize\CacheKeyGenerator');
        $cacheKey          = $cacheKeyGenerator();

        $success      = false;
        $cacheEnabled = $this->config['cache_enabled'] ?? false;
        if ($cacheEnabled) {
            $this->acl = $cache->getItem(key: $cacheKey, success: $success);
        }

        if (!$this->acl instanceof Acl || !$success) {
            $this->loadAcl();
            if ($cacheEnabled) {
                $cache->setItem(key: $cacheKey, value: $this->acl);
            }
        }

        $this->setIdentityProvider(provider: $this->serviceLocator->get(IdentityProvider::class));

        $parentRoles = $this->getIdentityProvider()->getIdentityRoles();

        $this->acl->addRole(role: $this->getIdentity(), parents: $parentRoles);
    }

    protected function addRoles($roles): void
    {
        if (!is_array(value: $roles) && !$roles instanceof Traversable) {
            $roles = [$roles];
        }

        /** @var Role $role */
        foreach ($roles as $role) {
            if ($this->acl->hasRole(role: $role)) {
                continue;
            }

            if ($role->getParent() !== null) {
                $this->addRoles(roles: [$role->getParent()]);
                $this->acl->addRole(role: $role, parents: $role->getParent());
            } elseif (!$this->acl->hasRole(role: $role)) {
                $this->acl->addRole(role: $role);
            }
        }
    }

    protected function loadResource($resources, $parent = null): void
    {
        if (!is_array(value: $resources) && !$resources instanceof Traversable) {
            throw new \InvalidArgumentException(message: 'Resources argument must be traversable: ' . print_r(value: $resources, return: true));
        }

        foreach ($resources as $key => $value) {
            if ($value instanceof ResourceInterface) {
                $key = $value;
            } elseif (is_string(value: $key)) {
                $key = new GenericResource(resourceId: $key);
            } elseif (is_int(value: $key)) {
                $key = new GenericResource(resourceId: $value);
            }

            if (is_iterable(value: $value)) {
                $this->acl->addResource(resource: $key, parent: $parent);
                $this->loadResource(resources: $value, parent: $key);
            } elseif (!$this->acl->hasResource(resource: $key)) {
                $this->acl->addResource(resource: $key, parent: $parent);
            }
        }
    }

    protected function loadRule(array $rule, $type): void
    {
        $privileges = null;
        $assertion  = null;
        $ruleSize   = count(value: $rule);

        if (4 === $ruleSize) {
            [$roles, $resources, $privileges, $assertion] = $rule;
            $assertion = $this->serviceLocator->get($assertion);
        } elseif (3 === $ruleSize) {
            [$roles, $resources, $privileges] = $rule;
        } elseif (2 === $ruleSize) {
            [$roles, $resources] = $rule;
        } else {
            throw new \InvalidArgumentException(message: 'Invalid rule definition: ' . print_r(value: $rule, return: true));
        }

        if (is_string(value: $assertion)) {
            $assertion = $this->serviceLocator->get($assertion);
        }

        if (static::TYPE_ALLOW === $type) {
            $this->acl->allow(roles: $roles, resources: $resources, privileges: $privileges, assert: $assertion);
        } else {
            $this->acl->deny(roles: $roles, resources: $resources, privileges: $privileges, assert: $assertion);
        }
    }

    /**
     * Initialize the Acl
     */
    private function loadAcl(): void
    {
        $this->acl = new Acl();

        foreach ($this->serviceLocator->get('BjyAuthorize\RoleProviders') as $provider) {
            $this->addRoleProvider(provider: $provider);
        }

        foreach ($this->serviceLocator->get('BjyAuthorize\ResourceProviders') as $provider) {
            $this->addResourceProvider(provider: $provider);
        }

        foreach ($this->serviceLocator->get('BjyAuthorize\RuleProviders') as $provider) {
            $this->addRuleProvider(provider: $provider);
        }

        foreach ($this->serviceLocator->get('BjyAuthorize\Guards') as $guard) {
            $this->addGuard(guard: $guard);
        }

        foreach ($this->roleProviders as $provider) {
            $this->addRoles(roles: $provider->getRoles());
        }

        foreach ($this->resourceProviders as $provider) {
            $this->loadResource(resources: $provider->getResources(), parent: null);
        }

        foreach ($this->ruleProviders as $provider) {
            $rules = $provider->getRules();
            if (isset($rules['allow'])) {
                foreach ($rules['allow'] as $rule) {
                    $this->loadRule(rule: $rule, type: static::TYPE_ALLOW);
                }
            }

            if (isset($rules['deny'])) {
                foreach ($rules['deny'] as $rule) {
                    $this->loadRule(rule: $rule, type: static::TYPE_DENY);
                }
            }
        }
    }
}
