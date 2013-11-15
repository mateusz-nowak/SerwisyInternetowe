<?php

namespace Bootstrap\Resources;

use BlogBundle\Model\Breadcrumb;
use BlogBundle\Entity\BlogManager;
use BlogBundle\Entity\PostManager;
use AuthBundle\Entity\UserManager;
use CoreBundle\Service\Template;
use CoreBundle\Service\Request;
use CoreBundle\Dispatcher;
use CoreBundle\Service\I18n;
use CoreBundle\Session;
use PDOBundle\Service\Client as PDOClient;
use AuthBundle\Form\Register as RegisterForm;
use BlogBundle\Form\Create as BlogCreateForm;
use BlogBundle\Form\Post as BlogPostForm;
use AuthBundle\Form\Login as LoginForm;
use AuthBundle\Service\SecurityContext;
use CoreBundle\AbstractDependencyContainerService;

class DependencyContainer extends AbstractDependencyContainerService
{
    protected function shareContainers()
    {
        $this->shareContainer('session', new Session);
        $this->shareContainer('dispatcher', $this->registerEventListeners());
        $this->shareContainer('pdo.manager', $this->definePdoConnection());

        $this->shareContainer('user.manager', new UserManager(
            $this->get('pdo.manager')
        ));

        $this->shareContainer('blog.manager', new BlogManager(
            $this->get('pdo.manager')
        ));
        
        $this->shareContainer('post.manager', new PostManager(
            $this->get('pdo.manager')
        ));

        $this->shareContainer('security.context', new SecurityContext(
            $this->get('user.manager'),
            $this->get('session'))
        );

        $this->shareContainer('template', new Template(
            $this->get('security.context')
        ));

        $this->shareContainer('request', new Request);

        $this->shareContainer('i18n',
            $this->defineI18nTranslation()
        );
        
        $this->shareContainer('breadcrumb', new Breadcrumb);

        // Forms
        $this->shareContainer('form.register', new RegisterForm($this->get('user.manager')));
        $this->shareContainer('form.login', new LoginForm($this->get('user.manager')));
        $this->shareContainer('form.blogs.new', new BlogCreateForm($this->get('user.manager')));
        $this->shareContainer('form.blog.posts.new', new BlogPostForm($this->get('user.manager')));
    }

    protected function defineI18nTranslation()
    {
        $i18n = new I18n();

        $i18n->registerTranslation(
            '../src/BlogBundle/I18n/pl_PL/messages.yml'
        );

        return $i18n;
    }

    protected function registerEventListeners()
    {
        $dispatcher = new Dispatcher;

        $dispatcher->registerEventListener(
            $this->createNewInstanceWithContainerAware(new \AuthBundle\EventListener\Auth)
        );

        $dispatcher->registerEventListener(
            $this->createNewInstanceWithContainerAware(new \BlogBundle\EventListener\Blog)
        );

        return $dispatcher;
    }

    protected function createNewInstanceWithContainerAware($class)
    {
        $class->setContainer($this);

        return $class;
    }

    protected function definePdoConnection()
    {
        return new PDOClient(
            $this->getParameter('db.type'),
            $this->getParameter('db.host'),
            $this->getParameter('db.port'),
            $this->getParameter('db.user'),
            $this->getParameter('db.password'),
            $this->getParameter('db.database')
        );
    }
}
