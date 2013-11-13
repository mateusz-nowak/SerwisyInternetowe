<?php

namespace FormBundle\Abstracts;

use FormBundle\Builder\FormBuilder;
use FormBundle\Builder\ConstraintsBuilder;

abstract class Form
{
    /* @var FormBundle\Builder\FormBuilder $formFields */
    protected $formFields;

    /* @var FormBundle\Builder\ConstraintsBuilder $constraints */
    protected $constraints;

    /* @var array $data */
    protected $data;

    public function __construct()
    {
        $this->formFields = new FormBuilder;
        $this->constraints = new ConstraintsBuilder($this);
    }

    public function isValid()
    {
        $this->validateFields();

        return count($this->constraints) == 0;
    }

    abstract protected function validateFields();

    public function bind($data)
    {
        $this->data = $data;
    }

    public function getData()
    {
        return (array) $this->data;
    }

    public function getValue($fieldName)
    {
        if (array_key_exists($fieldName, $this->getData())) {
            return (string) $this->data[$fieldName];
        }

        return '';
    }

    public function getErrorForField($fieldName)
    {
        $error = $this->getErrors();

        if (array_key_exists($fieldName, $error)) {
            return $error[$fieldName];
        }
    }

    public function getErrors()
    {
        return $this->constraints->getErrors();
    }
}
