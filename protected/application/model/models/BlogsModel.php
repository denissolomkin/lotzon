<?php

Application::import(PATH_APPLICATION . 'model/entities/Blog.php');
Application::import(PATH_APPLICATION . 'model/processors/BlogsDBProcessor.php');

class BlogsModel extends Model
{
    public function init()
    {
        $this->setProcessor(new BlogsDBProcessor());
    }

    public static function myClassName()
    {
        return __CLASS__;
    }

    public function getList($lang, $count = null, $beforeId = null, $afterId = null, $enable = 1, $offset = null, $modifyDate = null)
    {
        return $this->getProcessor()->getList($lang, $count, $beforeId, $afterId, $enable, $offset, $modifyDate);
    }

    public function getSimilarList($blogId, $lang, $count, $beforeId = null, $afterId = null, $enable = 1)
    {
        return $this->getProcessor()->getSimilarList($blogId, $lang, $count, $beforeId, $afterId, $enable);
    }

    public function getCount($lang)
    {
        return $this->getProcessor()->getCount($lang);
    }

    public function addSimilar($blogId, $similarId)
    {
        return $this->getProcessor()->addSimilar($blogId, $similarId);
    }

    public function removeSimilars($blogId)
    {
        return $this->getProcessor()->removeSimilars($blogId);
    }
}
