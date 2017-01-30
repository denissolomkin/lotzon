<?php

namespace controllers\production;
use \Application, \SettingsModel, \Player, \EntityException, \LotteryTicket, \CountriesModel, \TicketsModel, \LotterySettings;

Application::import(PATH_APPLICATION . 'model/entities/Player.php');
Application::import(PATH_APPLICATION . 'model/entities/LotteryTicket.php');
Application::import(PATH_CONTROLLERS . 'production/AjaxController.php');

class LotteryController extends \AjaxController
{
    static $ticketsCount;
    static $defaultCountry;

    public function init()
    {
        self::$ticketsCount     = LotterySettings::TOTAL_TICKETS;
        self::$defaultCountry   = CountriesModel::instance()->defaultCountry();

        parent::init();

        $this->validateRequest();
        $this->authorizedOnly(true);
        $this->validateLogout();
        $this->validateCaptcha();
    }

    public function createTicketAction()
    {
        $ticket = new LotteryTicket();
        $ticket->setPlayerId($this->player->getId());
        $ticket->setCombination($this->request()->post('combination'));
        $ticket->setTicketNum($this->request()->post('tickNum'));
        $ticket->setIsGold($this->request()->post('isGold',0)=="true"?1:0);

        if (! \TicketsModel::instance()->isAvailableTicket($ticket->getPlayerId(), $ticket->getTicketNum())) {
            $res = array(
                "message" => "TICKET_NOT_AVAILABLE",
                "tickets" => array(
                    "filledTickets" => \TicketsModel::instance()->getUnplayedTickets($ticket->getPlayerId())
                )
            );
            $this->ajaxResponseNoCache($res, 403);
            return false;
        }

        if (\TicketsModel::instance()->getUnplayedTickets($ticket->getPlayerId())[$ticket->getTicketNum()] !== null) {
            $res = array(
                "message" => "TICKET_ALREADY_FILLED",
                "tickets" => array(
                    "filledTickets" => \TicketsModel::instance()->getUnplayedTickets($ticket->getPlayerId())
                )
            );
            $this->ajaxResponseNoCache($res, 403);
            return false;
        }

        if ($ticket->getIsGold()==true) {
            $player = new Player;
            $player->setId($ticket->getPlayerId())->fetch();
            if ($player->getGoldTicket()<1) {
                $res = array(
                    "message" => "TICKET_NOT_BOUGHT",
                    "tickets" => array(
                        "filledTickets" => \TicketsModel::instance()->getUnplayedTickets($ticket->getPlayerId())
                    )
                );
                $this->ajaxResponseNoCache($res, 402);
                return false;
            }
        }

        try {
            $ticket->beginTransaction();
            if ($ticket->getIsGold()==true) {
                $player = new Player;
                $player->setId($ticket->getPlayerId())->fetch();
                \PlayersModel::instance()->updateGoldTicket($player, -1);
            }
            $gifts = \GiftsModel::instance()->getList($this->player->getId(),'Ticket',7,false,time());
            if (($ticket->getTicketNum()==7)and(count($gifts)>0)) {
                array_shift($gifts)->setUsed(true)->update();
            }
            $ticket->create();
            $ticket->commit();
        } catch (EntityException $e) {
            $ticket->rollBack();
            $this->ajaxResponseInternalError($e->getMessage());
        }

        $res = array(
            'captcha' => $this->player->activateCaptcha(),
            'tickets' => array(
                'filledTickets' => \TicketsModel::instance()->getUnplayedTickets($ticket->getPlayerId())
            )
        );

        $this->ajaxResponseNoCache($res);
    }

