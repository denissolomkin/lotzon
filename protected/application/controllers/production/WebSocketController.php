<?php namespace controllers\production;

 use Ratchet\MessageComponentInterface;
 use Ratchet\ConnectionInterface;
 use Ratchet\Wamp\Exception;
 use \Player, \DB, \Config, \Application, \GameSettingsModel;


 Application::import(PATH_APPLICATION . '/model/entities/Player.php');
 Application::import(PATH_APPLICATION . '/model/entities/GameSettings.php');
 require_once dirname(__DIR__) . '/../../system/DB.php';
 require_once dirname(__DIR__) . '/../../system/Config.php';
 require_once dirname(__DIR__) . '/../../../protected/configs/config.php';

class WebSocketController implements MessageComponentInterface {

    const   MIN_WAIT_TIME = 2;
    const   PERIODIC_TIMER = 2;

    private $_clients=array();
    private $_stack=array();
    private $_apps=array();
    private $_players=array();
    private $_bots=array();
    private $_class;
    private $_settings,$_loop;
    private $_rating=array();
    private $_games=array(
        'NewGame' => 1
    );
    private $_modes=array('POINT-0','POINT-25','POINT-50','MONEY-0.1','MONEY-0.25');
    protected $_shutdown;

    public function __construct($loop) {

        echo time()." ". "Server have started\n";
        $this->_loop=$loop;
        $this->_loop->addPeriodicTimer(self::PERIODIC_TIMER, function () { $this->checkStack();});
        $this->_bots=Config::instance()->gameBots;
        $this->_clients = array();
        $this->_settings = GameSettingsModel::instance()->loadSettings();
    }

    public function checkStack()
    {
        foreach($this->_stack as $game=>$modes)
            foreach($modes as $mode=>$stacks)
                foreach($stacks as $id=>$client)
                    if($client->time + self::MIN_WAIT_TIME < time()){
                        $clients[$id] = $client;
                        $bot=(object) $this->_bots[array_rand($this->_bots)];
                        $clients[$bot->id] = $bot;
                        $this->initGame($clients,$game,$mode,$id);

                    }
    }

    public function initGame($clients,$name,$mode,$id)
    {
        echo time()." $name инициируем приложение\n";
        $app = new $this->_class;
        $keys = array_keys($clients);
        list($currency, $price) = explode("-", $mode);

        #echo time()." чистим стек\n";
        foreach ($keys as $key) {
            if(isset($this->_players[$key])) {
                unset ($this->_stack[$name][$mode][$key]);
                $this->_players[$key]['appId'] = $app->getIdentifier();
            }
        }

        if (isset($this->_stack[$name][$mode]) AND count($this->_stack[$name][$mode]) == 0){
            unset($this->_stack[$name][$mode]);
            #echo time()." никого не осталось, удаляем стек\n";
        }

        #echo time()." запускаем и кешируем приложение\n";
        $app->setClients($clients)
            ->setClient($id)
            ->setCurrency($currency)
            ->setPrice((float)$price);;
        $this->_apps[$name][$app->getIdentifier()] = $app;
        $this->runGame($name,$app->getIdentifier(),'startAction',$id);
    }


    public function runGame($name,$id,$action,$pid=null,$data=null)
    {

        if($app=$this->_apps[$name][$id]) {
            $class = $this->_class;
            echo time() . " " . "$name {$app->getIdentifier()} $action ".($pid?"игрок №$pid":'бот')." \n";

            if (($action == 'replayAction' && !$this->checkBalance($pid, $app->getCurrency(), $app->getPrice()))) {
                #echo time() . " " . "Игрок {$from->resourceId} - недостаточно средств для игры\n";
                if($this->_clients[$pid])
                    $this->_clients[$pid]->send(json_encode(array('error' => 'INSUFFICIENT_FUNDS')));
                #echo time() . " " . "прошли проверку, устанавливаем клиента \n";
            } else {

                if(isset($pid))
                    $app->setClient($pid);

                #echo time() . " " . "пробуем вызвать экшн \n";
                call_user_func(array($app, $action), $data);

                #echo time() . " " . "рассылаем игрокам результат обработки \n";
                $this->sendCallback($app->getResponse(), $app->getCallback());

                if($app->_botTimer AND !$app->_isOver)
                    $this->_loop->addTimer($app->_botTimer, function () use ($name, $id) {
                        $this->runGame($name,$id,'moveAction');
//                        $this->_apps[$name][$id]->moveAction();
//                        $this->sendCallback($this->_apps[$name][$id]->getResponse(), $this->_apps[$name][$id]->getCallback());
//                        echo time() . " " . "$name {$this->_apps[$name][$id]->getIdentifier()} moveAction Бот \n";
                    });

                if (!$app->isSaved() && $app->isOver()) {
                    echo time() . " $name $id приложение завершилось, записываем данные\n";
                    $this->saveGame($app);
                }

                if ($app->isOver() && count($app->getClients()) < $class::GAME_PLAYERS) {
                    unset($this->_apps[$name][$id]);
                    echo time() . " $name $id удаляем приложение \n";
                }
            }
        }
    }

