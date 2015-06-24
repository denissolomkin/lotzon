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

    public function getList($status=1, $limit = null, $offset = null, $ignore = false, $json = false) {

        $list = $this->getProcessor()->getList($status, $limit, $offset, $ignore);

        if($json){
            while ($reviewData = array_pop($list))
                foreach ($reviewData as $reviewItem) {
                    $responseData[] = array(
                        'id' => $reviewItem->getReviewId()?:$reviewItem->getId(),
                        'date' => date('d.m.Y H:i', $reviewItem->getDate()+\SettingsModel::instance()->getSettings('counters')->getValue('HOURS_ADD')*3600),
                        'playerId' => $reviewItem->getPlayerId(),
                        'playerAvatar' => $reviewItem->getPlayerAvatar(),
                        'playerName' => $reviewItem->getPlayerName(),
                        'text' => $reviewItem->getText(),
                        'image' => $reviewItem->getImage(),
                        'answer' => $reviewItem->getReviewId(),
                    );
                }

            $list = $responseData;
        }

        return $list;
    }

    public function getReview($id) {
        return $this->getProcessor()->getReview($id);
    }

    public function imageExists($image) {
        return $this->getProcessor()->imageExists($image);
    }

    public function getCount($status=1) {
        return $this->getProcessor()->getCount($status);
    } 
}