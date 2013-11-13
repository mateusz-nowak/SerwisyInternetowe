<?php

namespace SiBundle;

use BlogBundle\Entity\BlogManager;
use AuthBundle\Entity\UserManager;
use SiBundle\Service\Template;
use SiBundle\Service\Request;
use SiBundle\Dispatcher;
use SiBundle\Service\I18n;
use SiBundle\Session;
use PDOBundle\Service\Client as PDOClient;
use AuthBundle\Form\Register as RegisterForm;
use BlogBundle\Form\Create as BlogCreateForm;
use AuthBundle\Form\Login as LoginForm;
use AuthBundle\Service\SecurityContext;

class DependencyContainer
{
    /* @var array $container */
    protected $container;

    public function __construct()
    {
        $this->container = array();
        $this->shareContainers();
    }

    public function get($containerName)
    {
        if (!array_key_exists($containerName, $this->container)) {
            throw new \RuntimeException(sprintf('Container %s not found.', $containerName));
        }

        return $this->container[$containerName];
    }

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
        
        // Forms
        $this->shareContainer('form.register', new RegisterForm($this->get('user.manager')));
        $this->shareContainer('form.login', new LoginForm($this->get('user.manager')));
        $this->shareContainer('form.blogs.new', new BlogCreateForm($this->get('user.manager')));
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
            'mysql', 'localhost', 3306, 'root', '5qrnaq3', 'si_devel'
        );
    }

    protected function shareContainer($containerName, $closure)
    {
        $this->container[$containerName] = $closure;
    }
}
