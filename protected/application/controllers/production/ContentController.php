<?php

namespace controllers\production;
use \Application, \Config, \Player, \EntityException, \Session2, \LotteryTicket, \LotteriesModel, \ShopModel, \NewsModel, \GameSettings, \ModelException, \TransactionsModel, \Common;

Application::import(PATH_APPLICATION . 'model/entities/Player.php');
Application::import(PATH_APPLICATION . 'model/entities/LotteryTicket.php');
Application::import(PATH_CONTROLLERS . 'production/AjaxController.php');

class ContentController extends \AjaxController
{
    public function init()
    {
        parent::init();
        if ($this->validRequest()) {
            if (!Session2::connect()->get(Player::IDENTITY) instanceof PLayer) {
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
                $playerLotteries = LotteriesModel::instance()->getPlayerPlayedLotteries(Session2::connect()->get(Player::IDENTITY)->getId());
                foreach ($playerLotteries as $lottery) {
                    if (isset($lotteries[$lottery->getId()])) {
                        $lotteries[$lottery->getId()]->playerPlayed = true;
                    }
                }
            } else {
                $lotteries = LotteriesModel::instance()->getPlayerPlayedLotteries(Session2::connect()->get(Player::IDENTITY)->getId(), Index::LOTTERIES_PER_PAGE, $offset);
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
            if (is_array($item->getCountries()) and !in_array(Session2::connect()->get(Player::IDENTITY)->getCountry(),$item->getCountries())) {
                continue;
            }

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
                'price'    => number_format($item->getPrice(), 0, '.', ' '),
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

        $news = NewsModel::instance()->getList(Session2::connect()->get(Player::IDENTITY)->getCountry(), Index::NEWS_PER_PAGE, $offset);
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
            $responseData['keepButtonShow'] = true;
        }

        $this->ajaxResponse($responseData);
    }

    public function lotteryDetailsAction($lotteryId)
    {
        if (!$lotteryId) {
            $this->ajaxResponse(array(), 0, 'EMPTY_LOTTERY_ID');
        }
        try {
            $lotteryDetails = LotteriesModel::instance()->getLotteryDetails($lotteryId);
        } catch (ModelException $e) {

            $this->ajaxResponse(array(), 0, $e->getCode() . ':INTERNAL_ERROR' . $e->getMessage());
        }

        $responseData = array(
            'lottery' => array(
                'id'  => $lotteryDetails['lottery']->getId(),
                'combination'  => $lotteryDetails['lottery']->getCombination(),
                'date'  => $lotteryDetails['lottery']->getDate('d.m.Y'),
            ),
            'winners' => array(),
            'tickets' => array(),
        );

        $langs = array();
        foreach ($lotteryDetails['winners'] as $player) {
            $responseData['winners'][] = array(
                'id'      => $player->getId(),
                'name'    => $player->getVisibility() ? $player->getName() : '',
                'surname' => $player->getVisibility() ? $player->getSurname() : '',
                'nick'    => $player->getVisibility() ? $player->getNicName() : 'id'.$player->getId(),
                'avatar'  => $player->getVisibility() ? ($player->getAvatar() ? '/filestorage/avatars/' .ceil($player->getId() / 100) . '/' . $player->getAvatar() : '') : '',
                'you'     => $player->getId() == Session2::connect()->get(Player::IDENTITY)->getId(),
            );
        }

        foreach ($lotteryDetails['tickets'] as $playerId => $ticketData) {
            $response['tickets'][$playerId] = array();
            foreach ($ticketData as $ticket) {
                $responseData['tickets'][$playerId][$ticket->getTicketNum()] = array(
                    'combination' => $ticket->getCombination(),
                    'win' => $ticket->getTicketWin() > 0 ? Common::viewNumberFormat($ticket->getTicketWin()) . " " . ($ticket->getTicketWinCurrency() == GameSettings::CURRENCY_POINT ? 'баллов' : Config::instance()->langCurrencies[Session2::connect()->get(Player::IDENTITY)->getCountry()]) : '',
                );
            }
        }

        $this->ajaxResponse($responseData);
    }

    public function nextLotteryDetailsAction($lotteryId)
    {
        if (!$lotteryId) {
            $this->ajaxResponse(array(), 0, 'EMPTY_LOTTERY_ID');
        }
        try {
            $nextLottery = LotteriesModel::instance()->getDependentLottery($lotteryId, 'next');
        } catch (ModelException $e) {
            // be realllly dirty
            $nextLottery = LotteriesModel::instance()->getDependentLottery(1000000/*;)*/, 'next');
        }


        if ($nextLottery) {
            return $this->lotteryDetailsAction($nextLottery->getId());
        }
        $this->ajaxResponse(array(),0, 'NOT_FOUND');
    }

    public function prevLotteryDetailsAction($lotteryId)
    {
        if (!$lotteryId) {
            $this->ajaxResponse(array(), 0, 'EMPTY_LOTTERY_ID');
        }
        try {
            $nextLottery = LotteriesModel::instance()->getDependentLottery($lotteryId, 'prev');
        } catch (ModelException $e) {
            // be dirty
            $nextLottery = LotteriesModel::instance()->getDependentLottery(0, 'prev');
        }

        if ($nextLottery) {
            return $this->lotteryDetailsAction($nextLottery->getId());
        }
        $this->ajaxResponse(array(),0, 'NOT_FOUND');
    }

    public function transactionsAction($currency)
    {
        $offset = (int)$this->request()->get('offset');

        if ($currency == GameSettings::CURRENCY_POINT) {
            $transactions = TransactionsModel::instance()->playerPointsHistory(Session2::connect()->get(Player::IDENTITY)->getId(), Index::TRANSACTIONS_PER_PAGE, $offset);
        }
        if ($currency == GameSettings::CURRENCY_MONEY) {
            $transactions = TransactionsModel::instance()->playerMoneyHistory(Session2::connect()->get(Player::IDENTITY)->getId(), Index::TRANSACTIONS_PER_PAGE, $offset);
        }
        $jsonTransactions = array();
        foreach ($transactions as $transaction) {
            $jsonTransactions[] = array(
                'description' => $transaction->getDescription(),
                'quantity' => ($transaction->getSum() > 0 ? '+' : '') . ($transaction->getSum() == 0 ? '' : Common::viewNumberFormat($transaction->getSum())),
                'date'  => date('d.m.Y', $transaction->getDate()),
            );
        }

        $this->ajaxResponse($jsonTransactions);
    }
}