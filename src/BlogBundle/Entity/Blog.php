<?php

namespace BlogBundle\Entity;

use AuthBundle\Entity\User;

class Blog
{
    protected $id;
    protected $title;
    protected $description;
    protected $user;
    protected $domain;
    
    public function getId()
    {
        return $this->id;
    }
    
    public function getTitle()
    {
        return $this->title;
    }
    
    public function setTitle($title)
    {
        $this->title = $title;
    }
    
    public function getDescription()
    {
        return $this->description;
    }
    
    public function setDescription($description)
    {
        $this->description = $description;
    }
    
    public function getDomain()
    {
        return $this->domain;
    }
    
    public function setDomain($domain)
    {
        $this->domain = $domain;
    }
    
    public function setUser(User $user)
    {
        $this->user = $user;
    }
    
    public function getUser()
    {
        return $this->user;
    }
    
    public function toArray()
    {
        return array(
            'title' => $this->getTitle(),
            'description' => $this->getDescription(),
            'domain' => $this->getDomain()
        );
    }
}