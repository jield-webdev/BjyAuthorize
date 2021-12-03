<?php

declare(strict_types=1);

namespace BjyAuthorizeTest\Provider\Identity;

use BjyAuthorize\Exception\InvalidRoleException;
use BjyAuthorize\Provider\Identity\LmcUserLaminasDb;
use Laminas\Authentication\AuthenticationService;
use Laminas\Db\TableGateway\TableGateway;
use Laminas\Permissions\Acl\Role\RoleInterface;
use LmcUser\Service\User;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * {@see \BjyAuthorize\Provider\Identity\LmcUserLaminasDb} test
 */
class LmcUserLaminasDbTest extends TestCase
{
    /** @var AuthenticationService|MockObject */
    protected $authService;

    /** @var User|MockObject */
    protected $userService;

    /** @var TableGateway|MockObject */
    private $tableGateway;

    /** @var LmcUserLaminasDb */
    protected $provider;

    /**
     * {@inheritDoc}
     *
     * @covers \BjyAuthorize\Provider\Identity\LmcUserLaminasDb::__construct
     */
    protected function setUp(): void
    {
        $this->authService  = $this->createMock(AuthenticationService::class);
        $this->userService  = $this->getMockBuilder(User::class)->getMock();
        $this->tableGateway = $this->getMockBuilder(TableGateway::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this
            ->userService
            ->expects($this->any())
            ->method('getAuthService')
            ->will($this->returnValue($this->authService));

        $this->provider = new LmcUserLaminasDb($this->tableGateway, $this->userService);
    }

    /**
     * @covers \BjyAuthorize\Provider\Identity\LmcUserLaminasDb::getIdentityRoles
     * @covers \BjyAuthorize\Provider\Identity\LmcUserLaminasDb::setDefaultRole
     */
    public function testGetIdentityRolesWithNoAuthIdentity()
    {
        $this->provider->setDefaultRole('test-default');

        $this->assertSame(['test-default'], $this->provider->getIdentityRoles());
    }

    /**
     * @covers \BjyAuthorize\Provider\Identity\LmcUserLaminasDb::getIdentityRoles
     */
    public function testSetGetDefaultRole()
    {
        $this->provider->setDefaultRole('test');
        $this->assertSame('test', $this->provider->getDefaultRole());

        $role = $this->createMock(RoleInterface::class);
        $this->provider->setDefaultRole($role);
        $this->assertSame($role, $this->provider->getDefaultRole());

        $this->expectException(InvalidRoleException::class);
        $this->provider->setDefaultRole(false);
    }

    /**
     * @covers \BjyAuthorize\Provider\Identity\LmcUserLaminasDb::getIdentityRoles
     */
    public function testGetIdentityRoles()
    {
        $roles = $this->provider->getIdentityRoles();
        $this->assertEquals($roles, [null]);
    }
}
