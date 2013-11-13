<?php

namespace AuthBundle\Entity;

class User
{
    protected $id;
    protected $email;
    protected $password;
    protected $name;
    protected $surname;
    protected $createdAt;
    
    public function getId()
    {
        return $this->id;
    }
    
    public function getEmail()
    {
        return $this->email;
    }
    
    public function setEmail($email)
    {
        $this->email = $email;
    }
    
    public function setPassword($password)
    {
        $this->password = md5($password);
    }
    
    public function setPlainPassword($password)
    {
        $this->password = $password;
    }
    
    public function getPassword()
    {
        return $this->password;
    }
    
    public function setName($name)
    {
        $this->name = $name;
    }
    
    public function getName()
    {
        return $this->name;
    }
    
    public function setSurname($surname)
    {
        $this->surname = $surname;
    }
    
    public function getSurname()
    {
        return $this->surname;
    }
    
    public function getFullname()
    {
        return sprintf('%s %s', $this->getName(), $this->getSurname());
    }
    
    public function setCreatedAt(\Datetime $createdAt)
    {
        $this->createdAt = $createdAt;
    }
    
    public function getCreatedAt()
    {
        return $this->createdAt;
    }
}