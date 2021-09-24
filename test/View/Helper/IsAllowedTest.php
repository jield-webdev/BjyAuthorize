<?php

declare(strict_types=1);

namespace BjyAuthorizeTest\View\Helper;

use BjyAuthorize\Service\Authorize;
use BjyAuthorize\View\Helper\IsAllowed;
use PHPUnit\Framework\TestCase;

/**
 * IsAllowed view helper test
 */
class IsAllowedTest extends TestCase
{
    /**
     * @covers \BjyAuthorize\View\Helper\IsAllowed
     */
    public function testIsAllowed()
    {
        $authorize = $this->getMockBuilder(Authorize::class)->disableOriginalConstructor()->getMock();
        $authorize
            ->expects($this->once())
            ->method('isAllowed')
            ->with('test', 'privilege')
            ->will($this->returnValue(true));

        $plugin = new IsAllowed($authorize);
        $this->assertTrue($plugin->__invoke('test', 'privilege'));

        $authorize2 = $this->getMockBuilder(Authorize::class)->disableOriginalConstructor()->getMock();
        $authorize2
            ->expects($this->once())
            ->method('isAllowed')
            ->with('test2', 'privilege2')
            ->will($this->returnValue(false));

        $plugin = new IsAllowed($authorize2);

        $this->assertFalse($plugin->__invoke('test2', 'privilege2'));
    }
}
