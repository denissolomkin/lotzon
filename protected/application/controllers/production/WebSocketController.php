<?php namespace controllers\production;

 use Ratchet\MessageComponentInterface;
 use Ratchet\ConnectionInterface;
 use Ratchet\Wamp\Exception;
 use \Player, \DB, \Config, \Application;

 Application::import(PATH_APPLICATION . '/model/entities/Player.php');
 require_once dirname(__DIR__) . '/../../system/DB.php';
 require_once dirname(__DIR__) . '/../../system/Config.php';
 require_once dirname(__DIR__) . '/../../../protected/configs/config.php';

class WebSocketController implements MessageComponentInterface {

    private $_clients=array();
    private $_stack=array();
    private $_apps=array();
    private $_players=array();
    private $_class;
    private $_rating=array();
    private $_games=array(
        'NewGame' => 1
    );
    private $_mode='POINT-0';
    protected $_shutdown;

    public function __construct($shutdown) {
        echo time()." ". "Server have started\n";
        $this->_clients = array();  // Create a collection of client
        $this->_shutdown = $shutdown;
    }

    public function onOpen(ConnectionInterface $conn)
    {
        if($conn->Session->get(Player::IDENTITY)) {


            $playerId = $conn->Session->get(Player::IDENTITY)->getId();
            $conn->resourceId = $playerId;
            $this->_clients[$playerId] = $conn;

            echo time()." ". "New connection: #{$conn->resourceId} " . $conn->Session->getId() . "\n";

            if(isset($this->_players[$playerId])){
                echo time()." ". "Выход игрока при соединении {$playerId}\n";
                $this->quitPlayer($playerId);
            }

            $sql = "SELECT Points, Money FROM `Players` WHERE `Id`=:id LIMIT 1";

            echo time() . " " . $sql . "\n";

            try {
                $sth = DB::Connect()->prepare($sql);
                $sth->execute(array(':id' => $playerId));
            } catch (PDOException $e) {
                throw new ModelException("Error processing storage query", 500);
            }

            if (!$sth->rowCount()) {
                throw new ModelException("Player not found", 404);
            }

            $player = $sth->fetch();

            $conn->send(json_encode(
                    array('path' => 'update',
                        'res' => array(
                            'money' => $player['Money'],
                            'points' => $player['Points']
                        )))
            );

            // $this->_class='chat';
            // $this->sendCallback($this->_clients, array('message'=>$conn->Session->get(Player::IDENTITY)->getNicName().' присоединился'));
        }

    }

