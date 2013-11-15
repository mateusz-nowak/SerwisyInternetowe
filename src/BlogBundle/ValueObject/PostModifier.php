<?php

namespace BlogBundle\ValueObject;

use BlogBundle\Entity\Post;
use BlogBundle\Form\Post as PostForm;

class PostModifier
{
    /* @var Post $post */
    protected $post;

    /* @var PostForm $form */
    protected $form;

    public function __construct(Post $post, PostForm $form)
    {
        $this->post = $post;
        $this->form = $form;
    }

    public function getForm()
    {
        return $this->form;
    }

    public function getPost()
    {
        return $this->post;
    }
}
