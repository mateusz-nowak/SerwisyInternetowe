<?php

namespace BlogBundle\ValueObject;

use BlogBundle\Entity\Blog;
use BlogBundle\Form\Create as BlogForm;

class BlogModifier
{
    /* @var Blog $blog */
    protected $blog;

    /* @var BlogForm $form */
    protected $form;

    public function __construct(Blog $blog, BlogForm $form)
    {
        $this->blog = $blog;
        $this->form = $form;
    }

    public function getForm()
    {
        return $this->form;
    }

    public function getBlog()
    {
        return $this->blog;
    }
}
