<?php

namespace SiBundle\Service;

class Request
{
    /* @var array $post */
    public $post;

    /* @var array $get */
    public $get;

    public function __construct()
    {
        $this->post = $_POST;
        $this->get = $_GET;
    }
    
    public function redirect($url)
    {
        header('Location: ' . $url);
        exit();
    }
}
