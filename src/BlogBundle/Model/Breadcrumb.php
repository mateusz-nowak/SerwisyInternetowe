<?php

namespace BlogBundle\Model;

use BlogBundle\ValueObject\Breadcrumb as BreadcrumbValueObject;

class Breadcrumb
{
    protected $nodes;
    
    public function __construct()
    {
        $this->nodes = new \SplObjectStorage();
    }
    
    public function attach($url, $title)
    {
        $this->nodes->attach(new BreadcrumbValueObject($url, $title));
    }
    
    public function getNodes()
    {
        return $this->nodes;
    }
}