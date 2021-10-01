<?php

declare(strict_types=1);

namespace BjyAuthorizeTest\Acl;

use BjyAuthorize\Acl\Role;
use BjyAuthorize\Exception\InvalidRoleException;
use PHPUnit\Framework\TestCase;
use stdClass;

/**
 * Base role object tests
 */
class RoleTest extends TestCase
{
    /**
     * @covers \BjyAuthorize\Acl\Role::__construct
     * @covers \BjyAuthorize\Acl\Role::getParent
     * @covers \BjyAuthorize\Acl\Role::getRoleId
     */
    public function testConstructor()
    {
        $role = new Role('test1');

        $this->assertSame('test1', $role->getRoleId());
        $this->assertNull($role->getParent());

        $role = new Role('test2', 'parent');

        $this->assertSame('test2', $role->getRoleId());
        $parent = $role->getParent();
        $this->assertNotNull($parent);
        $this->assertSame('parent', $parent->getRoleId());
    }

    /**
     * @covers \BjyAuthorize\Acl\Role::setRoleId
     * @covers \BjyAuthorize\Acl\Role::getRoleId
     */
    public function testSetGetRoleId()
    {
        $role = new Role('test1');

        $this->assertSame('test1', $role->getRoleId());
        $role->setRoleId('test2');
        $this->assertSame('test2', $role->getRoleId());
    }

    /**
     * @covers \BjyAuthorize\Acl\Role::setParent
     * @covers \BjyAuthorize\Acl\Role::getParent
     */
    public function testSetGetParent()
    {
        $role   = new Role('test1');
        $parent = new Role('parent');

        $role->setParent($parent);
        $this->assertSame($parent, $role->getParent());

        $role->setParent('parent2');
        $this->assertNotSame($parent, $role->getParent());
        $this->assertSame('parent2', $role->getParent()->getRoleId());
    }

    /**
     * @covers \BjyAuthorize\Acl\Role::setParent
     * @covers \BjyAuthorize\Acl\Role::getParent
     */
    public function testSetParentWithNull()
    {
        $parent = new Role('parent');
        $role   = new Role('test1', $parent);

        $this->assertSame($parent, $role->getParent());

        $role->setParent(null);
        $this->assertNull($role->getParent());
    }

    /**
     * @covers \BjyAuthorize\Acl\Role::setParent
     */
    public function testSetInvalidParent()
    {
        $role = new Role('test1');

        $this->expectException(InvalidRoleException::class);
        $role->setParent(new stdClass());
    }
}
