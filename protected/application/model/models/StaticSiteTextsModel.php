<?php

Application::import(PATH_APPLICATION . 'model/Model.php');
Application::import(PATH_APPLICATION . 'model/entities/StaticSiteText.php');
Application::import(PATH_APPLICATION . 'model/processors/StaticSiteTextDBProcessor.php');
Application::import(PATH_APPLICATION . 'model/processors/StaticSiteTextCacheProcessor.php');


class StaticSiteTextsModel extends Model
{
    public function init()
    {
        //$this->setProcessor(Config::instance()->cacheEnabled ? new StaticSiteTextCacheProcessor() : new StaticSiteTextDBProcessor());
        $this->setProcessor(new StaticSiteTextDBProcessor());
    }

    public static function myClassName()
    {
        return __CLASS__;
    }

    public function getListGroupedByLang()
    {
        $data = array();

        $list = $this->getProcessor()->getList();    
        
        foreach ($list as $text) {
            if (!isset($data[$text->getLang()])) {
                $data[$text->getLang()] = array();
            }
            $data[$text->getLang()][$text->getId()] = $text;
        }

        return $data;
    }

    public function getListGroupedByIdentifier()
    {
        $data = array();

        $list = $this->getProcessor()->getList();    
        
        foreach ($list as $text) {
            if (!isset($data[$text->getId()])) {
                $data[$text->getId()] = array();
            }
            $data[$text->getId()][$text->getlang()] = $text;
        }

        return $data;   
    }
}