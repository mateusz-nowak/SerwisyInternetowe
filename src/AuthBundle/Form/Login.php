<?php

namespace AuthBundle\Form;

use FormBundle\Abstracts\Form;
use AuthBundle\Entity\UserManager;

class Login extends Form
{
    protected $userManager;
    protected $userInstance;
    
    public function __construct(UserManager $userManager)
    {
        $this->userManager = $userManager;
        
        parent::__construct();
    }
    
    protected function validateFields()
    {
        $formValues = $this->getData();
        $userManager = $this->userManager;
        $userInstance = null;
        
        $this->constraints->add('email', function($value) use ($userManager, $formValues, &$userInstance) {
            $user = $userManager->findOneByEmail($value);
            
            if (!$user) {
                return false;
            }
            
            if (md5($formValues['password']) == $user->getPassword()) {
                $userInstance = $user;
                return true;
            }
        }, 'Błędny login lub hasło.');
        
        $this->userInstance = $userInstance;
    }
    
    public function getUserInstance()
    {
        return $this->userInstance;
    }
}
