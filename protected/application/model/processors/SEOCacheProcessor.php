<?php

Application::import(PATH_APPLICATION . 'model/processors/BaseCacheProcessor.php');
Application::import(PATH_APPLICATION . 'model/processors/SEODBProcessor.php');

class SEOCacheProcessor extends BaseCacheProcessor
{

    const CACHE_KEY = "seo::settings";

    public function init()
    {
        $this->setBackendProcessor(new SEODBProcessor());
    }

    public function getSEOSettings()
    {
        if (($seo = Cache::init()->get(self::CACHE_KEY)) === false) {

            $seo = $this->getBackendProcessor()->getSEOSettings();

            if (!Cache::init()->set(self::CACHE_KEY, $seo)) {
                throw new ModelException("Unable to cache storage data", 500);
            }
        }

        return $seo;
    }

    public function updateSEO($seo)
    {
        $seo = $this->getBackendProcessor()->updateSEO($seo);

        if (!Cache::init()->set(self::CACHE_KEY, $seo)) {
            throw new ModelException("Unable to cache storage data", 500);
        }

        return $seo;
    }

}