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
        if (($seo = Cache::init()->get(self::CACHE_KEY)) === false || !isset($seo['Pages'])) {
            $seo = $this->recache();
        }

        /* todo delete after first use */
        if (!isset($seo['Pages'])) {
            $seo = $this->recache();
        }

        return $seo;
    }

    public function updateSEO($seo)
    {
        $seo = $this->recache($seo);
        return $seo;
    }

    public function recache($seo = null)
    {
        $seo = $seo
            ? $this->getBackendProcessor()->updateSEO($seo)
            : $this->getBackendProcessor()->getSEOSettings();

        if (!Cache::init()->set(self::CACHE_KEY, $seo)) {
            throw new ModelException("Unable to cache storage data", 500);
        }

        return $seo;
    }

}