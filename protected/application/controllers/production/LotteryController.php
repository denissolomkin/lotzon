<?php

namespace controllers\production;
use \Application, \SettingsModel, \Player, \EntityException, \LotteryTicket, \CountriesModel, \TicketsModel;
use Symfony\Component\HttpFoundation\Session\Session;

Application::import(PATH_APPLICATION . 'model/entities/Player.php');
Application::import(PATH_APPLICATION . 'model/entities/LotteryTicket.php');
Application::import(PATH_CONTROLLERS . 'production/AjaxController.php');

class LotteryController extends \AjaxController
{
    private $session;

    public function init()
    {
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

}
