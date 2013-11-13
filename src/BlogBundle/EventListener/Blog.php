<?php

namespace BlogBundle\EventListener;

use BlogBundle\Form\Create as BlogForm;
use BlogBundle\Event\Blog as EventBlog;
use BlogBundle\Entity\Blog as BlogEntity;
use SiBundle\ContainerAware;
use BlogBundle\ValueObject\BlogModifier;

class Blog extends ContainerAware
{
    public function registerEvents()
    {
        return array(
            EventBlog::IS_CREATED => array($this, 'isCreatedEvent'),
            EventBlog::IS_DESTROYED => array($this, 'isDestroyedEvent'),
            EventBlog::IS_EDITED => array($this, 'isEditedEvent'),
        );
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
        $blog->setUser($securityContext->getUser());
        
        $this->getContainer()->get('blog.manager')->createBlog($blog);
        $securityContext->setFlash('notice', $i18n->get('flash.blog.created_success'));
        
        $this->getContainer()->get('request')->redirect('/user/blogs');
    }
}