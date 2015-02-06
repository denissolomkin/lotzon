<?php
namespace controllers\admin;

use \Application, \PrivateArea, \Player, \PlayersModel, \ModelException, \LotteriesModel, \TransactionsModel, \Transaction, \NoticesModel, \Notice, \NotesModel, \Note, \Session2, \Admin;
use \GameSettings, \ShopOrdersModel, \MoneyOrderModel, \Config;

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

        if (!Config::instance()->rights[Session2::connect()->get(Admin::SESSION_VAR)->getRole()][$this->activeMenu]) {
            $this->redirect('/private');
        }
    }

    public function indexAction()
    {
        $page = $this->request()->get('page', 1);
        $search = $this->request()->get('search', null);
        $sort = array(
            'field' => $this->request()->get('sortField', 'Id'),
            'direction' => $this->request()->get('sortDirection', 'desc'),
        );

        $list = PlayersModel::instance()->getList(self::PLAYERS_PER_PAGE, $page == 1 ? 0 : self::PLAYERS_PER_PAGE * $page - self::PLAYERS_PER_PAGE, $sort, $search);
        $count = PlayersModel::instance()->getPlayersCount($search);

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
            'frontend'      => 'admin/users_frontend.php',
            'activeMenu'   => $this->activeMenu,
            'list'         => $list,
            'playersCount' => $count,
            'pager'        => $pager,
            'currentSort'  => $sort,
            'search'       => $search,
            'stats'        => PlayersModel::instance()->getProcessor()->getPlayersStats(),
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
                        'bal' => $transaction->getBalance(),
                        'desc' => $transaction->getDescription(),
                        'date' => date('d.m.Y H:i:s', $transaction->getDate()),
                    );
                }
                foreach ($response['data']['money'] as &$transaction) {
                    $transaction = array(
                        'id' => $transaction->getId(),
                        'sum' => $transaction->getSum(),
                        'bal' => $transaction->getBalance(),
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

    public function ordersAction($playerId)
    {
        if ($this->request()->isAjax()) {
            $response = array(
                'status'  => 1,
                'message' => 'OK',
                'data'    => array(),
            );
            $number=$this->request()->get('number',null);

            try {
                $response['data'] = array(
                    'ShopOrders' => ShopOrdersModel::instance()->getOrdersToProcess(null,null,$playerId,null,$number),
                    'MoneyOrders' => MoneyOrderModel::instance()->getOrdersToProcess(null,null,$playerId,null,null,$number)
                );

                foreach ($response['data']['ShopOrders'] as &$order) {
                    $order = array(
                        'id' => $order->getId(),
                        'status' => $order->getStatus(),
                        'number' => $order->getPhone(),
                        'username' => ($order->getUserName()?$order->getUserName().': ':'').($order->getDateProcessed()?date('d.m.Y H:i:s', $order->getDateProcessed()):''),
                        'playername' => ($order->getPlayer()?$order->getPlayer()->getNicname().'<br>'.$order->getPlayer()->getSurName().' '.$order->getPlayer()->getName().' '.$order->getPlayer()->getSecondName():''),
                        'item' => $order->getItem()->getTitle(),
                        'name' => $order->getSurname().' '.$order->getName.' '.$order->getSecondName(),
                        'phone' => $order->getPhone(),
                        'address' => ($order->getRegion() ? $order->getRegion() . ' обл.,' : '').' г. '.$order->getCity().', '.$order->getAddress(),
                        'price'     => ($order->getChanceGameId() ? 'Выиграл в шанс' : $order->getItem()->getPrice()),
                        'date' => date('d.m.Y H:i:s', $order->getDateOrdered()),
                    );
                }


                foreach ($response['data']['MoneyOrders'] as &$order) {

                    $dataOrder=array();
                    foreach ($order->getData() as $key => $data)
                        $dataOrder[] = $data['title'].': '.$data['value'];
                    $order = array(
                        'id' => $order->getId(),
                        'status' => $order->getStatus(),
                        'number' => $order->getNumber(),
                        'username' => ($order->getUserName()?$order->getUserName().': ':'').($order->getDateProcessed()?date('d.m.Y H:i:s', $order->getDateProcessed()):''),
                        'playername' => ($order->getPlayer()?$order->getPlayer()->getNicname().'<br>'.$order->getPlayer()->getSurName().' '.$order->getPlayer()->getName().' '.$order->getPlayer()->getSecondName():null),
                        'type' => $order->getType(),
                        'data' => (implode('</br>',$dataOrder)),
                        'date' => date('d.m.Y H:i:s', $order->getDateOrdered()),
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

    public function ticketsAction($playerId)
    {
        if ($this->request()->isAjax()) {
            $response = array(
                'status'  => 1,
                'message' => 'OK',
                'data'    => array(),
            );
            try {
                $response['data'] = array(
                    'tickets' => PlayersModel::instance()->getTickets($playerId),
                );

            } catch (ModelException $e) {
                $response['status'] = 0;
                $response['message'] = $e->getMessage();
            }

            die(json_encode($response));
        }
        $this->redirect('/private');
    }

    public function reviewsAction($playerId)
    {
        if ($this->request()->isAjax()) {
            $response = array(
                'status'  => 1,
                'message' => 'OK',
                'data'    => array(),
            );
            try {
                $response['data'] = array(
                    'reviews' => PlayersModel::instance()->getReviews($playerId),
                );

            } catch (ModelException $e) {
                $response['status'] = 0;
                $response['message'] = $e->getMessage();
            }

            die(json_encode($response));
        }
        $this->redirect('/private');
    }

    public function logsAction($playerId)
    {
        if ($this->request()->isAjax()) {
            $response = array(
                'status'  => 1,
                'message' => 'OK',
                'data'    => array(),
            );
            try {
                $response['data'] = array(
                    'logs' => PlayersModel::instance()->getLog($playerId,$this->request()->get('action', 'null')),
                );

            } catch (ModelException $e) {
                $response['status'] = 0;
                $response['message'] = $e->getMessage();
            }

            die(json_encode($response));
        }
        $this->redirect('/private');
    }

    public function notesAction($playerId)
    {
        if ($this->request()->isAjax()) {
            $response = array(
                'status'  => 1,
                'message' => 'OK',
                'data'    => array(),
            );
            try {
                $response['data'] = array(
                    'notes' => NotesModel::instance()->getList($playerId),
                );

                foreach ($response['data']['notes'] as &$note) {
                    $note = array(
                        'id' => $note->getId(),
                        'user' => $note->getUserName(),
                        'date' => date('d.m.Y H:i:s', $note->getDate()),
                        'text' => $note->getText(),
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

    public function removeNoteAction($noteId)
    {
        if ($this->request()->isAjax()) {
            $response = array(
                'status'  => 1,
                'message' => 'OK',
                'data'    => array(),
            );
            try {
                $note = new Note();
                $note->setId($noteId)->delete();

            } catch (EntityException $e) {
                $response['status'] = 0;
                $response['message'] = $e->getMessage();
            }
            die(json_encode($response));
        }

        $this->redirect('/private');
    }

    public function addNoteAction($playerId)
    {
        if ($this->request()->isAjax()) {
            $response = array(
                'status'  => 1,
                'message' => 'OK',
                'data'    => array(),
            );
            try {
                $note = new Note();
                $note->setPlayerId($playerId)
                    ->setUserId(Session2::connect()->get(Admin::SESSION_VAR)->getId())
                    ->setText($this->request()->post('text'))
                    ->create();
            } catch (EntityException $e) {
                $response['status'] = 0;
                $response['message'] = $e->getMessage();
            }

            die(json_encode($response));
        }

        $this->redirect('/private');
    }

    public function noticesAction($playerId)
    {
        if ($this->request()->isAjax()) {
            $response = array(
                'status'  => 1,
                'message' => 'OK',
                'data'    => array(),
            );
            try {
                $response['data'] = array(
                    'notices' => NoticesModel::instance()->getList($playerId),
                );

                foreach ($response['data']['notices'] as &$notice) {
                    $notice = array(
                        'id' => $notice->getId(),
                        'title' => $notice->getTitle(),
                        'username' => ($notice->getUserName()?:''),
                        'text' => $notice->getText(),
                        'date' => date('d.m.Y H:i:s', $notice->getDate()),
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

    public function removeNoticeAction($noticeId)
    {
        if ($this->request()->isAjax()) {
            $response = array(
                'status'  => 1,
                'message' => 'OK',
                'data'    => array(),
            );
            try {
                $notice = new Notice();
                $notice->setId($noticeId)->delete();

            } catch (EntityException $e) {
                $response['status'] = 0;
                $response['message'] = $e->getMessage();
            }
            die(json_encode($response));
        }

        $this->redirect('/private');
    }

    public function banAction($playerId)
    {
        if ($this->request()->isAjax()) {
            $response = array(
                'status'  => 1,
                'message' => 'OK',
                'data'    => array(),
            );
            try {
                $player = new Player;
                $player->setId($playerId)->fetch();
                $status = $this->request()->get('ban',0);

                PlayersModel::instance()->writeLog($player,array('action'=>'PLAYER_BAN', 'desc'=>($status?'BLOCKED':'UNBLOCKED'), 'status'=>($status?'danger':'warning')));

                $response['data'] = array(
                    'ban' => PlayersModel::instance()->ban($player,$status),
                );

            } catch (ModelException $e) {
                $response['status'] = 0;
                $response['message'] = $e->getMessage();
            }

            die(json_encode($response));
        }
        $this->redirect('/private');
    }

    public function deleteAction($playerId)
    {
        if ($this->request()->isAjax()) {
            $response = array(
                'status'  => 1,
                'message' => 'OK',
                'data'    => array(),
            );
            try {
                $player = new Player;
                $player->setId($playerId)->fetch();
                $response['data'] = array(
                    'delete' => PlayersModel::instance()->delete($player),
                );

            } catch (ModelException $e) {
                $response['status'] = 0;
                $response['message'] = $e->getMessage();
            }

            die(json_encode($response));
        }
        $this->redirect('/private');
    }

    public function addNoticeAction($playerId)
    {
        if ($this->request()->isAjax()) {
            $response = array(
                'status'  => 1,
                'message' => 'OK',
                'data'    => array(),
            );
            try {
                $notice = new Notice();
                $notice->setPlayerId($playerId)
                    ->setUserId(Session2::connect()->get(Admin::SESSION_VAR)->getId())
                    ->setText($this->request()->post('text'))
                    ->setTitle($this->request()->post('title'))
                    ->setType($this->request()->post('type'))
                    ->create();
            } catch (EntityException $e) {
                $response['status'] = 0;
                $response['message'] = $e->getMessage();
            }

            die(json_encode($response));
        }

        $this->redirect('/private');
    }
}