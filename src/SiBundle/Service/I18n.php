<?php

namespace SiBundle\Service;

use Symfony\Component\Yaml\Yaml;

class I18n
{
    protected $translations;
    
    public function __construct()
    {
        $this->translations = array();
    }
    
    public function registerTranslation($translation)
    {
        if (!is_file($translation)) {
            throw new \RuntimeException('Translation: ' . $translation . ' does not exists.');
        }
        
        $this->translations = array_merge($this->translations, Yaml::parse(file_get_contents($translation)));
    }
    
    public function get($translationField)
    {
        if (array_key_exists($translationField, $this->translations)) {
            return $this->translations[$translationField];
        }
        
        return $translationField;
    }
}
