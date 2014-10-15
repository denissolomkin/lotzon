<?php
namespace controllers\admin;

use \Application, \PrivateArea, \NewsModel, \Config, \ShopModel, \ChanceGame, \ChanceGamesModel, \EntityException;

Application::import(PATH_CONTROLLERS . 'private/PrivateArea.php');

class MomentalChances extends PrivateArea 
{
    public $activeMenu = 'chances';

    public function init()
    {
        parent::init();
    }

    public function indexAction()
    {   
       $shopItems = ShopModel::instance()->getAllItems(false);
       $games = ChanceGamesModel::instance()->getGamesSettings();

       $this->render('admin/chances', array(
            'title'      => 'Моментальные шансы',
            'layout'     => 'admin/layout.php',
            'activeMenu' => $this->activeMenu,
            'shopItems'  => $shopItems,
            'games'      => $games,
        ));
    }

    public function saveAction()
    {
        if ($this->request()->isAjax()) {
            $response = array(
                'status'  => 1,
                'message' => 'OK',
                'data'    => array(),
            );
            $game = new ChanceGame();
            $game->setIdentifier($this->request()->post('identifier'));
            switch ($game->getIdentifier()) {
                case 'moment':
                    $game->setMinFrom($this->request()->post('minFrom'));
                    $game->setMinTo($this->request()->post('minTo'));
                    $game->setPointsWin($this->request()->post('pointsWin'));
                break;
                case '33' :
                case '44' :
                case '55' :
                    $game->setGameTitle($this->request()->post('title'));
                    $game->setGamePrice($this->request()->post('price'));
                    $game->setPrizes($this->request()->post('prizes'));
                break;
                default:break;
            }   
            if ($game->getIdentifier() == '55') {
                $game->setTriesCount($this->request()->post('tries'));
            }

            try {
                $game->save();
            } catch (EntityException $e) {
                $response['status'] = 0;
                $response['message'] = $e->getMessage();
            }

            die(json_encode($response));
        }
    }
}