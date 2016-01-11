<?php

namespace controllers\production;
use \Application, \SettingsModel, \Player, \EntityException, \LotteryTicket, \CountriesModel, \LotteriesModel, \TicketsModel, \LotterySettings, \LotterySettingsModel, \QuickGamesModel;
use \ChanceGamesModel, \GameSettingsModel;
use Symfony\Component\HttpFoundation\Session\Session;

Application::import(PATH_APPLICATION . 'model/entities/Player.php');
Application::import(PATH_CONTROLLERS . 'production/AjaxController.php');

class PrizesController extends \AjaxController
{
    static  $prizesPerPage;
    private $session;

    public function init()
    {
        self::$prizesPerPage = (int)SettingsModel::instance()->getSettings('counters')->getValue('PRIZES_PER_PAGE') ? : 9;
        $this->session = new Session();
        parent::init();
        if ($this->validRequest()) {
            if (!$this->session->get(Player::IDENTITY) instanceof PLayer) {
                $this->ajaxResponseUnauthorized();
                return false;
            }
            $this->session->get(Player::IDENTITY)->markOnline();
        }
    }

    public function listAction()
    {
        $offset   = $this->request()->get('offset', 0);
        $category = $this->request()->get('category',NULL);

        $list = \ShopModel::instance()->loadShop();

        $player = new Player;
        $player_countries = $player->setId($this->session->get(Player::IDENTITY)->getId())->fetch()->getCountry();

        $categories = array();
        if ($category===NULL) {
            $list_goods = array();
            foreach ($list as $category) {
                $categories[$category->getId()] = $category->exportTo('list');
                $list_goods = array_merge($list_goods, $category->getItems());
            }
        } else {
            $list_goods = $list[$category]->getItems();
        }

        $items = array();
        foreach (array_slice($list_goods,$offset, self::$prizesPerPage+1) as $item) {
            $countries = $item->getCountries();
            if (is_array($countries) and !in_array($player_countries,$countries)) {
                continue;
            }
            $items[$item->getId()] = $item->exportTo('list');
        }

        $response = array();
        if (count($items)>self::$prizesPerPage) {
            array_pop($items);
        } else {
            $response['lastItem'] = true;
        }

        $response['res']['prizes']['exchange']['goods'] = $items;

        if (($categories != array())&&($offset==0)) {
            $response['res']['prizes']['exchange']['categories'] = $categories;
        }

        $this->ajaxResponseCode($response,200);
        return true;
    }

    public function goodAction($itemId) {
        $player = new Player;
        $player_countries = $player->setId($this->session->get(Player::IDENTITY)->getId())->fetch()->getCountry();

        try {
            $item = new \ShopItem();
            $item->setId($itemId)->fetch();
        } catch (EntityException $e) {
            $this->ajaxResponseInternalError();
        }

        if (is_array($item->getCountries()) and !in_array($player_countries,$item->getCountries())) {
            $this->ajaxResponseNotFound();
            return false;
        }

        $response = array();
        $response['res']['prizes']['exchange']['goods'][$itemId] = $item->exportTo('list');

        $this->ajaxResponseCode($response,200);
        return true;
    }

    public function orderAction($itemId)
    {
        try {
            $item = new \ShopItem();
            $item->setId($itemId)->fetch();
        } catch (EntityException $e) {
            $this->ajaxResponseNotFound();
            return false;
        }

        $player = new Player;
        $player->setId($this->session->get(Player::IDENTITY)->getId())->fetch();

        $order = new \ShopItemOrder();
        $order->setPlayer($this->session->get(Player::IDENTITY))
            ->setItem($item)
            ->setName($player->getName())
            ->setSurname($player->getSurname())
            ->setNumber($player->getPhone())
            ->setPhone($player->getPhone())
            ->setRegion($player->getZip())
            ->setCity($player->getCity())
            ->setAddress($player->getZip().' '.$player->getAddress());

        try {
            $order->create();
            $order->getPlayer()->addPoints(-1*$order->getItem()->getPrice(), $order->getItem()->getTitle());
            if ($order->getItem()->getQuantity()) {
                $order->getItem()->setQuantity($order->getItem()->getQuantity() - 1)->update();
            }
        } catch(EntityException $e) {
            $this->ajaxResponseInternalError();
        }

        $player->fetch();
        $res = array(
            "message" => "OK",
            "player"  => array(
                "balance" => array(
                    "money"  => $player->getMoney(),
                    "points" => $player->getPoints(),
                )
            )
        );

        $this->ajaxResponseCode($res);
    }

}