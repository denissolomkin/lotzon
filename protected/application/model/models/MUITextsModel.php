<?php

Application::import(PATH_APPLICATION . 'model/Model.php');
Application::import(PATH_APPLICATION . 'model/entities/MUIText.php');
Application::import(PATH_APPLICATION . 'model/processors/MUITextsDBProcessor.php');
Application::import(PATH_APPLICATION . 'model/processors/MUITextsCacheProcessor.php');

class StaticTextsModel extends Model
{
    private $_lang = '';
    private $_list = '';

    public function init()
    {
        //$this->setProcessor(Config::instance()->cacheEnabled ? new StaticSiteTextCacheProcessor() : new StaticSiteTextDBProcessor());
        $this->setProcessor(new StaticTextDBProcessor());
    }

    public static function myClassName()
    {
        return __CLASS__;
    }

    public function setLang($lang=null)
    {
        $this->_lang = $lang?:\CountriesModel::instance()->defaultLang();
        return $this;
    }

    public function getText($key, $args=null)
    {
        if(!$this->_list)
            $this->_list=$this->getList();

        if(!$this->_lang)
            $this->setLang();

        if (isset($this->_list[$key])){
            $text = $this->_list[$key]->getText($this->_lang);
        } else
            $text = $key;

        if(isset($args)){
            $text=call_user_func_array('sprintf', array_merge((array)(str_replace(array('{0}','{s}'),array('%d','%s'),$text)), (array)$args));
        }

        if(\Session2::connect()->get(\Admin::SESSION_VAR) && \SEOModel::instance()->getSEOSettings()['debug']){
            $text="<div class='debug text-trigger' data-key='{$key}'>{$text}</div>";
        }

        return $text;
    }

    public function getList()
    {

        if(!$this->_list)
            $this->_list=$this->getProcessor()->getList();
        return $this->_list;
    }

    public function getCategory($category=null)
    {
        $list=$this->getProcessor()->getList();
        if($category)
            if (isset($list[$category]))
                return $list[$category];

        else
            return false;
    }
}