<?php

declare(strict_types=1);

namespace BjyAuthorize\View\Helper;

use BjyAuthorize\Service\Authorize;
use Laminas\View\Helper\AbstractHelper;

/**
 * IsAllowed View helper. Allows checking access to a resource/privilege in views.
 */
class IsAllowed extends AbstractHelper
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
