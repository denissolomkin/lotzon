<?php

Application::import(PATH_APPLICATION . 'model/Model.php');
Application::import(PATH_APPLICATION . 'model/entities/MUILanguage.php');
Application::import(PATH_APPLICATION . 'model/processors/MUILanguagesDBProcessor.php');
Application::import(PATH_APPLICATION . 'model/processors/MUILanguagesCacheProcessor.php');

class LanguagesModel extends Model
{
    public function init()
    {
        parent::init();

        $this->setProcessor(Config::instance()->cacheEnabled ? new LanguagesCacheProcessor() : new LanguagesDBProcessor());
    }

    public static function myClassName()
    {
        return __CLASS__;
    }

    public function getList()
    {
        return $this->getProcessor()->getList();
    }

    public function fetch(Entity $language) {

        try {
            $language = $this->getProcessor()->fetch($language);
        } catch (ModelException $e) {

            if($e->getCode()=='404')
                $language->setId(1)
                    ->setCode('RU')
                    ->setTitle('Русский');
            else
                throw new EntityException("Model Error", 500);
        }

        return $language;
    }

}
