<?php

namespace AuthBundle\Form;

use FormBundle\Abstracts\Form;
use AuthBundle\Entity\UserManager;

class Register extends Form
{
    protected $userManager;

    public function __construct(UserManager $userManager)
    {
        $this->userManager = $userManager;

        parent::__construct();
    }

    protected function validateFields()
    {
        $formValues = $this->getData();
        $userManager = $this->userManager;

        $this->constraints->add('email', function($value) use ($userManager) {
            return !$userManager->findOneByEmail($value);
        }, 'Ten e-mail jest już zajęty.');

        $this->constraints->add('email', function($value) use ($userManager) {
            return filter_var($value, FILTER_VALIDATE_EMAIL);
        }, 'Niepoprawny e-mail.');

        $this->constraints->add('password', function($value) {
           return strlen($value) >= 6;
        }, 'Hasło musi składać się z conajmniej 6 znaków.');

        $this->constraints->add('repeatPassword', function($value) use ($formValues) {
           return $value == $formValues['password'];
        }, 'Hasła nie są identyczne.');

        $this->constraints->add('name', function($value) {
           return strlen($value) >= 2;
        }, 'Podaj swoje imię.');
        //
        // $this->constraints->add('surname', function($value) {
        //    return strlen($value) >= 2;
        // }, 'Podaj swoje nazwisko.');
    }
}
