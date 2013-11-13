<?php

namespace FormBundle\Builder;

class FormBuilder
{
    /* @var array $fields */
    protected $fields;

    public function __construct()
    {
        $this->fields = array();
    }

    public function define($fieldName)
    {
        array_push($this->fields, $fieldName);
    }
}
