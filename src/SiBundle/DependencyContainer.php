<?php

namespace SiBundle;

use SiBundle\Template;

class DependencyContainer
{
    /* @var array $container */
    protected $container;
    
    public function __construct()
    {
        $this->container = array();
        $this->shareContainers();
    }
    
    public function get($containerName)
    {
        if (!array_key_exists($containerName, $this->container)) {
            throw new \RuntimeException(sprintf('Container %s not found.', $containerName));
        }

        return $this->container[$containerName];
    }

    protected function shareContainers()
    {
        $this->shareContainer('template', new Template());
    }
    
    protected function shareContainer($containerName, $closure)
    {
        $this->container[$containerName] = $closure;
    }
}