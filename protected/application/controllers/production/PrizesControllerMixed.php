<?php

namespace controllers\production;
use \Application, \SettingsModel, \Player, \EntityException;
use \CountriesModel, \Banner, \Common;

Application::import(PATH_APPLICATION . 'model/entities/Player.php');
Application::import(PATH_CONTROLLERS . 'production/AjaxController.php');

class PrizesControllerMixed extends \AjaxController
{
    static  $prizesPerPage;

    public function init()
    {
        self::$prizesPerPage = (int)SettingsModel::instance()->getSettings('counters')->getValue('PRIZES_PER_PAGE') ? : 9;
        parent::init();

        $this->validateRequest();
    }

    public function listAction()
    {
        if ($this->isAuthorized(true)) {
            $this->validateLogout();
            $this->validateCaptcha();
            $country  = $this->player->getCountry();
        } else {
            $country  = Common::getUserIpCountry();
        }

        $offset   = $this->request()->get('offset', 0);
        $category = $this->request()->get('category',NULL);

        $list = \ShopModel::instance()->loadShop();

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
                ->setCountry($country)
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

}
