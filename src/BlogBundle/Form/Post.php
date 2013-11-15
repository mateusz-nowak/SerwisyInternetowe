<?php

namespace BlogBundle\Form;

use FormBundle\Abstracts\Form;
use AuthBundle\Entity\UserManager;

class Post extends Form
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

        $this->constraints->add('text', function($value) {
            return strlen($value) > 0 && strlen($value) < 255;
        }, 'Opis musi zawierać się w długości 0 - 255 znaków.');
    }
}
