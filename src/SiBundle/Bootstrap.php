<?php

namespace SiBundle;

use Closure;

class Bootstrap
{
    protected $requestUri;
    protected $responseClosure;

    public function __construct()
    {
        $this->requestUri = $_SERVER['REQUEST_URI'];
    }

    public function get($uri, Closure $closure)
    {
        if (FALSE === $this->isPost() && preg_match('#^' . $uri . '$#', $this->requestUri, $parameters)) {
            $this->responseClosure = $closure(
                $parameters
            );
        }
    }

    public function post($uri, Closure $closure)
    {
        if (TRUE === $this->isPost() && preg_match('#^' . $uri . '$#', $this->requestUri, $parameters)) {
            $this->responseClosure = $closure(
                $parameters
            );
        }
    }

    public function run()
    {
        if (!$this->responseClosure) {
            throw new \RuntimeException('No route found');
        }

        echo $this->responseClosure;
    }

    public function isPost()
    {
        return $_SERVER['REQUEST_METHOD'] == 'POST';
    }
}
