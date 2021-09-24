<?php

declare(strict_types=1);

namespace BjyAuthorizeTest\Provider\Role;

use BjyAuthorize\Acl\Role;
use BjyAuthorize\Provider\Role\LaminasDb;
use BjyAuthorize\Provider\Role\ObjectRepositoryProvider;
use Laminas\Db\TableGateway\TableGateway;
use Laminas\ServiceManager\ServiceLocatorInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * {@see \BjyAuthorize\Provider\Role\LaminasDb} test
 */
class LaminasDbTest extends TestCase
{
    /** @var ObjectRepositoryProvider */
    private $provider;

    /** @var ServiceLocatorInterface|MockObject */
    private $serviceLocator;

    /** @var TableGateway|MockObject */
    private $tableGateway;

    /**
     * @covers \BjyAuthorize\Provider\Role\LaminasDb::__construct
     */
    protected function setUp(): void
    {
        $this->serviceLocator = $this->createMock(ServiceLocatorInterface::class);
        $this->provider       = new LaminasDb([], $this->serviceLocator);
        $this->tableGateway   = $this->getMockBuilder(TableGateway::class)
                                     ->disableOriginalConstructor()
                                     ->getMock();
    }

    /**
     * @covers \BjyAuthorize\Provider\Role\LaminasDb::getRoles
     */
    public function testGetRoles()
    {
        $this->tableGateway->expects($this->any())->method('selectWith')->will(
            $this->returnValue(
                [
                    ['id' => 1, 'role_id' => 'guest', 'is_default' => 1, 'parent_id' => null],
                    ['id' => 2, 'role_id' => 'user', 'is_default' => 0, 'parent_id' => null],
                ]
            )
        );

        $this->serviceLocator->expects($this->any())->method('get')->will($this->returnValue($this->tableGateway));
        $provider = new LaminasDb([], $this->serviceLocator);

        $this->assertEquals($provider->getRoles(), [new Role('guest'), new Role('user')]);
    }

    /**
     * @covers \BjyAuthorize\Provider\Role\LaminasDb::getRoles
     */
    public function testGetRolesWithInheritance()
    {
        $this->tableGateway->expects($this->any())->method('selectWith')->will(
            $this->returnValue(
                [
                    ['id' => 1, 'role_id' => 'guest', 'is_default' => 1, 'parent_id' => null],
                    ['id' => 2, 'role_id' => 'user', 'is_default' => 0, 'parent_id' => 1],
                ]
            )
        );

        $this->serviceLocator->expects($this->any())->method('get')->will($this->returnValue($this->tableGateway));
        $provider = new LaminasDb([], $this->serviceLocator);

        $this->assertEquals($provider->getRoles(), [new Role('guest'), new Role('user', 'guest')]);
    }
}