    public function onOpen(ConnectionInterface $conn)
    {

        if($conn->Session->has(Player::IDENTITY)) {

            $player = $conn->Session->get(Player::IDENTITY);
            $conn->resourceId = $player->getId();
            $this->_clients[$player->getId()] = $conn;
            $this->_players[$player->getId()] = array('Id'=>$player->getId(),'Country'=>$player->getCountry());

            echo time()." "."New connection: #{$conn->resourceId} " . $conn->Session->getId() . "\n";

            if(isset($this->_players[$player->getId()])){
                echo time()." ". "Выход игрока при соединении {$player->getId()}\n";
                $this->quitPlayer($player->getId());
            }

            $sql = "SELECT Points, Money FROM `Players` WHERE `Id`=:id LIMIT 1";


            try {
                $sth = DB::Connect()->prepare($sql);
                $sth->execute(array(':id' => $player->getId()));
            } catch (PDOException $e) {
                throw new ModelException("Error processing storage query", 500);
            }

            if (!$sth->rowCount()) {
                throw new ModelException("Player not found", 404);
            }

            $balance = $sth->fetch();

            // echo time() . " SQL " . $sql . "\n";

            $conn->send(json_encode(
                    array('path' => 'update',
                        'res' => array(
                            'money' => $balance['Money'],
                            'points' => $balance['Points']
                        )))
            );

            // $this->_class='chat';
            // $this->sendCallback($this->_clients, array('message'=>$conn->Session->get(Player::IDENTITY)->getNicName().' присоединился'));
        }

    }

