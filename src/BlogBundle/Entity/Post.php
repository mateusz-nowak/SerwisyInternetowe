<?php

namespace BlogBundle\Entity;

use AuthBundle\Entity\User;
use BlogBundle\Entity\Blog;

class Post
{
    protected $id;
    protected $title;
    protected $text;
    protected $blog;
    protected $blog_id;
    protected $createdAt;

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

    public function getText()
    {
        return $this->text;
    }

    public function setText($text)
    {
        $this->text = $text;
    }
    
    public function setBlog(Blog $blog)
    {
        $this->blog = $blog;
    }
    
    public function getBlog()
    {
        return $this->blog;
    }
    
    public function getBlogId()
    {
        return $this->blog_id;
    }

    public function setCreatedAt(\Datetime $createdAt)
    {
        $this->createdAt = $createdAt;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function toArray()
    {
        return array(
            'title' => $this->getTitle(),
            'text' => $this->getText(),
            'createdAt' => $this->getCreatedAt()->format('Y-m-d G:i:s'),
            'blog_id' => $this->getBlog()->getId()
        );
    }
    
    public function toFormArray()
    {
        return array(
            'title' => $this->getTitle(),
            'text' => $this->getText(),
        );
    }
}
