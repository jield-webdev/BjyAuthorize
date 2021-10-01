<?php

declare(strict_types=1);

namespace BjyAuthorizeTest\Provider\Role;

use BjyAuthorize\Acl\HierarchicalRoleInterface;
use BjyAuthorize\Acl\Role;
use BjyAuthorize\Provider\Role\ObjectRepositoryProvider;
use Doctrine\Persistence\ObjectRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use stdClass;

/**
 * {@see \BjyAuthorize\Provider\Role\ObjectRepositoryProvider} test
 */
class ObjectRepositoryProviderTest extends TestCase
{
    /** @var ObjectRepositoryProvider */
    private $provider;

    /** @var MockObject */
    private $repository;

    /**
     * @covers \BjyAuthorize\Provider\Role\ObjectRepositoryProvider::__construct
     */
    protected function setUp(): void
    {
        $this->repository = $this->createMock(ObjectRepository::class);
        $this->provider   = new ObjectRepositoryProvider($this->repository);
    }

    /**
     * @param string $name
     * @param string $parent
     * @return MockObject|HierarchicalRoleInterface
     */
    private function createRoleMock($name, $parent)
    {
        $role = $this->createMock(HierarchicalRoleInterface::class);
        $role->expects($this->atLeastOnce())
            ->method('getRoleId')
            ->will($this->returnValue($name));

        $role->expects($this->atLeastOnce())
            ->method('getParent')
            ->will($this->returnValue($parent));

        return $role;
    }

    /**
     * @covers \BjyAuthorize\Provider\Role\ObjectRepositoryProvider::getRoles
     */
    public function testGetRolesWithNoParents()
    {
        // Set up mocks
        $roles = [
            new stdClass(), // to be skipped
            $this->createRoleMock('role1', null),
            $this->createRoleMock('role2', null),
        ];

        $this->repository->expects($this->once())
            ->method('findAll')
            ->will($this->returnValue($roles));

        // Set up the expected outcome
        $expects = [
            new Role('role1', null),
            new Role('role2', null),
        ];

        $this->assertEquals($expects, $this->provider->getRoles());
    }

    /**
     * @covers \BjyAuthorize\Provider\Role\ObjectRepositoryProvider::getRoles
     */
    public function testGetRolesWithParents()
    {
        // Setup mocks
        $role1 = $this->createRoleMock('role1', null);
        $roles = [
            $role1,
            $this->createRoleMock('role2', null),
            $this->createRoleMock('role3', $role1),
        ];

        $this->repository->expects($this->once())
            ->method('findAll')
            ->will($this->returnValue($roles));

        // Set up the expected outcome
        $expectedRole1 = new Role('role1', null);
        $expects       = [
            $expectedRole1,
            new Role('role2', null),
            new Role('role3', $expectedRole1),
        ];

        $this->assertEquals($expects, $this->provider->getRoles());
    }
}
