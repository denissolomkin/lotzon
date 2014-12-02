<?php
namespace controllers\admin;

use \Application, \PrivateArea, \Player, \PlayersModel, \ModelException, \LotteriesModel, \TransactionsModel, \Transaction;
use \GameSettings;

Application::import(PATH_CONTROLLERS . 'private/PrivateArea.php');
Application::import(PATH_APPLICATION . '/model/models/PlayersModel.php');
Application::import(PATH_APPLICATION . '/model/models/LotteriesModel.php');
Application::import(PATH_APPLICATION . '/model/entities/Player.php');

class Users extends PrivateArea 
{
    const PLAYERS_PER_PAGE = 100;

    public $activeMenu = 'users';

    public function init()
    {
        parent::init();
    }

    public function indexAction()
    {
        $page = $this->request()->get('page', 1);
        $sort = array(
            'field' => $this->request()->get('sortField', 'Id'),
            'direction' => $this->request()->get('sortDirection', 'desc'),
        );

        $list = PlayersModel::instance()->getList(self::PLAYERS_PER_PAGE, $page == 1 ? 0 : self::PLAYERS_PER_PAGE * $page - self::PLAYERS_PER_PAGE, $sort);
        $count = PlayersModel::instance()->getPlayersCount();

        $pager = array(
            'page' => $page,
            'rows' => $count,
            'per_page' => self::PLAYERS_PER_PAGE,
            'pages' => 0,
        );
        $pager['pages'] = ceil($pager['rows'] / $pager['per_page']);

        $this->render('admin/users', array(
            'title'        => 'Пользователи',
            'layout'       => 'admin/layout.php',
            'activeMenu'   => $this->activeMenu,
            'list'         => $list,
            'playersCount' => $count,
            'pager'        => $pager,
            'currentSort'  => $sort,
        ));
    }

    public function statsAction($playerId) 
    {
        if ($this->request()->isAjax()) {
            $response = array(
                'status'  => 1,
                'message' => 'OK',
                'data'    => array(),
            );
            try {
                $lotteries = LotteriesModel::instance()->getPlayerHistory($playerId);    
            
                foreach ($lotteries as &$lottery) {
                    $lottery['Date']  = date('d.m.Y', $lottery['Date']);
                }
                $response['data']['lotteries'] = $lotteries;    
            } catch (ModelException $e) {
                $response['status'] = 0;
                $response['message'] = $e->getMessage();
            }

            die(json_encode($response));
        }

        $this->redirect('/private');
    }

    public function transactionsAction($playerId) 
    {
        if ($this->request()->isAjax()) {
            $response = array(
                'status'  => 1,
                'message' => 'OK',
                'data'    => array(),
            );
            try {
                $response['data'] = array(
                    'points' => TransactionsModel::instance()->playerPointsHistory($playerId),
                    'money'  => TransactionsModel::instance()->playerMoneyHistory($playerId),
                );
                foreach ($response['data']['points'] as &$transaction) {
                    $transaction = array(
                        'id' => $transaction->getId(),
                        'sum' => $transaction->getSum(),
                        'desc' => $transaction->getDescription(),
                        'date' => date('d.m.Y H:i:s', $transaction->getDate()),
                    );
                }
                foreach ($response['data']['money'] as &$transaction) {
                    $transaction = array(
                        'id' => $transaction->getId(),
                        'sum' => $transaction->getSum(),
                        'desc' => $transaction->getDescription(),
                        'date' => date('d.m.Y H:i:s', $transaction->getDate()),
                    );
                }
            } catch (ModelException $e) {
                $response['status'] = 0;
                $response['message'] = $e->getMessage();
            }

            die(json_encode($response));
        }

        $this->redirect('/private');
    }

    public function removeTransactionAction($transactionId)
    {
        if ($this->request()->isAjax()) {
            $response = array(
                'status'  => 1,
                'message' => 'OK',
                'data'    => array(),
            );
            try {
                $transaction = new Transaction();
                $transaction->setId($transactionId)->fetch();

                $player = new Player();
                $player->setId($transaction->getPlayerId())->fetch();
                
                if ($transaction->getCurrency() == GameSettings::CURRENCY_POINT) {
                    $player->addPoints($transaction->getSum() * -1, $this->request()->post('description'));
                } else {
                    $player->addMoney($transaction->getSum() * -1, $this->request()->post('description'));
                }
                $transaction->delete();
            } catch (EntityException $e) {
                $response['status'] = 0;
                $response['message'] = $e->getMessage();
            }

            die(json_encode($response));
        }

        $this->redirect('/private');
    }

    public function addTransactionAction($playerId)
    {
        if ($this->request()->isAjax()) {
            $response = array(
                'status'  => 1,
                'message' => 'OK',
                'data'    => array(),
            );
            try {
                $player = new Player();
                $player->setId($playerId)->fetch();
                
                if ($this->request()->post('currency') == GameSettings::CURRENCY_POINT) {
                    $player->addPoints($this->request()->post('sum'), $this->request()->post('description'));
                } else {
                    $player->addMoney($this->request()->post('sum'), $this->request()->post('description'));
                }
            } catch (EntityException $e) {
                $response['status'] = 0;
                $response['message'] = $e->getMessage();
            }

            die(json_encode($response));
        }

        $this->redirect('/private');
    }
}