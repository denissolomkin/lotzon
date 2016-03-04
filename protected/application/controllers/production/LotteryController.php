<?php

namespace controllers\production;
use \Application, \SettingsModel, \Player, \EntityException, \LotteryTicket, \CountriesModel, \TicketsModel;
use Ratchet\Wamp\Exception;
use Symfony\Component\HttpFoundation\Session\Session;

Application::import(PATH_APPLICATION . 'model/entities/Player.php');
Application::import(PATH_APPLICATION . 'model/entities/LotteryTicket.php');
Application::import(PATH_CONTROLLERS . 'production/AjaxController.php');

class LotteryController extends \AjaxController
{
    private $session;

    static $lotteriesPerPage;
    static $ticketsCount;
    static $defaultCountry;

    public function init()
    {
        self::$lotteriesPerPage = (int)SettingsModel::instance()->getSettings('counters')->getValue('LOTTERIES_PER_PAGE') ? : 10;
        self::$ticketsCount     = \LotterySettings::TOTAL_TICKETS;
        self::$defaultCountry   = CountriesModel::instance()->defaultCountry();
        $this->session = new Session();
        parent::init();
        if ($this->validRequest()) {
            if (!$this->session->get(Player::IDENTITY) instanceof Player) {
                $this->ajaxResponseUnauthorized();
                return false;
            }
            $this->session->get(Player::IDENTITY)->markOnline();
        }
    }

    private function authorizedOnly()
    {
        if (!$this->session->get(Player::IDENTITY) instanceof Player) {
            $this->ajaxResponseUnauthorized();
            return false;
        }
        $this->session->get(Player::IDENTITY)->markOnline();
        return true;
    }

    public function createTicketAction()
    {
        $ticket = new LotteryTicket();
        $ticket->setPlayerId($this->session->get(Player::IDENTITY)->getId());
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
            $this->ajaxResponseCode($res, 403);
            return false;
        }

