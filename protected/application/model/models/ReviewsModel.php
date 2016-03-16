<?php

Application::import(PATH_APPLICATION . 'model/Model.php');
Application::import(PATH_APPLICATION . 'model/entities/Review.php');
Application::import(PATH_APPLICATION . 'model/processors/ReviewsDBProcessor.php');
Application::import(PATH_APPLICATION . 'model/processors/ReviewsCacheProcessor.php');


class ReviewsModel extends Model
{
    public function init()
    {
        //$this->setProcessor(Config::instance()->cacheEnabled ? new ReviewsCacheProcessor() : new ReviewsDBProcessor());
        $this->setProcessor(new ReviewsDBProcessor());
    }

    public static function myClassName()
    {
        return __CLASS__;
    }

    public function getList($limit = null, $offset = null, $args = array('Status' => 1, 'ParentId' => 'null')) {
        $list = $this->getProcessor()->getList($limit, $offset, $args);
        return $list;
    }

    public function getReview($id) {
        return $this->getProcessor()->getReview($id);
    }

    public function imageExists($image) {
        return $this->getProcessor()->imageExists($image);
    }

    public function getCount($args = array()) {
        return $this->getProcessor()->getCount($args);
    } 
}