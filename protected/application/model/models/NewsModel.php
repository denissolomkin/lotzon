<?php

Application::import(PATH_APPLICATION . 'model/Model.php');
Application::import(PATH_APPLICATION . 'model/entities/News.php');
Application::import(PATH_APPLICATION . 'model/processors/NewsDBProcessor.php');
Application::import(PATH_APPLICATION . 'model/processors/NewsCacheProcessor.php');


class NewsModel extends Model
{
    public function init()
    {
        //$this->setProcessor(Config::instance()->cacheEnabled ? new NewsCacheProcessor() : new NewsDBProcessor());
        $this->setProcessor(new NewsDBProcessor());
    }

    public static function myClassName()
    {
        return __CLASS__;
    }

    public function getList($lang, $limit = null, $offset = null) {
        return $this->getProcessor()->getList($lang, $limit, $offset);
    }

    public function getCount($lang) {
        return $this->getProcessor()->getCount($lang);
    } 
}