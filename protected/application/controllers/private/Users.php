<?php
namespace controllers\admin;

use \Application, \PrivateArea,  \EntityException, \Player, \PlayersModel, \ModelException, \LotteriesModel, \TransactionsModel, \Transaction, \NoticesModel, \Notice, \NotesModel, \Note, \Session2, \Admin;
use \LotterySettings, \ShopOrdersModel, \MoneyOrderModel, \SettingsModel;

Application::import(PATH_CONTROLLERS . 'private/PrivateArea.php');
Application::import(PATH_APPLICATION . '/model/models/PlayersModel.php');
Application::import(PATH_APPLICATION . '/model/models/LotteriesModel.php');
Application::import(PATH_APPLICATION . '/model/entities/Player.php');

class Users extends PrivateArea 
{
    static $PER_PAGE;

    public $activeMenu = 'users';

    public function init()
    {
        parent::init();
        self::$PER_PAGE = SettingsModel::instance()->getSettings('counters')->getValue('PLAYERS_PER_ADMIN') ? : 100;

        if(!array_key_exists($this->activeMenu, SettingsModel::instance()->getSettings('rights')->getValue(Session2::connect()->get(Admin::SESSION_VAR)->getRole())))
            $this->redirect('/private');

    }

    public function indexAction()
    {
        $page = $this->request()->get('page', 1);
        $search = $this->request()->get('search', null);
        $sort = array(
            'field' => $this->request()->get('sortField', 'Id'),
            'direction' => $this->request()->get('sortDirection', 'desc'),
        );

        $list = PlayersModel::instance()->getList(self::$PER_PAGE, $page == 1 ? 0 : self::$PER_PAGE * $page - self::$PER_PAGE, $sort, $search);
        $count = PlayersModel::instance()->getPlayersCount($search);

        $pager = array(
            'page' => $page,
            'rows' => $count,
            'per_page' => self::$PER_PAGE,
            'pages' => 0,
        );
        $pager['pages'] = ceil($pager['rows'] / $pager['per_page']);

        $this->render('admin/users', array(
            'title'        => 'Пользователи',
            'layout'       => 'admin/layout.php',
            'frontend'      => 'users',
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
            $limit = SettingsModel::instance()->getSettings('counters')->getValue('TRANSACTIONS_PER_ADMIN')?:20;
            $offset = $this->request()->get('offset')?:0;
            $currency = $this->request()->get('currency')?:null;
            $response = array(
                'status'  => 1,
                'message' => 'OK',
                'data'    => array(),
            );
            try {
                $response['data'] = array(
                    'points' => (!$currency || $currency=='points' ? TransactionsModel::instance()->playerPointsHistory($playerId, $limit, $offset) : null),
                    'money'  => (!$currency || $currency=='money' ? TransactionsModel::instance()->playerMoneyHistory($playerId,$limit, $offset) : null),
                    'limit' => $limit,
                );
                if(is_array($response['data']['points']))
                    foreach ($response['data']['points'] as &$transaction) {
                        $transaction = array(
                            'id' => $transaction->getId(),
                            'sum' => $transaction->getSum(),
                            'bal' => $transaction->getBalance(),
                            'desc' => $transaction->getDescription(),
                            'date' => date('d.m.Y H:i:s', $transaction->getDate()),
                        );
                    }
                if(is_array($response['data']['money']))
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
                
                if ($transaction->getCurrency() == LotterySettings::CURRENCY_POINT) {
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
                
                if ($this->request()->post('currency') == LotterySettings::CURRENCY_POINT) {
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

    public function profileAction($playerId)
    {
        if ($this->request()->isAjax()) {
            $response = array(
                'status'  => 1,
                'message' => 'OK',
                'data'    => array(),
            );
            $player = new Player;
            $player->setId($playerId)->fetch();
            try {
                $response['data'] = array(
                    'Avatar' => $player->getAvatar(),
                    'Id' => $player->getId(),
                    'Nicname' => $player->getNicName(),
                    'Name' => $player->getName(),
                    'Surname' => $player->getSurname(),
                    'Birthday' => $player->getBirthday()?$player->getBirthday('d.m.Y'):'',
                    'Phone' => $player->getPhone()?:'',
                    'Qiwi' => $player->getQiwi()?:'',
                    'YandexMoney' => $player->getYandexMoney()?:'',
                    'WebMoney' => $player->getWebMoney()?:'',
                    'Country' => $player->getCountry(),
                    'Lang' => $player->getLang(),
                    'UTC' => $player->getUtc()?:'',
                );

            } catch (ModelException $e) {
                $response['status'] = 0;
                $response['message'] = $e->getMessage();
            }

            die(json_encode($response));
        }
        $this->redirect('/private');
    }

    public function updateProfileAction($playerId)
    {
        if ($this->request()->isAjax()) {
            $response = array(
                'status'  => 1,
                'message' => 'OK',
                'data'    => array(),
            );

            $player = new Player;
            $player->setId($playerId)->fetch();
            try {

                if ($this->request()->post('bd') && (!strtotime($this->request()->post('bd')) || !preg_match('/[0-3][0-9].[0-1][0-9].[1-2][0-9]{3}/', $this->request()->post('bd')))) {
                    throw new EntityException("INVALID_DATE_FORMAT", 400);
                }

                $player->setPhone($this->request()->post('phone'));

                $player->setQiwi($this->request()->post('qiwi'));
                $player->setWebMoney($this->request()->post('webmoney'));
                $player->setYandexMoney($this->request()->post('yandexmoney'));

                if($this->request()->post('bd'))
                    $player->setBirthday(strtotime($this->request()->post('bd')));

                $player->setNicname($this->request()->post('Nicname'))
                    ->setName($this->request()->post('Name'))
                    ->setSurName($this->request()->post('Surname'))
                    ->setCountry($this->request()->post('Country'))
                    ->setLang($this->request()->post('Lang'))
                    ->setUtc($this->request()->post('UTC'))
                    ->update();

            } catch (EntityException $e){
                die(json_encode(array(
                    'status'  => 0,
                    'message' => $e->getMessage(),
                    'data'    => array(),
                    )));
            }
            if ($pwd = $this->request()->post('Password')) {
                $pwd=trim($pwd);
                $player->writeLog(array('action'=>'CHANGE_PASSWORD', 'desc'=>$player->hidePassword($pwd),'status'=>'info'))
                    ->changePassword($pwd);
            }

            die(json_encode($response));
        }
        $this->redirect('/private');
    }

    public function loginsAction($playerId)
    {
        if ($this->request()->isAjax()) {
            $response = array(
                'status'  => 1,
                'message' => 'OK',
                'data'    => array(),
            );
            try {
                $response['data'] = array(
                    'logins' => PlayersModel::instance()->getLogins($playerId),
                );

            } catch (ModelException $e) {
                $response['status'] = 0;
                $response['message'] = $e->getMessage();
            }

            die(json_encode($response));
        }
        $this->redirect('/private');
    }

    public function multsAction($playerId)
    {
        if ($this->request()->isAjax()) {
            $response = array(
                'status'  => 1,
                'message' => 'OK',
                'data'    => array(),
            );
            try {
                $response['data'] = array(
                    'mults' => PlayersModel::instance()->getMults($playerId),
                );

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
                        'country' => $notice->getCountry(),
                        'minLotteries' => ($notice->getMinLotteries()?:null),
                        'registeredFrom' => ($notice->getRegisteredFrom()? date('d.m.Y',$notice->getRegisteredFrom()) :null),
                        'registeredUntil' => ($notice->getRegisteredUntil()? date('d.m.Y',$notice->getRegisteredUntil()) :null),
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

    public function addNoticeAction($playerId)
    {
        if ($this->request()->isAjax()) {
            $response = array(
                'status'  => 1,
                'message' => 'OK',
                'data'    => array(),
            );

            try {
                if($players = $this->request()->post('ids')){
                    $players = explode(',', $players);
                } else {
                    $players = array($playerId);
                }

                foreach($players as $playerId) {
                    if(!is_numeric($playerId))
                        continue;

                    if($playerId) {
                        $message = new \Message;
                        $message->setPlayerId(SettingsModel::instance()->getSettings('counters')->getValue('USER_REVIEW_DEFAULT'))
                            ->setToPlayerId($playerId)
                            ->setText($this->request()->post('text'))
                            ->create();
                    }

                    $notice = new Notice();
                    $notice->setPlayerId($playerId)
                        ->setUserId(Session2::connect()->get(Admin::SESSION_VAR)->getId())
                        ->setText($this->request()->post('text'))
                        ->setTitle($this->request()->post('title'))
                        ->setType($this->request()->post('type'))
                        ->setCountry($this->request()->post('country') ?: null)
                        ->setMinLotteries($this->request()->post('minLotteries') ?: null)
                        ->setRegisteredFrom($this->request()->post('registeredFrom') ?: null)
                        ->setRegisteredUntil($this->request()->post('registeredUntil') ?: null)
                        ->create();
                }

            } catch (EntityException $e) {
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

    public function botAction($playerId, $status = 0)
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

                if($status && !$player->isBan()){
                    $response['status'] = 0;
                    $response['message'] = 'FIRST_BAN_PLAYER';
                } else {
                    $player->setBot($status);

                    PlayersModel::instance()->bot($player);
                    PlayersModel::instance()->writeLog($player, array('action' => 'PLAYER_BOT', 'desc' => ($status ? 'SET' : 'UNSET'), 'status' => ($status ? 'danger' : 'warning')));

                    $response['data'] = array(
                        'bot' => $player->isBot(),
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

    public function banAction($playerId, $status = 0)
    {
        if ($this->request()->isAjax()) {

            $response = array(
                'status'  => 1,
                'message' => 'OK',
                'data'    => array(),
            );

            try {
                $player = new Player;
                $player
                    ->setId($playerId)
                    ->fetch()
                    ->setBan($status);

                if(!$status && $player->isBot()){
                    $response['status'] = 0;
                    $response['message'] = 'FIRST_UNSET_BOT';
                } else {
                    PlayersModel::instance()->ban($player);
                    PlayersModel::instance()->writeLog($player, array('action' => 'PLAYER_BAN', 'desc' => ($status ? 'BLOCKED' : 'UNBLOCKED'), 'status' => ($status ? 'danger' : 'warning')));

                    $response['data'] = array(
                        'ban' => $player->isBan(),
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

    public function avatarAction($id){

            $response = array(
                'status'  => 1,
                'message' => 'OK',
                'data'    => array(),
            );

            $player   = new \Player();

            try {

                $player
                    ->setId($id)
                    ->fetch()
                    ->uploadAvatar();

                $response['data'] = array(
                    'image' => $player->getAvatar(),
                );

            } catch (ModelException $e) {
                $response['status']  = 0;
                $response['message'] = $e->getMessage();
            }

            die(json_encode($response));
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

}