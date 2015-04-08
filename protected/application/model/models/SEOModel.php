<?php

Application::import(PATH_APPLICATION . 'model/Model.php');
Application::import(PATH_APPLICATION . 'model/processors/SEODBProcessor.php');
Application::import(PATH_APPLICATION . 'model/processors/SEOCacheProcessor.php');


class SEOModel extends Model
{
    public function init()
    {
        $this->setProcessor(Config::instance()->cacheEnabled ? new SEOCacheProcessor() : new SEODBProcessor());
    }

    public static function myClassName()
    {
        return __CLASS__;
    }

    public function getSEOSettings()
    {
        return $this->getProcessor()->getSEOSettings();
    }

    public function updateSEO($seo)
    {
        return $this->getProcessor()->updateSEO($seo);   
    }
}