<?php

namespace SiBundle;

class Template
{
    public function render($templatePath, array $arguments = array())
    {
        $src = '../' . $templatePath;

        ob_start();

        if (!file_exists($src)) {
            throw new RuntimeException(sprintf(
                'Template %s does not found',
                $src
            ));
        }
        extract($arguments);
        include_once $src;

        return ob_get_clean();
    }
}