        if (\TicketsModel::instance()->getUnplayedTickets($ticket->getPlayerId())[$ticket->getTicketNum()] !== null) {
            $res = array(
                "message" => "TICKET_ALREADY_FILLED",
                "tickets" => array(
                    "filledTickets" => \TicketsModel::instance()->getUnplayedTickets($ticket->getPlayerId())
                )
            );
            $this->ajaxResponseCode($res, 403);
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
                $this->ajaxResponseCode($res, 402);
                return false;
            }
        }

        try {
            \TicketsModel::instance()->beginTransaction();
            if ($ticket->getIsGold()==true) {
                $player = new Player;
                $player->setId($ticket->getPlayerId())->fetch();
                \PlayersModel::instance()->updateGoldTicket($player, -1);
            }
            $ticket->create();
            \TicketsModel::instance()->commit();
        } catch (EntityException $e) {
            \TicketsModel::instance()->rollBack();
            $this->ajaxResponseInternalError($e->getMessage());
        }

        $res = array(
            "tickets" => array(
                "filledTickets" => \TicketsModel::instance()->getUnplayedTickets($ticket->getPlayerId())
            )
        );

        $this->ajaxResponseCode($res);
    }

    public function buyGoldTicketAction()
    {
        $price    = $this->request()->post('price');

        $player = new Player;
        $player->setId($this->session->get(Player::IDENTITY)->getId())->fetch();
        $country = (
        CountriesModel::instance()->isCountry($player->getCountry())
            ? $this->session->get(Player::IDENTITY)->getCountry()
            : CountriesModel::instance()->defaultCountry());

        $goldPrice = SettingsModel::instance()->getSettings('goldPrice')->getValue($country);

        if ($goldPrice != $price) {
            $this->ajaxResponseCode(array("message"=>"PRICE_NOT_SAME"),403);
        }

        if ($player->getGoldTicket()>0) {
            $this->ajaxResponseCode(array("message"=>"ALREADY_BOUGHT"),400);
        }

        try {
            \TicketsModel::instance()->beginTransaction();
            $money = \PlayersModel::instance()->getBalance($player, true)['Money'];
            if ($money<$goldPrice) {
                throw new \Exception();
            }
            \PlayersModel::instance()->updateBalance($player, 'Money', (0-$goldPrice));
            \PlayersModel::instance()->updateGoldTicket($player,1);

            $transaction = new \Transaction;
            $transaction->setPlayerId($player->getId())->setCurrency('MONEY')->setSum(0-$goldPrice)->setDescription('Покупка золотого билета')->setBalance(\PlayersModel::instance()->getBalance($player, true)['Money'])->create();

            \TicketsModel::instance()->commit();
        } catch (\Exception $e) {
            \TicketsModel::instance()->rollBack();
            $res = array(
                "message" => "MONEY_NO_ENOUGH",
                "tickets" => array(
                    "filledTickets" => \TicketsModel::instance()->getUnplayedTickets($player->getId())
                )
            );
            $this->ajaxResponseCode($res, 402);
        }
        catch (EntityException $e) {
            \TicketsModel::instance()->rollBack();
            $this->ajaxResponseInternalError();
        }

        $player->fetch();
        $res = array(
            "tickets" => array(
                "filledTickets" => \TicketsModel::instance()->getUnplayedTickets($player->getId())
            ),
            "player" => array(
                "balance" => array(
                    "money" => $player->getMoney(),
                )
            )
        );

        $this->ajaxResponseCode($res);
    }

    public function historyAction()
    {
        if (!$this->request()->isAjax()) {
            return false;
        }

        $this->authorizedOnly();

        $playerId = $this->session->get(Player::IDENTITY)->getId();

        $offset = $this->request()->get('offset');
        $count  = $this->request()->get('count', self::$lotteriesPerPage);

        $type = $this->request()->get('type');

        $response = array(
            'res' => array(
                'lottery' => array(
                    'history' => array(
                    ),
                ),
            ),
        );

        try {
            if ($type != "mine") {
                $response['cache'] = 'local';
                $list              = \LotteriesModel::instance()->getPublishedLotteriesList($count + 1, $offset);
                $lotteryType       = "all";
            } else {
                $response['cache'] = 'session';
                $list              = \LotteriesModel::instance()->getPlayerPlayedLotteries($playerId, $count + 1, $offset);
                $lotteryType       = "mine";
            }
        } catch (\PDOException $e) {
            $this->ajaxResponseInternalError();
            return false;
        }

        if (count($list)<=$count) {
            $response['lastItem'] = true;
        } else {
            array_pop($list);
        }

        foreach ($list as $id=>$lottery) {
            $response['res']['lottery']['history'][$id]         = $lottery->exportTo('list');
            $response['res']['lottery']['history'][$id]['type'] = $lotteryType;
        }

        $this->ajaxResponseCode($response);
        return true;
    }

    public function lotteryInfoAction($lotteryId)
    {
        if (!$this->request()->isAjax()) {
            return false;
        }

        $this->authorizedOnly();

        $player = new \Player;
        $player_country = $player->setId($this->session->get(Player::IDENTITY)->getId())->fetch()->getCountry();

        try {
            $lottery = \LotteriesModel::instance()->getLotteryDetails($lotteryId);
            if ($lottery == array()) {
                throw new \ModelException("LOTTERY_NOT_FOUND", 404);
            }
            $prizes      = $lottery->getPrizes();
            $prizes_gold = $lottery->getPrizesGold();
            if (!isset($prizes[$player_country])) {
                $prizes      = $prizes[self::$defaultCountry];
                $prizes_gold = $prizes_gold[self::$defaultCountry];
            } else {
                $prizes      = $prizes[$player_country];
                $prizes_gold = $prizes_gold[$player_country];
            }

            $balls      = $lottery->getBallsTotal();
            $balls_incr = $lottery->getBallsTotalIncr();

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
                    'matches'  => ($lotteryId>=120?$matches + $balls_incr[$count]:null),
                );
                $response['res']['lottery'][$lotteryId]['statistics']["gold"][$count]    = array(
                    'balls'    => $count,
                    'currency' => $prizes_gold[$count]['currency'],
                    'sum'      => $prizes_gold[$count]['sum'],
                    'matches'  => 0,
                );
            }

        } catch (\ModelException $e) {
            $this->ajaxResponseInternalError();

            return false;
        }

        $this->ajaxResponseCode($response);
        return true;
    }

    public function lotteryTicketsAction($lotteryId)
    {
        if (!$this->request()->isAjax()) {
            return false;
        }

        $this->authorizedOnly();

        $player = new Player;
        $player->setId($this->session->get(Player::IDENTITY)->getId())->fetch();

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

        $this->ajaxResponseCode($response);
        return true;
    }

    public function ticketsAction()
    {
        if (!$this->request()->isAjax()) {
            return false;
        }

        $this->authorizedOnly();

        $player = new Player;
        $player->setId($this->session->get(Player::IDENTITY)->getId())->fetch();

        $response = array(
            "player" => array(
                "balance"  => array(
                    "points" => $player->getPoints(),
                    "money"  => $player->getMoney(),
                ),
            ),
            "tickets" => array(
                "lastLotteryId" => \LotteriesModel::instance()->getLastPublishedLottery()->getId(),
                "timeToLottery" => \LotterySettingsModel::instance()->loadSettings()->getNearestGame() + strtotime('00:00:00', time()) - time(),
                "filledTickets" => \TicketsModel::instance()->getUnplayedTickets($player->getId()),
            ),
        );

        $this->ajaxResponseCode($response);
        return true;
    }

}
