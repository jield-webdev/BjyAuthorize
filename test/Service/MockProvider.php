<?php

declare(strict_types=1);

namespace BjyAuthorizeTest\Service;

use Interop\Container\ContainerInterface;

class MockProvider
{
    /** @var array */
    public $options;

    /** @var ContainerInterface */
    public $container;

    /**
     * @param array $options
     */
    public function __construct(array $options, ContainerInterface $container)
    {
        $this->options   = $options;
        $this->container = $container;
    }
}
