<?php

declare(strict_types=1);

namespace BjyAuthorizeTest\Provider\Resource;

use BjyAuthorize\Provider\Resource\Config;
use PHPUnit\Framework\TestCase;

/**
 * Config resource provider test
 */
class ConfigTest extends TestCase
{
    /**
     * @covers \BjyAuthorize\Provider\Resource\Config::__construct
     * @covers \BjyAuthorize\Provider\Resource\Config::getResources
     */
    public function testGetResources()
    {
        $config = new Config(['resource1', 'resource2']);

        $resources = $config->getResources();

        $this->assertCount(2, $resources);
        $this->assertContains('resource1', $resources);
        $this->assertContains('resource2', $resources);
    }
}
