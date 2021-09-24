<?php

declare(strict_types=1);

namespace BjyAuthorize\Service;

use BjyAuthorize\Service\Authorize;

/**
 * Interface for Authorize-aware objects. Allows injection
 * of an {@see \BjyAuthorize\Service\Authorize}
 */
interface AuthorizeAwareInterface
{
    /**
     * @return void
     */
    public function setAuthorizeService(Authorize $auth);
}