    public function onMessage(ConnectionInterface $from, $msg) {

        if($from->Session->get(Player::IDENTITY)) {
            $data = json_decode($msg);
            list($type, $name, $id) = array_pad(explode("/", $data->path),3,0);
            echo time()." ". "#{$from->resourceId}: " . (isset($data->data->action) ? $data->data->action : '') . " - " . $data->path . " \n";
            if(isset($data->data))
                $data = $data->data;
            $this->_class = $class = '\\' . $name;
            $action = (isset($data->action) ? $data->action : '') . 'Action';
            $mode = (isset($data->mode) ? $data->mode : $this->_mode);
            $player = $from->Session->get(Player::IDENTITY);

            switch ($type) {
                case 'app':
                    try {

                        // $this->sendCallback($this->_clients, array('message'=>$player->getNicName().": ".$action),'chat');
                        if (class_exists($class)) {
                            // нет запущенного приложения, пробуем создать новое или просто записаться в очередь
                            if (!$id) {
                                echo time()." ". "id приложения нет \n";
                                // записались

                                if ($action == 'cancelAction' || $action == 'quitAction') {

                                    if (isset($this->_players[$from->resourceId]['appMode'])) {
                                        echo time()." ". "Игрок {$from->resourceId} отказался ждать в стеке новой игры \n";
                                        unset(
                                            $this->_stack[$name][$this->_players[$from->resourceId]['appMode']][$player->getId()],
                                            $this->_players[$from->resourceId]['appName'],
                                            $this->_players[$from->resourceId]['appMode']);
                                    }

                                } elseif ($action == 'startAction') {

                                    list($currency, $price) = explode("-", $mode);

                                    $sql = "SELECT Points, Money FROM `Players` WHERE `Id`=:id LIMIT 1";
                                    echo time() . " " . $sql . "\n";

                                    try {
                                        $sth = DB::Connect()->prepare($sql);
                                        $sth->execute(array(':id' => $from->resourceId));
                                    } catch (PDOException $e) {
                                        throw new ModelException("Error processing storage query", 500);
                                    }

                                    if (!$sth->rowCount()) {
                                        throw new ModelException("Player not found", 404);
                                    }

                                    $funds = $sth->fetch();

                                    if ($funds[($currency == 'MONEY' ? 'Money' : 'Points')] < $price) {
                                        echo time()." ". "Игрок {$from->resourceId} - недостаточно средств для игры\n";
                                        $from->send(json_encode(array('error' => 'INSUFFICIENT_FUNDS')));

                                    } else {

                                        echo time()." ". "Игрок {$from->resourceId} записался в стек новой игры \n";
                                        $this->_stack[$name][$mode][$player->getId()] = $from;
                                        $this->_players[$from->resourceId]['appName'] = $name;
                                        $this->_players[$from->resourceId]['appMode'] = $mode;


                                        // если насобирали минимальную очередь
                                        if (count($this->_stack[$name][$mode]) >= $class::STACK_PLAYERS
                                            AND count($this->_stack[$name][$mode]) >= $class::GAME_PLAYERS
                                        ) {

                                            // перемешали игроков
                                            $keys = array_keys($this->_stack[$name][$mode]);
                                            shuffle($keys);

                                            $app = new $class();
                                            $app->setCurrency($currency)->setPrice((float)$price);

                                            // начали формировать список на игру
                                            foreach ($keys as $key) {
                                                $clients[$key] = $this->_stack[$name][$mode][$key];
                                                unset ($this->_stack[$name][$mode][$key]);
                                                $this->_players[$key]['appId'] = $app->getIdentifier();

                                                // дошли до необходимого числа и прервали
                                                if (count($clients) == $class::GAME_PLAYERS)
                                                    break;
                                            }
                                            if (isset($this->_stack[$name][$mode]) AND count($this->_stack[$name][$mode]) == 0)
                                                unset($this->_stack[$name][$mode]);

                                            // запускаем и кешируем приложение
                                            $app->setClients($clients);
                                            $app->setClient($from);
                                            $this->_apps[$name][$app->getIdentifier()] = $app;


                                        } else {
                                            $this->sendCallback($from, array('action' => 'stack', 'stack' => count($this->_stack[$name][$mode]), 'mode' => $mode));
                                        }
                                    }
                                }
                            } // пробуем загрузить приложение, проверяем наличие, если есть, загружаем и удаляем игрока из стека
                            elseif (isset($this->_apps[$name][$id])) {
                                $app = $this->_apps[$name][$id];
                                echo time()." ". "приложение нашли $name " . $app->getCurrency() . $app->getPrice() . " $id\n";

                                // если нет, сообщаем об ошибке
                            } else {
                                if($action=='replayAction' || $action=='quitAction'){
                                    echo time()." ". "id есть, но приложения $name $id нет, заглушка\n";
                                } else {
                                    echo time()." ". "id есть, но приложения $name $id нет, сообщаем об ошибке, удаляем из активных игроков \n";
                                    $this->sendCallback($from, array(
                                        'action' => 'error',
                                        'error' => 'APPLICATION_DOESNT_EXISTS',
                                        'appId' => 0));
                                    unset($this->_players[$from->Session->get(Player::IDENTITY)->getId()]);
                                }
                            }

                            // если приложение запустили или загрузили
                            if (isset($app)) {
                                echo time()." ". "стартуем приложение $name {$app->getIdentifier()} \n";

                                // пробуем вызвать экшн
                                $app->setClient($from);
                                call_user_func(array($app, $action), $data);

                                // рассылаем игрокам результат обработки
                                $this->sendCallback($app->getResponse(), $app->getCallback());

                                // если приложение завершилось, записываем данные и выгружаем из памяти
                                if (!$app->isSaved() && $app->isOver()) {
                                    $this->saveGame($app);
                                }

                                if ($app->isOver() && count($app->getClients()) < $class::GAME_PLAYERS) {
                                    unset($this->_apps[$name][$id]);
                                    echo time()." ". "удаление приложения" . $name . $id . "\n";
                                }

                            }
                        } // если не нашли класс
                        else {
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
                        (SELECT count(Id)  FROM `PlayerGames` WHERE `GameId` = :gameid) `All`
                                        FROM `Players`
                                        LEFT JOIN `PlayerGames`
                                        ON `PlayerGames`.`PlayerId` = `Players`.`Id`
                                        WHERE `Players`.`Id`=:id AND `PlayerGames`.`GameId` = :gameid
                                        LIMIT 1";
                    echo time() . " SELECT PLAYER INFO" . "\n";

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
                    $stat['All']/=$class::GAME_PLAYERS;

                    if (isset($this->_rating[$name]['timeout']) AND $this->_rating[$name]['timeout'] > time()) {
                        $top = $this->_rating[$name]['top'];
                    } else {


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


                        $sql = "SELECT
sum(g.Win) W, count(g.Id) T, p.Nicname N,  p.Avatar A, p.Id I,
(sum(g.Win)/count(g.Id)) R
FROM `PlayerGames` g
LEFT JOIN Players p On p.Id=g.PlayerId
where g.GameId = :gameid
group by g.PlayerId
having T > (SELECT (count(Id) / count(distinct(PlayerId)) / ".$class::GAME_PLAYERS." ) FROM PlayerGames WHERE GameId = :gameid)
order by R DESC, T DESC
LIMIT 10";
                        echo time() . " SELECT TOP\n";

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
                            $player['O'] = ( (isset($this->_players[$player['I']]['appName']) && $this->_players[$player['I']]['appName'] == $name ? 1 : 0) );
                            $top[] = $player;
                        }

                        $this->_rating[$name]['top'] = $top;
                        $this->_rating[$name]['timeout'] = time() + 5 * 60;

                    }

                    echo time()." ". "Топ + обновление данных игрока\n";
                    $from->send(json_encode(array(
                        'path' => 'update',
                        'res' => array(
                            'all' => $stat['All'],
                            'count' => $stat['Count'],
                            'win' => $stat['Win'],
                            // кол-во ожидающих во всех стеках игры - количество стеков из-за рекурсии + кол-во игр * кол-во игроков
                            'online' =>
                                ((isset($this->_stack[$name]) ? count($this->_stack[$name], COUNT_RECURSIVE) - count($this->_stack[$name]) : 0 ) +
                                (isset($this->_apps[$name]) ? count($this->_apps[$name]) * $class::GAME_PLAYERS : 0)),
                            'top' => $top
                        ))));

                    break;

                default:
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
                    } else
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
            foreach( $response as $client )
                $client->send(
                    json_encode(
                        array(
                            'path'=>'app'.$class,
                            'res'=>$callback
                        )));
        else
            $response->send(
                json_encode(
                    array(
                        'path'=>'app'.$class,
                        'res'=>$callback
                    )));
    }

    public function quitPlayer($playerId) {

        if (isset($this->_players[$playerId]['appName'])){
            echo time()." ". "Удаление маячка игры ".$this->_players[$playerId]['appName'].$this->_players[$playerId]['appMode']."\n";
            $class = $this->_players[$playerId]['appName'];
            $mode = $this->_players[$playerId]['appMode'];
            if(isset($this->_players[$playerId]['appId'])){
                echo time()." ". "Выход маячка Id игры #".$this->_players[$playerId]['appId']."\n";
                $id = $this->_players[$playerId]['appId'];
            }
        }

        // сдаемся и выходим, сохраняем и удалеяем игру
        if (isset($class))
        {
            if(isset($this->_stack[$class][$mode][$playerId])){
                unset($this->_stack[$class][$mode][$playerId]);
                echo time()." ". "Удаление игрока из игрового стека ожидающих \n";
            }

            echo time()." ". "Удаление игрока из массива игроков \n";
            unset($this->_players[$playerId]);

            if(isset($this->_stack[$class][$mode]) AND count($this->_stack[$class][$mode])==0){
                echo time()." ". "Удаление стека ожидающих игроков {$class} {$mode}\n";
                unset($this->_stack[$class][$mode]);
            }

            if (isset($id) AND isset($this->_apps[$class][$id])) {

                $app = $this->_apps[$class][$id];
                // если есть игра - сдаемся
                $app->setClient($this->_clients[$playerId]);

                if (!$app->isOver()) {
                    echo time()." ". "Игра активная - сдаемся\n";
                    $app->passAction();
                    $this->sendCallback($app->getResponse(), $app->getCallback());
                }

                // сигнализируем об уходе и отключаемся от игры
                echo time()." ". "Сигнализируем об уходе\n";
                $app->quitAction();
                $this->sendCallback($app->getResponse(), $app->getCallback());

                // если приложение завершилось, сохраняем и выгружаем из памяти
                if ($app->isOver() && !$app->isSaved()) {
                    echo time()." ". "Сохраняем результаты".$class.$id."\n";
                    $this->saveGame($app);
                    unset($this->_apps[$class][$id]);
                    echo time()." ". "удаление приложения".$class.$id."\n";
                }
            }
        }

    }

    function saveGame($app){

        echo time()." ". "Состояние игры:".$app->_isSaved."/".$app->_isOver." - Сохраняем игру #".$app->getId()."\n";
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
            if($app->getPrice() AND $player['result']!=0) {
                $sql_transactions_players[]='(?,?,?,?,?)';

                $currency=$app->getCurrency()=='MONEY'?'Money':'Points';

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

                    $this->_clients[$player['pid']]->send(json_encode(
                            array('path'=>'update',
                                'res'=>array(
                                    'money'=>$money,
                                    'points'=>$points
                                )))
                    );

                }

                array_push($transactions,
                    $player['pid'],
                    $app->getCurrency(),
                    $app->getPrice()*$player['result'],
                    ($currency=='Money' ? $money : $points),
                    'Игра '.$app->getTitle(),
                    time()
                );

                $sql="UPDATE Players SET ".$currency." = ".$currency.($player['result'] < 0 ? '' : '+').
                    ($player['result']*$app->getPrice())." WHERE Id=".$player['pid'];

                echo time()." ".$sql."\n";

                try{
                    DB::Connect()->query($sql);
                }
                catch(\Exception $e)
                {
                    echo time()." ". $e->getMessage();
                }

            }

            array_push($results,
                $player['pid'],
                $app->getId(),
                $app->getIdentifier(),
                time(),
                ($player['result'] == 1?1:0),
                ($player['result'] == -1?1:0),
                ($player['result'] == 0?1:0),
                $player['result'],
                $app->getCurrency(),
                $app->getPrice()
            );
        }

        echo time(); print_r($results); echo "\n";

        try {
            DB::Connect()->prepare($sql_results)->execute($results);
            echo time()." ". "Записали результаты игры в базу\n";
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query" . $e->getMessage(), 500);
        }

        if($app->getPrice()) {
            $sql=$sql_transactions.implode(",",$sql_transactions_players);
            echo time()." ".$sql."\n";
            try {
                DB::Connect()->prepare($sql)->execute($transactions);
                echo time()." ". "Записали транзакции в базу\n";
            } catch (PDOException $e) {
                throw new ModelException("Error processing storage query" . $e->getMessage(), 500);
            }
        }
    }
}