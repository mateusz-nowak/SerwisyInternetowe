<?php

namespace BlogBundle\Event;

use BlogBundle\Entity\Blog as BlogEntity;
use BlogBundle\Form\Post as PostForm;

class PostEvent
{
    /* @var BlogEntity $blog */
    protected $blog;

    /* @var PostForm $form */
    protected $form;

    public function __construct(BlogEntity $blog, PostForm $form)
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
