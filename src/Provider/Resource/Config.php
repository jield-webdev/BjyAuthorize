<?php

declare(strict_types=1);

namespace BjyAuthorize\Provider\Resource;

use Laminas\Permissions\Acl\Resource\ResourceInterface;

/**
 * Array-based resources list
 */
class Config implements ProviderInterface
{
    /** @var ResourceInterface[] */
    protected $resources = [];

    /**
     * @param ResourceInterface[] $config
     */
    public function __construct(array $config = [])
    {
        $this->resources = $config;
    }

    /**
     * {@inheritDoc}
     */
    public function getResources()
    {
        return $this->resources;
    }
}
