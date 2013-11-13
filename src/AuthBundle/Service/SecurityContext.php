<?php

namespace AuthBundle\Service;

use AuthBundle\Entity\UserManager;
use AuthBundle\Entity\User;
use CoreBundle\Session;

class SecurityContext
{
    protected $userManager;
    protected $session;

    public function __construct(UserManager $userManager, Session $session)
    {
        $this->userManager = $userManager;
        $this->session = $session;
    }

    public function authenticate(User $user)
    {
        $this->session->set('user_id', $user->getId());
    }

    public function setFlash($type, $msg)
    {
        $this->session->set('flash_' . $type, $msg);
    }

    public function getFlash($type)
    {
        $flash = $this->session->get('flash_' . $type);

        $this->session->set('flash_' . $type, null);

        return $flash;
    }

    public function getUser()
    {
        if (!$this->getUserId()) {
            return false;
        }

        $user = $this->userManager->findOneById($this->getUserId());

        return $user;
    }

    public function logout()
    {
        $this->session->set('user_id', null);
    }

    protected function getUserId()
    {
        return $this->session->get('user_id');
    }
}
