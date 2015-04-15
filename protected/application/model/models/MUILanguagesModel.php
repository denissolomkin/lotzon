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
                $language->formatForm('CLASS',$this->getDefault());
            else
                throw new EntityException("Model Error", 500);
        }

        return $language;
    }

    public function isLang($code=null)
    {
        return isset($this->getList()[$code]);
    }


    public function defaultLang()
    {
        if(is_array($languages=$this->getList()) && !empty($languages)) {
            if ($default = (reset($languages)))
                return $default->getCode();
        } else {
            return $this->getDefault()->getCode();
        }
    }

    public function getDefault(){

        $default = new Language;
        $default->setId(1)
            ->setCode('RU')
            ->setTitle('русский');
        return $default;
    }
}
