<?php

namespace controllers\production;
use \Application, \SettingsModel, \Player, \EntityException;
use \CountriesModel, \Banner;

Application::import(PATH_APPLICATION . 'model/entities/Player.php');
Application::import(PATH_CONTROLLERS . 'production/AjaxController.php');

class PrizesController extends \AjaxController
{
    static  $prizesPerPage;

    public function init()
    {
        self::$prizesPerPage = (int)SettingsModel::instance()->getSettings('counters')->getValue('PRIZES_PER_PAGE') ? : 9;
        parent::init();

        $this->validateRequest();
        $this->authorizedOnly(true);
        $this->validateLogout();
        $this->validateCaptcha();
    }

    public function listAction()
    {
        $offset   = $this->request()->get('offset', 0);
        $category = $this->request()->get('category',NULL);

        $list = \ShopModel::instance()->loadShop();
        $country = $this->player->fetch()->getCountry();

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

        $from_first = 0;
        $items = array();
        foreach ($list_goods as $item) {
            $countries = $item->getCountries();
            if (is_array($countries) and !in_array($country, $countries)) {
                continue;
            }
            $from_first++;
            if ($from_first<=$offset) {
                continue;
            }
            $items[$item->getId()] = $item->exportTo('list');
            if (count($items)==self::$prizesPerPage+1) {
                break;
            }
        }

        $response = array();
        if (count($items)>self::$prizesPerPage) {
            array_pop($items);
        } else {
            $response['lastItem'] = true;
        }

        if(count($items)) {
            $increment = $offset ? ceil($offset/self::$prizesPerPage)+1:'';
            $banner = new Banner;
            $keys = array_keys($items);
            $items[$keys[array_rand($keys)]]['block'] = $banner
                ->setDevice('desktop')
                ->setLocation('context')
                ->setPage('prize'.$increment)
                ->setCountry($this->player->getCountry())
                ->random()
                ->render();
        }

        $response['res']['prizes']['exchange']['goods'] = $items;

        if (($categories != array())&&($offset==0)) {
            $response['res']['prizes']['exchange']['categories'] = $categories;
        }

        $this->ajaxResponseNoCache($response,200);
        return true;
    }

    public function goodAction($itemId)
    {

        $country = $this->player->fetch()->getCountry();

        try {
            $item = new \ShopItem();
            $item->setId($itemId)->fetch();
        } catch (EntityException $e) {
            $this->ajaxResponseInternalError();
        }

        if (is_array($item->getCountries()) and !in_array($country, $item->getCountries())) {
            $this->ajaxResponseNotFound();
            return false;
        }

        $response = array();
        $response['res']['prizes']['exchange']['goods'][$itemId] = $item->exportTo('list');

        $this->ajaxResponseNoCache($response,200);
        return true;
    }

    public function orderAction($itemId)
    {
        try {

            $item = new \ShopItem();
            $item->setId($itemId)->fetch();
            if ((($item->getQuantity()==0)and($item->getQuantity()!==NULL))or(!$item->isVisible())) {
                throw new EntityException("INVALID_ITEM", 400);
            }

        } catch (EntityException $e) {
            $this->ajaxResponseNotFound();
            return false;
        }

        $this->player
            ->fetch()
            ->initAccounts();

        $currency = CountriesModel::instance()->getCountry($this->player->getCurrency())->loadCurrency();
        $phone = $this->player->getAccounts('Phone') ? $this->player->getAccounts('Phone')[0] : null;

        $order = new \ShopItemOrder();
        $order->setPlayer($this->player)
            ->setItem($item)
            ->setSum($item->getPrice())
            ->setEquivalent($item->getPrice() / ($currency->getRate() * $currency->getCoefficient()))
            ->setName($this->player->getName()!=''?$this->player->getName():' ')
            ->setSurname($this->player->getSurname()!=''?$this->player->getSurname():' ')
            ->setNumber($phone)
            ->setPhone($phone)
            ->setRegion($this->player->getZip()!=''?$this->player->getZip():' ')
            ->setCity($this->player->getCity()!=''?$this->player->getCity():' ')
            ->setAddress($this->player->getAddress()!=''?$this->player->getAddress():' ');

        try {

            $order->beginTransaction();
            $order->create();
            $this->player->addPoints(-1*$order->getItem()->getPrice(), $order->getItem()->getTitle());
            if ($order->getItem()->getQuantity()) {
                $order->getItem()->setQuantity($order->getItem()->getQuantity() - 1)->update();
            }
            $this->session->set(Player::IDENTITY, $this->player);
            $order->commit();

        } catch(EntityException $e) {

            $order->rollBack();
            $this->ajaxResponseNoCache(array("message" => $e->getMessage()), $e->getCode());
        }

        $res = array(
            "message" => "message-order-success",
            "player"  => array(
                "balance" => array(
                    "money"  => $this->player->getMoney(),
                    "points" => $this->player->getPoints(),
                )
            )
        );

        $this->ajaxResponseNoCache($res);
    }

}
