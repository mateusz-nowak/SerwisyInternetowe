<?php

namespace SiBundle\Service;

use AuthBundle\Service\SecurityContext;

class Template
{
    protected $securityContext;

    public function __construct(SecurityContext $securityContext)
    {
        $this->securityContext = $securityContext;
    }

    public function render($templatePath, array $arguments = array())
    {
        $src = '../' . $templatePath;

        ob_start();

        if (!file_exists($src)) {
            throw new \RuntimeException(sprintf(
                'Template %s does not found',
                $src
            ));
        }

        $arguments['app'] = array(
            'user' => $this->securityContext->getUser(),
            'security.context' => $this->securityContext,
        );

        extract($arguments);
        include_once $src;

        return ob_get_clean();
    }
}