    public function buyGoldTicketAction()
    {
        $price    = $this->request()->post('price');
        $currency = $this->request()->post('currency', 'money');

        $player = new Player;
        $player->setId($this->player->getId())->fetch();

        if (($player->getGoldTicket()>0)or(\TicketsModel::instance()->getUnplayedTickets($player->getId())[8]!==false)) {
            $this->ajaxResponseNoCache(array("message"=>"ALREADY_BOUGHT"),400);
        }

        $ticket = new LotteryTicket();

        if ($currency!='points') {
            $country = (
            CountriesModel::instance()->isCountry($player->getCurrency())
                ? $this->session->get(Player::IDENTITY)->getCurrency()
                : CountriesModel::instance()->defaultCountry());

            $goldPrice = SettingsModel::instance()->getSettings('goldPrice')->getValue($country);

            if ($goldPrice != $price) {
                $this->ajaxResponseNoCache(array("message" => "PRICE_NOT_SAME"), 403);
            }

            try {
                $ticket->beginTransaction();
                $money = \PlayersModel::instance()->getBalance($player, true)['Money'];
                if ($money < $goldPrice) {
                    throw new \Exception();
                }

                \PlayersModel::instance()->updateGoldTicket($player, 1, \LotteriesModel::instance()->getLastPublishedLottery()->getId()+1);

                $player->addMoney(
                    (0 - $goldPrice),
                    array('title'=>'Покупка золотого билета','type'=>'Gold')
                );

                $ticket->commit();
            } catch (\Exception $e) {
                $ticket->rollBack();
                $res = array(
                    "message" => "MONEY_NO_ENOUGH",
                    "tickets" => array(
                        "filledTickets" => \TicketsModel::instance()->getUnplayedTickets($player->getId())
                    )
                );
                $this->ajaxResponseNoCache($res, 402);
            } catch (EntityException $e) {
                $ticket->rollBack();
                $this->ajaxResponseInternalError();
            }
        } else {
            $goldPrice = SettingsModel::instance()->getSettings('goldPrice')->getValue('POINTS');

            if ($goldPrice != $price) {
                $this->ajaxResponseNoCache(array("message" => "PRICE_NOT_SAME"), 403);
            }

            try {
                $ticket->beginTransaction();
                $money = \PlayersModel::instance()->getBalance($player, true)['Points'];
                if ($money < $goldPrice) {
                    throw new \Exception();
                }

                \PlayersModel::instance()->updateGoldTicket($player, 1, \LotteriesModel::instance()->getLastPublishedLottery()->getId()+1);

                $player->addPoints(
                    (0 - $goldPrice),
                    array('title'=>'Покупка золотого билета','type'=>'Gold')
                );

                $ticket->commit();
            } catch (\Exception $e) {
                $ticket->rollBack();
                $res = array(
                    "message" => "POINTS_NO_ENOUGH",
                    "tickets" => array(
                        "filledTickets" => \TicketsModel::instance()->getUnplayedTickets($player->getId())
                    )
                );
                $this->ajaxResponseNoCache($res, 402);
            } catch (EntityException $e) {
                $ticket->rollBack();
                $this->ajaxResponseInternalError();
            }
        }

        $player->fetch();
        $res = array(
            "tickets" => array(
                "filledTickets" => \TicketsModel::instance()->getUnplayedTickets($player->getId())
            ),
            "player" => array(
                "balance" => array(
                    "money"  => $player->getMoney(),
                    "points" => $player->getPoints(),
                ),
            ),
        );

        $this->ajaxResponseNoCache($res);
    }

    public function lotteryInfoAction($lotteryId)
    {

        $type = $this->request()->get('type', false);

        $player = new \Player;
        $player_currency = $player->setId($this->player->getId())->fetch()->getCurrency();

        try {
            $lottery = \LotteriesModel::instance()->getLotteryDetails($lotteryId);
            if ($lottery == array()) {
                throw new \ModelException("LOTTERY_NOT_FOUND", 404);
            }
            $prizes      = $lottery->getPrizes();
            $prizes_gold = $lottery->getPrizesGold();
            if (!isset($prizes[$player_currency])) {
                $prizes      = $prizes[self::$defaultCountry];
                $prizes_gold = $prizes_gold[self::$defaultCountry];
            } else {
                $prizes      = $prizes[$player_currency];
                $prizes_gold = $prizes_gold[$player_currency];
            }

            $balls          = $lottery->getBallsTotal();
            $balls_incr     = $lottery->getBallsTotalIncr();
            $ballsGold_incr = $lottery->getBallsTotalGoldIncr();

            $response                                             = array(
                'cache' => 'session',
            );
            $response['res']                                      = array(
                "lottery" => array(
                    $lotteryId => $lottery->exportTo('list'),
                ),
            );
            $response['res']['lottery'][$lotteryId]['statistics'] = array(
                "default" => array(),
                "gold"    => array(),
            );

            foreach ($balls as $count => $matches) {
                $response['res']['lottery'][$lotteryId]['statistics']["default"][$count] = array(
                    'balls'    => $count,
                    'currency' => $prizes[$count]['currency'],
                    'sum'      => $prizes[$count]['sum'],
                    'matches'  => ($lotteryId>=495?$matches + $balls_incr[$count]:null),
                );
                $response['res']['lottery'][$lotteryId]['statistics']["gold"][$count]    = array(
                    'balls'    => $count,
                    'currency' => $prizes_gold[$count]['currency'],
                    'sum'      => $prizes_gold[$count]['sum'],
                    'matches'  => ($lotteryId>=495?$ballsGold_incr[$count]:null),
                );
            }

            if ($type != "mine") {
                $response['res']['lottery'][$lotteryId]['nextId'] =  \LotteriesModel::instance()->getDependentLotteryId($lotteryId,'next');
                $response['res']['lottery'][$lotteryId]['prevId'] =  \LotteriesModel::instance()->getDependentLotteryId($lotteryId,'prev');
            } else {
                $response['res']['lottery'][$lotteryId]['nextId'] =  \LotteriesModel::instance()->getDependentLotteryId($lotteryId,'next', $player->getId());
                $response['res']['lottery'][$lotteryId]['prevId'] =  \LotteriesModel::instance()->getDependentLotteryId($lotteryId,'prev', $player->getId());
                $response['res']['lottery'][$lotteryId]['type']   = 'mine';
            }



        } catch (\ModelException $e) {
            $this->ajaxResponseInternalError();

            return false;
        }

        $this->ajaxResponseNoCache($response);
        return true;
    }