    public function onMessage(ConnectionInterface $from, $msg) {

        if($player = $from->Session->get(Player::IDENTITY)) {
            $data = json_decode($msg);
            list($type, $name, $id) = array_pad(explode("/", $data->path),3,0);
            #echo time()." ". "#{$from->resourceId}: " . (isset($data->data->action) ? $data->data->action : '') . " - " . $data->path . " \n";
            if(isset($data->data))
                $data = $data->data;
            $this->_class = $class = '\\' . $name;
            $action = (isset($data->action) ? $data->action : '') . 'Action';
            $mode = ((isset($data->mode) AND in_array($data->mode,$this->_modes)) ? $data->mode : $this->_modes[0]);

            switch ($type) {
                case 'app':
                    try {

                        if (class_exists($class)) {

                            // нет запущенного приложения, пробуем создать новое или просто записаться в очередь
                            if (!$id) {
                                #echo time() . " " . "id приложения нет \n";

                                if ($action == 'cancelAction' || $action == 'quitAction') {

                                    if (isset($this->_players[$from->resourceId]['appMode'])) {
                                        #echo time() . " " . "Игрок {$from->resourceId} отказался ждать в стеке новой игры \n";
                                        unset(
                                            $this->_stack[$name][$this->_players[$from->resourceId]['appMode']][$player->getId()],
                                            $this->_players[$from->resourceId]['appName'],
                                            $this->_players[$from->resourceId]['appMode']);
                                    }

                                } elseif ($action == 'startAction') {

                                    list($currency, $price) = explode("-", $mode);

                                    if($this->checkBalance($player->getId(), $currency, $price)){

                                        #echo time() . " " . "Игрок {$from->resourceId} записался в стек новой игры \n";
                                        $this->_stack[$name][$mode][$player->getId()] =
                                            (object) array(
                                                'time'      =>  time(),
                                                'id'        =>  $player->getId(),
                                                'avatar'    =>  $player->getAvatar(),
                                                'name'      =>  $player->getNicName());
                                        $this->_players[$from->resourceId]['appName'] = $name;
                                        $this->_players[$from->resourceId]['appMode'] = $mode;

                                        $success=false;

                                        // если насобирали минимальную очередь
                                        if (count($this->_stack[$name][$mode]) >= $class::STACK_PLAYERS
                                            AND count($this->_stack[$name][$mode]) >= $class::GAME_PLAYERS) {

                                            // перемешали игроков
                                            $keys = array_keys($this->_stack[$name][$mode]);
                                            shuffle($keys);

                                            // начали проверять стек на игру, так как могут быть те, кто не желает играть друг с другом
                                            foreach ($keys as $key) {
                                                $clients[$key] = $this->_stack[$name][$mode][$key];
                                                // дошли до необходимого числа и прервали
                                                if (count($clients) == $class::GAME_PLAYERS) {
                                                    $success = true;
                                                    break;
                                                }
                                            }
                                        }

                                        if ($success) {
                                            $this->initGame($clients,$name,$mode,$player->getId());
                                        } else {
                                            $this->sendCallback($from->resourceId,
                                                array(
                                                    'action' => 'stack',
                                                    'stack' => count($this->_stack[$name][$mode]),
                                                    'mode' => $mode));
                                        }
                                    }
                                }

                            // пробуем загрузить приложение, проверяем наличие, если есть, загружаем и удаляем игрока из стека
                            }  elseif (!isset($this->_apps[$name][$id])) {

                                if ($action == 'replayAction' || $action == 'quitAction') {
                                    echo time() . " " . "id есть, но приложения $name $id нет, заглушка\n";

                                } else {
                                    echo time() . " " . "id есть, но приложения $name $id нет, сообщаем об ошибке, удаляем из активных игроков \n";
                                    $this->sendCallback($from->resourceId, array(
                                        'action' => 'error',
                                        'error' => 'APPLICATION_DOESNT_EXISTS',
                                        'appId' => 0));
                                    unset($this->_players[$from->Session->get(Player::IDENTITY)->getId()]);
                                }

                            // если нет, сообщаем об ошибке
                            } else {

                                #echo time() . " " . "приложение нашли $name  $id\n";
                                $this->runGame($name,$id,$action,$from->resourceId,$data);
                            }

                        // если не нашли класс
                        } else {
                            $from->send(json_encode(array('error' => 'WRONG_APPLICATION_TYPE')));
                        }

                    } catch (Exception $e) {
                        $from->send($e->getMessage());
                    }
                    break;

                case 'url':
                    break;

                case 'update':

                    $sql = "SELECT count(`PlayerGames`.`Id`) Count, sum(`PlayerGames`.`Win`) `Win`,
                        (SELECT count(distinct(Id))  FROM `PlayerGames` WHERE `GameId` = :gameid) `All`
                                        FROM `Players`
                                        LEFT JOIN `PlayerGames`
                                        ON `PlayerGames`.`PlayerId` = `Players`.`Id`
                                        WHERE `Players`.`Id`=:id AND `PlayerGames`.`GameId` = :gameid
                                        LIMIT 1";
                    #echo time() . " SELECT PLAYER INFO" . "\n";

                    try {
                        $sth = DB::Connect()->prepare($sql);
                        $sth->execute(array(':id' => $from->resourceId, ':gameid' => $this->_games[$name]));
                    } catch (PDOException $e) {
                        throw new ModelException("Error processing storage query", 500);
                    }

                    if (!$sth->rowCount()) {
                        throw new ModelException("Player not found", 404);
                    }

                    $stat = $sth->fetch();
                    $stat['All'] /= $class::GAME_PLAYERS;

                    if (isset($this->_rating[$name]['timeout']) AND $this->_rating[$name]['timeout'] > time()) {
                        $top = $this->_rating[$name]['top'];
                    } else {

                        /*
                                                $sql = "SELECT count(`PlayerGames`.`Id`) Count, sum(`PlayerGames`.`Win`) `Win`,

                                                `Players`.`Id`, `Players`.`Nicname`, `Players`.`Avatar`
                                                FROM `Players`
                                                LEFT JOIN
                                                (SELECT count(Id)/count(DISTINCT(PlayerId)) FROM `PlayerGames` WHERE `GameId` = :gameid)

                                                LEFT JOIN `PlayerGames`
                                                ON `PlayerGames`.`PlayerId` = `Players`.`Id`
                                                WHERE `PlayerGames`.`GameId` = :gameid AND Count >
                                                GROUP BY `Players`.`Id`
                                                ORDER BY (`Win`/count(`PlayerGames`.`Id`)) DESC
                                                LIMIT 10";

                                                $sql = "SELECT sum(g.Win) W, count(g.Id) T, p.Nicname N,  p.Avatar A, p.Id I,
                                                ( ( count(g.Id) / ( count(g.Id) + (count(DISTINCT(g.`GameUid`))/count(DISTINCT(g.`PlayerId`))) ) ) * ( (sum(g.`Win`) /  count(g.Id) + 1) )
                                                +
                                                ( ( count(g.Id) + (count(DISTINCT(g.`GameUid`))/count(DISTINCT(g.`PlayerId`))) ) / ( count(g.Id) + ( count(g.Id) + (count(DISTINCT(g.`GameUid`))/count(DISTINCT(g.`PlayerId`))) ) ) ) * 1.5 )
                                                R
                                                FROM `PlayerGames` g
                                                LEFT JOIN Players p ON p.Id=g.PlayerId
                                                WHERE g.GameId = :gameid
                                                group by PlayerId
                                                having count(DISTINCT(g.`GameUid`))  > (count(DISTINCT(g.`GameUid`))/count(DISTINCT(g.`PlayerId`))) OR g.PlayerId = :playerid
                                                ORDER By
                                                ( count(g.Id) / ( count(g.Id) + (count(DISTINCT(g.`GameUid`))/count(DISTINCT(g.`PlayerId`))) ) ) * ( (sum(g.`Win`) /  count(g.Id) + 1) )
                                                +
                                                ( ( count(g.Id) + (count(DISTINCT(g.`GameUid`))/count(DISTINCT(g.`PlayerId`))) ) / ( count(g.Id) + ( count(g.Id) + (count(DISTINCT(g.`GameUid`))/count(DISTINCT(g.`PlayerId`))) ) ) ) * 1.5
                                                LIMIT 11";

                        $sql = "SELECT sum(g.Win) W, count(g.Id) T, p.Nicname N,  p.Avatar A, p.Id I, (sum(g.Win)/count(g.Id)) R
                                FROM `PlayerGames` g
                                LEFT JOIN Players p On p.Id=g.PlayerId
                                where g.GameId = :gameid
                                group by g.PlayerId
                                having T > (SELECT (count(Id) / count(distinct(PlayerId)) / " . $class::GAME_PLAYERS . " ) FROM PlayerGames WHERE GameId = :gameid)
                                order by R DESC, T DESC
                                LIMIT 10";

                        */

                        $sql = "SELECT sum(g.Win) W, count(g.Id) T, p.Nicname N,  p.Avatar A, p.Id I, (sum(g.Win)*5+count(g.Id)) R
                                FROM `PlayerGames` g
                                JOIN Players p On p.Id=g.PlayerId
                                where g.GameId = :gameid
                                group by g.PlayerId
                                having T > (SELECT (count(Id) / count(distinct(PlayerId)) / " . $class::GAME_PLAYERS . " ) FROM PlayerGames WHERE GameId = :gameid)
                                order by R DESC, T DESC
                                LIMIT 10";

                        #echo time() . " SELECT TOP\n";

                        try {
                            $sth = DB::Connect()->prepare($sql);
                            $sth->execute(
                                array(
                                    ':gameid' => $this->_games[$name],
                                    ':playerid' => $from->resourceId
                                ));
                        } catch (PDOException $e) {
                            throw new ModelException("Error processing storage query", 500);
                        }

                        $top = array();
                        foreach ($sth->fetchAll() as $player) {
                            $player['O'] = ((isset($this->_players[$player['I']]['appName']) && $this->_players[$player['I']]['appName'] == $name ? 1 : 0));
                            $top[] = $player;
                        }

                        $this->_rating[$name]['top'] = $top;
                        $this->_rating[$name]['timeout'] = time() + 1 * 60;

                    }

                    #echo time() . " " . "Топ + обновление данных игрока\n";
                    $from->send(json_encode(array(
                        'path' => 'update',
                        'res' => array(
                            'all' => $stat['All'],
                            'count' => $stat['Count'],
                            'win' => $stat['Win'],
                            // кол-во ожидающих во всех стеках игры - количество стеков из-за рекурсии + кол-во игр * кол-во игроков
                            'online' =>
                                ((isset($this->_stack[$name]) ? count($this->_stack[$name], COUNT_RECURSIVE) - count($this->_stack[$name]) : 0) +
                                    (isset($this->_apps[$name]) ? count($this->_apps[$name]) * $class::GAME_PLAYERS : 0))+10,
                            'top' => $top
                        ))));

                    break;

                default:
                    if(isset($data->message)){

                        if ($data->message == 'stop') {
                            call_user_func($this->_shutdown);
                            //die;
                        } elseif ($data->message == 'online') {

                            $from->send(json_encode(
                                array(
                                    'path' => 'appchat',
                                    'res' => array(
                                        'user' => 'system',
                                        'message' => 'Игроков онлайн - ' . count($this->_clients))
                                )
                            ));

                        } elseif ($data->message == 'players') {

                            foreach ($this->_clients as $client)
                                $names[] = $client->Session->get(Player::IDENTITY)->getNicName();
                            $from->send(json_encode(
                                array(
                                    'path' => 'appchat',
                                    'res' => array(
                                        'user' => 'system',
                                        'message' => 'Игроки онлайн - '. count($this->_clients).': ' . implode(', ', $names))
                                )
                            ));

                        } elseif ($data->message == 'games') {
                            $games='';
                            $count=0;
                            foreach ($this->_apps as $apps_class)
                                foreach ($apps_class as $app)
                                {
                                    $count++;
                                    $games .= $app->getTitle().' ['.$app->getCurrency().'-'.$app->getPrice().'] '.(time()-$app->getTime()).'s ';
                                    $names = array();
                                    foreach ($app->getPlayers() as $name)
                                        $names[] = $name['pid'];
                                    $games.=(!empty($names)?implode(':',$names).'<br>':'');
                                }

                            $from->send(json_encode(
                                array(
                                    'path' => 'appchat',
                                    'res' => array(
                                        'user' => 'system',
                                        'message' => 'Игр онлайн - ' . $count . ($count>0?'<br>'.$games:'')
                                    )
                                )
                            ));

                        } elseif ($data->message == 'stack') {
                            $stack='';
                            $count=0;

                            foreach ($this->_stack as $class=>$stack_class)
                                foreach ($stack_class as $mode=>$players)
                                {
                                    $count++;
                                    $names=array();
                                    $stack .= $class.' ['.$mode.'] ';
                                    foreach ($players as $id=>$client)
                                        $names[] = $id;
                                    $stack.=(!empty($names)?implode(',',$names).'<br>':'');
                                }
                            $from->send(json_encode(
                                array(
                                    'path' => 'appchat',
                                    'res' => array(
                                        'user' => 'system',
                                        'message' => 'В стеке - ' . $count . ($count>0?'<br>'.$stack:'')
                                    )
                                )
                            ));

                        } elseif(isset($data->message)) {

                            foreach ($this->_clients as $client) {
                                $client->send(json_encode(
                                    array(
                                        'path' => 'appchat',
                                        'res' => array(
                                            'uid' => $player->getId(),
                                            'user' => $player->getNicName(),
                                            'message' => $data->message)
                                    )
                                ));
                            }
                        }
                    } else {
                        echo time().' default '.(json_encode($msg));
                    }
                    break;
            }
            /* */
        }
    }

