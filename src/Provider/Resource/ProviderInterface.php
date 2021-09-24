<?php

declare(strict_types=1);

namespace BjyAuthorize\Provider\Resource;

use Laminas\Permissions\Acl\Resource\ResourceInterface;

/**
 * Resource provider interface, provides existing resources list
 */
interface ProviderInterface
{
    /**
     * @return ResourceInterface[]
     */
    public function getResources();
}
