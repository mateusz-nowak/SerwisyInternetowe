<?php

namespace BlogBundle\Form;

use FormBundle\Abstracts\Form;
use AuthBundle\Entity\UserManager;

class Create extends Form
{
    protected $userManager;

    public function __construct(UserManager $userManager)
    {
        $this->userManager = $userManager;

        parent::__construct();
    }

    protected function validateFields()
    {
        $this->constraints->add('title', function($value) {
            return strlen($value) > 0 && strlen($value) < 128;
        }, 'Tytuł musi zawierać się w długości 0 - 128 znaków.');

        $this->constraints->add('description', function($value) {
            return strlen($value) > 0 && strlen($value) < 255;
        }, 'Opis musi zawierać się w długości 0 - 255 znaków.');

        $this->constraints->add('domain', function($value) {
            return strlen($value) > 3 && strlen($value) < 16;
        }, 'Domena musi być z zakresu 3 - 16 znaków.');
    }
}
