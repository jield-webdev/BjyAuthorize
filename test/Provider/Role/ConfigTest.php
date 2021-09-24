<?php

declare(strict_types=1);

namespace BjyAuthorizeTest\Provider\Role;

use BjyAuthorize\Acl\Role;
use BjyAuthorize\Provider\Role\Config;
use PHPUnit\Framework\TestCase;

/**
 * Config resource provider test
 */
class ConfigTest extends TestCase
{
    /**
     * @covers \BjyAuthorize\Provider\Role\Config::__construct
     * @covers \BjyAuthorize\Provider\Role\Config::loadRole
     * @covers \BjyAuthorize\Provider\Role\Config::getRoles
     */
    public function testConstructor()
    {
        $config = new Config(
            [
                'role1' => [],
                'role2',
                'role3' => [
                    'children' => ['role4'],
                ],
                'role5' => [
                    'children' => [
                        'role6',
                        'role7' => [],
                    ],
                ],
            ]
        );

        $roles = $config->getRoles();

        $this->assertCount(7, $roles);

        /** @var Role $role */
        foreach ($roles as $role) {
            $this->assertInstanceOf(Role::class, $role);
            $this->assertContains(
                $role->getRoleId(),
                ['role1', 'role2', 'role3', 'role4', 'role5', 'role6', 'role7']
            );

            if ('role4' === $role->getRoleId()) {
                $this->assertNotNull($role->getParent());
                $this->assertSame('role3', $role->getParent()->getRoleId());
            } elseif ('role6' === $role->getRoleId() || 'role7' === $role->getRoleId()) {
                $this->assertNotNull($role->getParent());
                $this->assertSame('role5', $role->getParent()->getRoleId());
            } else {
                $this->assertNull($role->getParent());
            }
        }
    }
}