    public function lotteryTicketsAction($lotteryId)
    {

        $player = new Player;
        $player->setId($this->player->getId())->fetch();

        try {
            $list = \TicketsModel::instance()->getPlayerTickets($player, $lotteryId);
        } catch (\PDOException $e) {
            $this->ajaxResponseInternalError();
            return false;
        }

        $response = array(
            'cache' =>  'session',
            'res' => array(
                "lottery" => array(
                    $lotteryId => array(
                        "tickets" => array(),
                    ),
                ),
            ),
        );

        if (!is_null($list)) {
            foreach ($list as $id => $ticket) {
                $response['res']["lottery"][$lotteryId]["tickets"][$ticket->getTicketNum()] = $ticket->getCombination();
            }
        }

        $this->ajaxResponseNoCache($response);
        return true;
    }

    public function ticketsAction()
    {

        $player = new Player;
        $player->setId($this->player->getId())->fetch();

        $response = array(
            "player" => array(
                "balance"  => array(
                    "points" => $player->getPoints(),
                    "money"  => $player->getMoney(),
                ),
                "referral" => array(
                    'total'  => \PlayersModel::instance()->getReferralsCount($player->getId()),
                    'profit' => $player->getReferralsProfit()
                ),
            ),
            "tickets" => array(
                "lastLotteryId" => \LotteriesModel::instance()->getLastPublishedLottery()->getId(),
                "timeToLottery" => \LotterySettingsModel::instance()->loadSettings()->getNearestGame() + strtotime('00:00:00', time()) - time(),
                "filledTickets" => \TicketsModel::instance()->getUnplayedTickets($player->getId()),
            ),
        );

        $this->ajaxResponseNoCache($response);
        return true;
    }

    public function captchaCloseAction()
    {
        $player = new Player;
        $player->setId($this->player->getId())->fetch();

        $player->setDates(time(), 'CaptchaNotification')->updateDate('CaptchaNotification');

        $response = array(
            "player" => array(
                "dates"  => array(
                    'captchaNotification' => $player->getDates('CaptchaNotification'),
                ),
            ),
        );

        $this->ajaxResponseNoCache($response);
        return true;
    }

    public function captchaAction()
    {
        $player = new Player;
        $player->setId($this->player->getId())->fetch();

        $post_info = $this->request()->post('moneycaptcha_code', false);

        $ok = false;
        if ($post_info!==false) {
            $handle = curl_init();
            curl_setopt($handle, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($handle, CURLOPT_URL, "https://moneycaptcha.ru/valid.php?code=" . $post_info);
            curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);
            $status = curl_exec($handle);
            if ($status !== false) {
                curl_close($handle);
                $xml = simplexml_load_string($status);
                if($xml->code == "1"){
                    $ok = true;
                }
            }
        }

        if ($ok === true) {
            $gift = new \Gift();
            $gift->setPlayerId($this->player->getId())
                ->setObjectType('Ticket')
                ->setObjectId(7)
                ->setExpiryDate(\LotterySettingsModel::instance()->loadSettings()->getNearestGame()+strtotime('00:00:00', time()))
                ->setDescription('Captcha')
                ->setUsed(false)
                ->create();
            $player->setDates(time(), 'Captcha')->updateDate('Captcha');
        }

        $response = array(
            "player" => array(
                "dates"  => array(
                    'captcha' => $player->getDates('Captcha'),
                ),
            ),
            "tickets" => array(
                "filledTickets" => \TicketsModel::instance()->getUnplayedTickets($player->getId()),
            ),
        );

        $this->ajaxResponseNoCache($response);
        return true;
    }

}
