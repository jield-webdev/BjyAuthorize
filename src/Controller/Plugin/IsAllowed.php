<?php

declare(strict_types=1);

namespace BjyAuthorize\Controller\Plugin;

use BjyAuthorize\Service\Authorize;
use Laminas\Mvc\Controller\Plugin\AbstractPlugin;

/**
 * IsAllowed Controller plugin. Allows checking access to a resource/privilege in controllers.
 */
class IsAllowed extends AbstractPlugin
{
    /** @var Authorize */
    protected Authorize $authorizeService;

    public function __construct(Authorize $authorizeService)
    {
        $this->authorizeService = $authorizeService;
    }

    /**
     * @param mixed $resource
     * @param mixed|null $privilege
     * @return bool
     */
    public function __invoke(mixed $resource, mixed $privilege = null): bool
    {
        return $this->authorizeService->isAllowed(resource: $resource, privilege: $privilege);
    }
}
