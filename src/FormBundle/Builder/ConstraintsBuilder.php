<?php

namespace FormBundle\Builder;

use FormBundle\Abstracts\Form;

use Closure;
use Countable;

class ConstraintsBuilder implements Countable
{
    /* @var \FormBundle\Abstracts\Form $form */
    protected $form;

    /* @var array $errors */
    protected $errors;

    public function __construct(Form $form)
    {
        $this->form = $form;
        $this->errors = array();
    }

    public function add($fieldName, Closure $closure, $messageFail)
    {
        $formData = $this->form->getData();

        if ($closure($formData[$fieldName])) {
            return;
        }

        $this->errors[$fieldName] = $messageFail;
    }

    public function count()
    {
        return count($this->errors);
    }

    public function getErrors()
    {
        return $this->errors;
    }
}
