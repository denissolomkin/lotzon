<?php
namespace controllers\admin;

use \Application, \PrivateArea, \NewsModel, \Config, \ShopModel, \ChanceGame, \ChanceGamesModel, \EntityException, \Admin, \Session2;

Application::import(PATH_CONTROLLERS . 'private/PrivateArea.php');

class MomentalChances extends PrivateArea 
{
    public $activeMenu = 'chances';

    public function init()
    {
        parent::init();

        if (!Config::instance()->rights[Session2::connect()->get(Admin::SESSION_VAR)->getRole()][$this->activeMenu]) {
            $this->redirect('/private');
        }
    }

    public function indexAction()
    {   
       $shopItems = ShopModel::instance()->getAllItems(false);
       $games = ChanceGamesModel::instance()->getGamesSettings();

        $this->render('admin/chances', array(
            'title'      => 'Настройка игр',
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
                case 'quickgame':
                    $game->setGameTitle($this->request()->post('title'));
                    $game->setMinFrom($this->request()->post('minFrom'));
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