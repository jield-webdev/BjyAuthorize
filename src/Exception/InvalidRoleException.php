<?php

declare(strict_types=1);

namespace BjyAuthorize\Exception;

use function get_class;
use function gettype;
use function is_object;
use function sprintf;

/**
 * Invalid role exception for BjyAuthorize
 */
class InvalidRoleException extends InvalidArgumentException
{
    /**
     * @param mixed $role
     * @return self
     */
    public static function invalidRoleInstance($role)
    {
        return new self(
            sprintf('Invalid role of type "%s" provided', is_object($role) ? get_class($role) : gettype($role))
        );
    }
}
