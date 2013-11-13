<?php

namespace SiBundle;

class ContainerAware
{
    protected $container;

    public function setContainer($container)
    {
        $this->container = $container;
    }

    public function getContainer()
    {
        return $this->container;
    }
}
