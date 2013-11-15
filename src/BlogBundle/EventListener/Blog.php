<?php

namespace BlogBundle\EventListener;

use BlogBundle\Form\Create as BlogForm;
use BlogBundle\Event\Blog as EventBlog;
use BlogBundle\Entity\Blog as BlogEntity;
use CoreBundle\ContainerAware;
use BlogBundle\ValueObject\BlogModifier;
use BlogBundle\ValueObject\PostModifier;
use BlogBundle\Event\PostEvent;
use BlogBundle\Entity\Post;

class Blog extends ContainerAware
{
    public function registerEvents()
    {
        return array(
            EventBlog::IS_CREATED => array($this, 'isCreatedEvent'),
            EventBlog::IS_DESTROYED => array($this, 'isDestroyedEvent'),
            EventBlog::IS_EDITED => array($this, 'isEditedEvent'),
            EventBlog::IS_NEW_POST_CREATED => array($this, 'isNewPostCreatedEvent'),
            EventBlog::IS_POST_DESTROYED => array($this, 'isPostDestroyed'),
            EventBlog::IS_POST_EDITED => array($this, 'isPostEdited')
        );
    }
    
    public function isPostEdited(PostModifier $postModifier)
    {
        $securityContext = $this->getContainer()->get('security.context');
        $i18n = $this->getContainer()->get('i18n');
            
        $form = $postModifier->getForm();
        $post = $postModifier->getPost();
        
        $this->getContainer()->get('post.manager')->edit($postModifier);

        $securityContext->setFlash('notice', $i18n->get('flash.post.edited_success'));
        $this->getContainer()->get('request')->redirect('/user/blog/' . $post->getBlog()->getId() . '/manage');
    }
    
    public function isPostDestroyed(Post $post)
    {
        $securityContext = $this->getContainer()->get('security.context');
        $i18n = $this->getContainer()->get('i18n');

        $this->getContainer()->get('post.manager')->destroy($post);

        $securityContext->setFlash('notice', $i18n->get('flash.post.deleted_success'));
        $this->getContainer()->get('request')->redirect('/user/blog/' . $post->getBlogId() . '/manage');
    }
    
    public function isNewPostCreatedEvent(PostEvent $event)
    {
        $securityContext = $this->getContainer()->get('security.context');
        $i18n = $this->getContainer()->get('i18n');

        $form = $event->getForm();
        $data = $form->getData();
        
        $post = new Post();
        $post->setText($data['text']);
        $post->setTitle($data['title']);
        $post->setBlog($event->getBlog());
        $post->setCreatedAt(new \Datetime);
        
        $this->getContainer()->get('post.manager')->createPost($post);
        $securityContext->setFlash('notice', $i18n->get('flash.post.created_success'));

        $this->getContainer()->get('request')->redirect('/user/blog/' . $post->getBlog()->getId() . '/manage');
    }

    public function isEditedEvent(BlogModifier $modifier)
    {
        $securityContext = $this->getContainer()->get('security.context');
        $i18n = $this->getContainer()->get('i18n');

        $this->getContainer()->get('blog.manager')->edit($modifier);

        $securityContext->setFlash('notice', $i18n->get('flash.blog.edited_success'));
        $this->getContainer()->get('request')->redirect('/user/blogs');
    }

    public function isDestroyedEvent(BlogEntity $blog)
    {
        $securityContext = $this->getContainer()->get('security.context');
        $i18n = $this->getContainer()->get('i18n');

        $this->getContainer()->get('blog.manager')->destroy($blog);

        $securityContext->setFlash('notice', $i18n->get('flash.blog.deleted_success'));
        $this->getContainer()->get('request')->redirect('/user/blogs');
    }

    public function isCreatedEvent(BlogForm $form)
    {
        $securityContext = $this->getContainer()->get('security.context');
        $i18n = $this->getContainer()->get('i18n');

        $data = $form->getData();

        $blog = new BlogEntity();
        $blog->setTitle($data['title']);
        $blog->setDescription($data['description']);
        $blog->setDomain($data['domain']);
        $blog->setUser($securityContext->getUser());

        $this->getContainer()->get('blog.manager')->createBlog($blog);
        $securityContext->setFlash('notice', $i18n->get('flash.blog.created_success'));

        $this->getContainer()->get('request')->redirect('/user/blogs');
    }
}
