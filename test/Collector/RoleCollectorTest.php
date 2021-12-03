<?php

declare(strict_types=1);

namespace BjyAuthorizeTest\Collector;

use ArrayIterator;
use BjyAuthorize\Collector\RoleCollector;
use BjyAuthorize\Provider\Identity\ProviderInterface;
use Laminas\Mvc\MvcEvent;
use Laminas\Permissions\Acl\Role\RoleInterface;
use PHPUnit\Framework\TestCase;

use function serialize;
use function unserialize;

/**
 * Tests for {@see \BjyAuthorize\Collector\RoleCollector}
 */
class RoleCollectorTest extends TestCase
{
    /** @var RoleCollector */
    protected $collector;

    /** @var MockObject|ProviderInterface */
    protected $identityProvider;

    /**
     * {@inheritDoc}
     *
     * @covers \BjyAuthorize\Collector\RoleCollector::__construct
     */
    public function setUp(): void
    {
        $this->identityProvider = $this->createMock(ProviderInterface::class);
        $this->collector        = new RoleCollector($this->identityProvider);
    }

    /**
     * @covers \BjyAuthorize\Collector\RoleCollector::collect
     * @covers \BjyAuthorize\Collector\RoleCollector::serialize
     * @covers \BjyAuthorize\Collector\RoleCollector::unserialize
     * @covers \BjyAuthorize\Collector\RoleCollector::getCollectedRoles
     */
    public function testCollect()
    {
        $role1    = $this->createMock(RoleInterface::class);
        $mvcEvent = $this->createMock(MvcEvent::class);

        $role1->expects($this->any())->method('getRoleId')->will($this->returnValue('role1'));

        $this
            ->identityProvider
            ->expects($this->any())
            ->method('getIdentityRoles')
            ->will(
                $this->returnValue(
                    [
                        $role1,
                        'role2',
                        'key' => 'role3',
                    ]
                )
            );

        $this->collector->collect($mvcEvent);

        $roles = $this->collector->getCollectedRoles();

        $this->assertCount(3, $roles);
        $this->assertContains('role1', $roles);
        $this->assertContains('role2', $roles);
        $this->assertContains('role3', $roles);

        /** @var RoleCollector $collector */
        $collector = unserialize(serialize($this->collector));

        $collector->collect($mvcEvent);

        $roles = $this->collector->getCollectedRoles();

        $this->assertCount(3, $roles);
        $this->assertContains('role1', $roles);
        $this->assertContains('role2', $roles);
        $this->assertContains('role3', $roles);
    }

    /**
     * @covers \BjyAuthorize\Collector\RoleCollector::collect
     * @covers \BjyAuthorize\Collector\RoleCollector::getCollectedRoles
     */
    public function testTraversableCollect()
    {
        $role1    = $this->createMock(RoleInterface::class);
        $mvcEvent = $this->createMock(MvcEvent::class);

        $role1->expects($this->any())->method('getRoleId')->will($this->returnValue('role1'));

        $this
            ->identityProvider
            ->expects($this->any())
            ->method('getIdentityRoles')
            ->will(
                $this->returnValue(
                    new ArrayIterator(
                        [
                            $role1,
                            'role2',
                            'key' => 'role3',
                        ]
                    )
                )
            );

        $this->collector->collect($mvcEvent);

        $roles = $this->collector->getCollectedRoles();

        $this->assertCount(3, $roles);
        $this->assertContains('role1', $roles);
        $this->assertContains('role2', $roles);
        $this->assertContains('role3', $roles);
    }

    /**
     * @covers \BjyAuthorize\Collector\RoleCollector::getName
     */
    public function testGetName()
    {
        $this->assertIsString($this->collector->getName());
    }

    /**
     * @covers \BjyAuthorize\Collector\RoleCollector::getPriority
     */
    public function testGetPriority()
    {
        $this->assertIsInt($this->collector->getPriority());
    }
}
