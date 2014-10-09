<?php

namespace controllers\production;
use \Application, \Config, \Player, \EntityException, \Session, \LotteryTicket, \LotteriesModel, \ShopModel, \NewsModel;

Application::import(PATH_APPLICATION . 'model/entities/Player.php');
Application::import(PATH_APPLICATION . 'model/entities/LotteryTicket.php');
Application::import(PATH_CONTROLLERS . 'production/AjaxController.php');

class ContentController extends \AjaxController
{
    public function init()
    {
        parent::init();
        if ($this->validRequest()) {
            if (!Session::connect()->get(Player::IDENTITY) instanceof PLayer) {
                $this->ajaxResponse(array(), 0, 'NOT_AUTHORIZED');
            }    
        }
    }

    public function lotteriesAction() 
    {
        $offset = $this->request()->get('offset');
        $onlyMine = $this->request()->get('onlyMine', false);
        try {

            if (!$onlyMine) {
                $lotteries = LotteriesModel::instance()->getPublishedLotteriesList(Index::LOTTERIES_PER_PAGE, $offset);
                $playerLotteries = LotteriesModel::instance()->getPlayerPlayedLotteries(Session::connect()->get(Player::IDENTITY)->getId());
                foreach ($playerLotteries as $lottery) {
                    if (isset($lotteries[$lottery->getId()])) {
                        $lotteries[$lottery->getId()]->playerPlayed = true;
                    }
                }
            } else {
                $lotteries = LotteriesModel::instance()->getPlayerPlayedLotteries(Session::connect()->get(Player::IDENTITY)->getId(), Index::LOTTERIES_PER_PAGE, $offset);    
            }
            
        } catch (EntityException $e) {
            $this->ajaxResponse(array(), 0, $e->getMessage());
        }
        $response = array(
            'lotteries'      => array(),
            'keepButtonShow' => true,
        );
        if (count($lotteries)) {
            foreach ($lotteries as $lottery) {
                $response['lotteries'][] = array(
                    'id'           => $lottery->getId(),
                    'date'         => $lottery->getDate('d.m.Y'),
                    'combination'  => $lottery->getCombination(),
                    'winnersCount' => $lottery->getWinnersCount(),
                    'iPlayed'      => $lottery->playerPlayed,
                );
            }
        }
        if (count($lotteries) < Index::LOTTERIES_PER_PAGE) {
            $response['keepButtonShow'] = false;
        }
        $response['offset'] = $offset;
        $response['onlyMine'] = $onlyMine;
        $this->ajaxResponse($response);
    }

    public function shopAction() 
    {
        $offset = (int)$this->request()->get('offset');
        $category = (int)$this->request()->get('category');

        $shop = ShopModel::instance()->loadShop();

        if (empty($shop[$category])) {
            $this->ajaxResponse(array(), 0, 'INVALID_CATEGORY');
        }
        $items = array();
        $i = 0;
        foreach ($shop[$category]->getItems() as $item) {
            if ($i < $offset) {
                $i++;
                continue;
            }
            if (count($items) >= Index::SHOP_PER_PAGE) {
                break;
            } 
            $items[] = array(
                'id'       => $item->getId(),
                'title'    => $item->getTitle(),
                'price'    => $item->getPrice(),
                'quantity' => $item->getQuantity(),
                'img'      => $item->getImage(),
            );
        }

        $data = array(
            'category' => $category,
            'items'    => $items,
            'keepButtonShow' => count($items) >= Index::SHOP_PER_PAGE,
        );

        $this->ajaxResponse($data);
    }

    public function newsAction()
    {
        $offset = (int)$this->request()->get('offset');

        $news = NewsModel::instance()->getList(Session::connect()->get(Player::IDENTITY)->getCountry(), Index::NEWS_PER_PAGE, $offset);
        $responseData = array(
            'news'           => array(),
            'keepButtonShow' => false,
        );

        foreach ($news as $newsItem) {
            $responseData['news'][] = array(
                'date'  => date('d.m.Y', $newsItem->getDate()),
                'title' => $newsItem->getTitle(),
                'text'  => $newsItem->getText(),
            );
        }

        if (count($news) >= Index::NEWS_PER_PAGE) {
            $response['keepButtonShow'] = true;
        }

        $this->ajaxResponse($responseData);
    }
}