    public function onClose(ConnectionInterface $conn) {

        if($conn->Session->get(Player::IDENTITY)){
            if(isset($this->_players[$conn->resourceId])){
                echo time()." ". "Выход игрока при разъединении {$conn->resourceId}\n";
                $this->quitPlayer($conn->resourceId);
            }

/*        foreach ($this->_clients as $client) {
            $client->send(json_encode(
                array(
                    'path'=>'appchat',
                    'res'=>array(
                        'message'=>$conn->Session->get(Player::IDENTITY)->getNicName().' отсоединился')
                )));
        }
*/
        }

        if(isset($this->_clients[$conn->resourceId])){
            unset($this->_clients[$conn->resourceId]);
            echo time()." ". "Connection {$conn->resourceId} has disconnected\n";
        }
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {

        if($e->getCode() == 'HY000' || stristr($e->getMessage(), 'server has gone away')) {
            echo time()." ". "{$e->getMessage()} RECONNECT \n";
            DB::Reconnect('default', Config::instance()->dbConnectionProperties);
        } else {
            echo time()." ". "An error has occurred: {$e->getMessage()}\n";
            $conn->close();
        }
    }



    public function sendCallback($response, $callback,$class=null) {

        if(!$class)
            $class=$this->_class;

        // рассылаем игрокам результат обработки
        if(is_array($response))
            foreach( $response as $client ) {
                if(!isset($client->bot) && isset($this->_clients[$client->id]) && ($this->_clients[$client->id] instanceof ConnectionInterface))
                $this->_clients[$client->id]->send(
                    json_encode(
                        array(
                            'path' => 'app' . $class,
                            'res' => $callback
                        )));
            }
        else {
            if(!isset($response->bot) && isset($this->_clients[$response]) && ($this->_clients[$response] instanceof ConnectionInterface))
            $this->_clients[$response]->send(
                json_encode(
                    array(
                        'path' => 'app' . $class,
                        'res' => $callback
                    )));
        }
    }

    private function checkBalance($pid, $currency, $price) {

        $sql = "SELECT Points, Money FROM `Players` WHERE `Id`=:id LIMIT 1";
        echo time() . " " . $sql . " || $pid \n";

        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(':id' => $pid));
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query", 500);
        }

