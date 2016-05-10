<?php
namespace controllers\admin;

use \Application, \PrivateArea, \SettingsModel, \Admin,  \CountriesModel, \GameConstructorModel, \Session2;

Application::import(PATH_CONTROLLERS . 'private/PrivateArea.php');

class Ad extends PrivateArea
{
    public $activeMenu = 'ad';

    public function init()
    {
        parent::init();

        if(!array_key_exists($this->activeMenu, SettingsModel::instance()->getSettings('rights')->getValue(Session2::connect()->get(Admin::SESSION_VAR)->getRole())))
            $this->redirect('/private');

    }

    public function indexAction()
    {
        $list = SettingsModel::instance()->getSettings($this->activeMenu)->getValue();
        $games = GameConstructorModel::instance()->getChanceGames();
        $supportedCountries = CountriesModel::instance()->getCountries();
        $banners = array(
            'pages' => array(
                'default' => 'По умолчанию',
                'blog' => 'Блог',
                'lottery' => 'Лотерея',
                'games' => 'Игры',
                'communication' => 'Общение',
                'users' => 'Друзья',
                'prizes' => 'Витрина'
            ),
            'devices' => array(
                'desktop' => array(
                    'brand' => 'Брендирование',
                    'top' => 'Шапка',
                    'right' => 'Боковой',
                    'teaser' => 'Тизерка'
                ),
                'tablet' => array(
                    'top' => 'Шапка',
                    'popup' => 'Всплывайка'
                ),
                'mobile' => array(
                    'popup' => 'Всплывайка'
                )
            ),
            'context' => array(
                'lottery' => 'Лотерея',
                'ticket' => 'Билет',
                'prize' => 'Приз1',
                'prize2' => 'Приз2',
                'prize3' => 'Приз3',
                'prize4' => 'Приз4',
                'prize5' => 'Приз5',
                'blog' => 'Блог1',
                'blog2' => 'Блог2',
                'blog3' => 'Блог3',
                'blog4' => 'Блог4',
                'blog5' => 'Блог5',
                'post' => 'Статья',
                'game' => 'Игра',
                'comment' => 'Комментарий'
            ),
            'chance' => array()
        );

        foreach($games as $id => $game)
            $banners['chance'][$id] = $game->getTitle(1);

        if (!is_array($list))
            $list = array();

        $this->render('admin/'.$this->activeMenu, array(
            'title'      => 'Баннеры на сайте',
            'layout'     => 'admin/layout.php',
            'activeMenu' => $this->activeMenu,
            'supportedCountries'  => $supportedCountries,
            'list'       => $list,
            'banners'    => $banners
        ));
    }

    public function bannerAction()
    {
        if ($this->request()->isAjax()) {

            $response = array(
                'status'  => 1,
                'message' => 'OK',
                'res'    => str_replace('document.write', "$('#banner-holder .modal-body').append", implode('',$_POST))
            );

            die(json_encode($response));
        }

        $this->redirect('/private/');
    }

    public function saveAction()
    {

        if($this->request()->post($this->activeMenu))
            SettingsModel::instance()->getSettings($this->activeMenu)->setValue($this->request()->post($this->activeMenu))->create();
        $this->redirect('/private/'.$this->activeMenu);
    }

}