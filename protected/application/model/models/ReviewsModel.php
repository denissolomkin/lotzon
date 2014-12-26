<?php

Application::import(PATH_APPLICATION . 'model/Model.php');
Application::import(PATH_APPLICATION . 'model/entities/Review.php');
Application::import(PATH_APPLICATION . 'model/processors/ReviewsDBProcessor.php');
Application::import(PATH_APPLICATION . 'model/processors/ReviewsCacheProcessor.php');


class ReviewsModel extends Model
{
    public function init()
    {
        $this->setProcessor(Config::instance()->cacheEnabled ? new ReviewsCacheProcessor() : new ReviewsDBProcessor());
    }

    public static function myClassName()
    {
        return __CLASS__;
    }

    public function getList($status=1, $limit = null, $offset = null) {
        return $this->getProcessor()->getList($status, $limit, $offset);
    }

    public function imageExists($image) {
        return $this->getProcessor()->imageExists($image);
    }

    public function getCount($status=1) {
        return $this->getProcessor()->getCount($status);
    } 
}