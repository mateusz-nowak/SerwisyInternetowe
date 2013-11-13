<?php

namespace AuthBundle\EventListener;

use AuthBundle\Event\Auth as EventAuth;
use SiBundle\ContainerAware;
use AuthBundle\Entity\User;
use AuthBundle\Form\Register as RegisterForm;

class Auth extends ContainerAware
{
    public function registerEvents()
    {
        return array(
            EventAuth::IS_LOGGED => array($this, 'isLoggedEvent'),
            EventAuth::IS_REGISTERED => array($this, 'isRegisteredEvent'),
            EventAuth::IS_LOGGED_OUT => array($this, 'isLoggedOutEvent'),
        );
    }
    
    public function isLoggedOutEvent()
    {    
        $i18n = $this->getContainer()->get('i18n');
        $securityContext = $this->getContainer()->get('security.context');
        $securityContext->logout();
        
        $securityContext->setFlash('notice', $i18n->get('flash.user.logged_out'));
        
        $this->getContainer()->get('request')->redirect('/');
    }
    
    public function isLoggedEvent(User $user)
    {
        $i18n = $this->getContainer()->get('i18n');
        $template = $this->getContainer()->get('template');
        
        $securityContext = $this->getContainer()->get('security.context');
        $securityContext->authenticate($user);
        
        $securityContext->setFlash('notice', $i18n->get('flash.user.logged_in'));
        
        $this->getContainer()->get('request')->redirect('/');
    }
    
    public function isRegisteredEvent(RegisterForm $registerForm)
    {
        $i18n = $this->getContainer()->get('i18n');
        $template = $this->getContainer()->get('template');
        $userManager = $this->getContainer()->get('user.manager');
        $userData = $registerForm->getData();
        
        $user = new User();
        $user->setEmail($userData['email']);
        $user->setPassword($userData['password']);
        $user->setCreatedAt(new \Datetime);
        $user->setName($userData['name']);
        $user->setSurname($userData['surname']);
        
        $userManager->registerUser($user);
        
        return $template->render('src/AuthBundle/Views/user/new_success.html', array(
            'title' => $i18n->get('user.heading.register')
        ));
    }
}