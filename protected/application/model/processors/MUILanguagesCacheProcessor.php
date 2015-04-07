<?php

Application::import(PATH_INTERFACES . 'IProcessor.php');
Application::import(PATH_APPLICATION . 'model/processors/BaseCacheProcessor.php');
Application::import(PATH_APPLICATION . 'model/processors/MUILanguagesDBProcessor.php');

class LanguagesCacheProcessor extends BaseCacheProcessor implements IProcessor
{

    const LIST_CACHE_KEY = "languages::list";

    public function init()
    {
        $this->setBackendProcessor(new LanguagesDBProcessor());
    }

    public function create(Entity $language)
    {
        $language = $this->getBackendProcessor()->create($language);
        $this->getList(true);
        return $language;
    }

    public function getList($recache=false)
    {
        if (($list = Cache::init()->get(self::LIST_CACHE_KEY)) === false OR $recache) {
            $list = $this->getBackendProcessor()->getList();

            if (!Cache::init()->set(self::LIST_CACHE_KEY , $list)) {
                throw new ModelException("Unable to cache storage data", 500);
            }
        }
        return $list;
    }


    public function update(Entity $language) {
    }

    public function fetch(Entity $language) {

        $list = $this->getList();
        if(isset($list[$language->getId()]) && $language->formatFrom('CLASS',$list[$language->getId()])) {
            return $language;
        } elseif(is_array($list) && !empty($list) && $language->formatFrom('CLASS',reset($list))) {
            return $language;
        } else
            throw new ModelException("language not found", 404);
    }

    public function delete(Entity $language) {
    }
}