        if (!$sth->rowCount()) {
            throw new ModelException("Player not found", 404);
        }

        $balance = $sth->fetch();
        if ($currency == 'MONEY'
            ? $balance['Money'] < $price * $this->_settings->getCountryCoefficient((in_array($this->_players[$pid]['Country'], Config::instance()->langs) ? $this->_players[$pid]['Country'] : Config::instance()->defaultLang))
            : $balance['Points'] < $price) {
            return false;
        } else
            return true;
    }

    public function quitPlayer($playerId) {

        if (isset($this->_players[$playerId]['appName'])){
            $class = $this->_players[$playerId]['appName'];
            $mode = $this->_players[$playerId]['appMode'];
            if(isset($this->_players[$playerId]['appId'])){
                echo time()." ". $this->_players[$playerId]['appName']. $this->_players[$playerId]['appId']. " удаление appId у игрока №$playerId\n";
                $id = $this->_players[$playerId]['appId'];
            }
        }

        // сдаемся и выходим, сохраняем и удаляем игру
        if (isset($class))
        {
            if(isset($this->_stack[$class][$mode][$playerId])){
                unset($this->_stack[$class][$mode][$playerId]);
                echo time()." ". "$class Удаление игрока из игрового стека ожидающих \n";
            }

            echo time()." ". "Удаление игрока №$playerId из массива игроков \n";
            unset($this->_players[$playerId]);

            if(isset($this->_stack[$class][$mode]) AND count($this->_stack[$class][$mode])==0){
                echo time()." ". "$class Удаление стека ожидающих игроков {$class} {$mode}\n";
                unset($this->_stack[$class][$mode]);
            }

            if (isset($id) AND isset($this->_apps[$class][$id])) {

                $app = $this->_apps[$class][$id];
                // если есть игра - сдаемся
                $app->setClient($playerId);

                if (!$app->isOver()) {
                    echo time()." ". "$class $id Игра активная - сдаемся\n";
                    $app->passAction();
                    $this->sendCallback($app->getResponse(), $app->getCallback());
                }

                // сигнализируем об уходе и отключаемся от игры
                #echo time()." ". "$class $id Сигнализируем об уходе\n";
                $app->quitAction();
                $this->sendCallback($app->getResponse(), $app->getCallback());

                // если приложение завершилось и не сохранено, сохраняем
                if ($app->isOver() && !$app->isSaved()) {
                    echo time()." ". "$class $id Сохраняем результаты"."\n";
                    $this->saveGame($app);
                }

                // если приложение завершилось и сохранено, выгружаем из памяти
                if ($app->isOver() && $app->isSaved()) {
                    echo time() . " $class $id удаляем приложение \n";
                    unset($this->_apps[$class][$id]);
                }
            }
        }

    }

    function saveGame($app){

        echo time()." ".array_search($app->getId(),$this->_games).' '.$app->getIdentifier(). " Состояние игры: ".$app->_isSaved."/".$app->_isOver." - Сохраняем игру \n";
        $app->_isSaved = 1;
        echo time()." ". "Результаты: "; print_r($app->getPlayers());

        $sql_results = "INSERT INTO `PlayerGames`
        (`PlayerId`, `GameId`, `GameUid`, `Date`, `Win`, `Lose`, `Draw`, `Result`, `Currency`, `Price`)
        VALUES (?,?,?,?,?,?,?,?,?,?)".
            str_repeat(',(?,?,?,?,?,?,?,?,?,?)', count($app->getPlayers())-1);

        if($app->getPrice())
            $sql_transactions = "INSERT INTO `Transactions` (`PlayerId`, `Currency`, `Sum`, `Balance`, `Description`, `Date`) VALUES ";

        $results = $transactions = array();

        foreach($app->getPlayers() as $player)
        {
            if($app->getPrice() AND $player['result']!=0 AND !isset($app->getClients()[$player['pid']]->bot)) {
                $sql_transactions_players[]='(?,?,?,?,?,?)';

                $currency=$app->getCurrency()=='MONEY'?'Money':'Points';
                $price=($currency=='Money'?
                    $app->getPrice()*$this->_settings->getCountryCoefficient((in_array($this->_clients[$player['pid']]->Session->get(Player::IDENTITY)->getCountry(), Config::instance()->langs) ? $this->_clients[$player['pid']]->Session->get(Player::IDENTITY)->getCountry() : Config::instance()->defaultLang ))
                    :$app->getPrice());

                /* update balance after game */
                $sql="UPDATE Players SET ".$currency." = ".$currency.($player['result'] < 0 ? '' : '+').
                    ($player['result']*$price)." WHERE Id=".$player['pid'];

                #echo time()." ".$sql."\n";

                try{
                    DB::Connect()->query($sql);
                }
                catch(\Exception $e)
                {
                    #echo time()." ". $e->getMessage();
                }


                /* send new balance to player */
                if(isset($this->_clients[$player['pid']])) {
                    $sql = "SELECT Points, Money FROM `Players` WHERE `Id`=:id LIMIT 1";

                    #echo time() . " " . $sql . "\n";

                    try {
                        $sth = DB::Connect()->prepare($sql);
                        $sth->execute(array(':id' => $player['pid']));
                    } catch (PDOException $e) {
                        throw new ModelException("Error processing storage query", 500);
                    }

                    if (!$sth->rowCount()) {
                        throw new ModelException("Player not found", 404);
                    }

                    $balance = $sth->fetch();

                    $this->_clients[$player['pid']]->send(json_encode(
                            array('path'=>'update',
                                'res'=>array(
                                    'money'=>$balance['Money'],
                                    'points'=>$balance['Points']
                                )))
                    );

                }

/*
                if(isset($this->_clients[$player['pid']])){
                    $this->_clients[$player['pid']]->Session->get(Player::IDENTITY)->setMoney(
                        $this->_clients[$player['pid']]->Session->get(Player::IDENTITY)->getMoney()+
                        ($currency=='Money'?$app->getPrice()*$player['result']:0)
                    );
                    $money=$this->_clients[$player['pid']]->Session->get(Player::IDENTITY)->getMoney();

                    $this->_clients[$player['pid']]->Session->get(Player::IDENTITY)->setPoints(
                        $this->_clients[$player['pid']]->Session->get(Player::IDENTITY)->getPoints()+
                        ($currency=='Points'?$app->getPrice()*$player['result']:0)
                    );

                    $points=$this->_clients[$player['pid']]->Session->get(Player::IDENTITY)->getPoints();


                }
*/

                /* prepare transactions */
                array_push($transactions,
                    $player['pid'],
                    $app->getCurrency(),
                    $price*$player['result'],
                    $balance[$currency],
                    'Игра '.$app->getTitle(),
                    time()
                );

            }

            array_push($results,
                $player['pid'],
                $app->getId(),
                $app->getIdentifier(),
                time(),
                ($player['result'] == 1?1:0),  // win
                ($player['result'] == -1?1:0), // lose
                ($player['result'] == 0?1:0),  // draw
                $player['result'],
                $app->getCurrency(),
                $app->getPrice()
            );
        }

        // echo time(); print_r($results); echo "\n";

        try {
            DB::Connect()->prepare($sql_results)->execute($results);
            echo time()." ". "MYSQL Записали результаты игры в базу\n";
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query" . $e->getMessage(), 500);
        }

        if($app->getPrice()) {
            $sql=$sql_transactions.implode(",",$sql_transactions_players);
            // echo time()." ".$sql."\n";
            try {
                DB::Connect()->prepare($sql)->execute($transactions);
                echo time()." ". "MYSQL Записали транзакции в базу\n";
            } catch (PDOException $e) {
                throw new ModelException("Error processing storage query" . $e->getMessage(), 500);
            }
        }
    }
}