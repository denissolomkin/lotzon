<?php namespace controllers\production;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Ratchet\Wamp\Exception;
use \Player, \DB, \Config, \Application, \OnlineGamesModel, \LotterySettingsModel;


Application::import(PATH_APPLICATION . '/model/Game.php');
Application::import(PATH_APPLICATION . '/model/entities/Player.php');
Application::import(PATH_APPLICATION . '/model/entities/LotterySettings.php');
Application::import(PATH_GAMES . '*');

class WebSocketController implements MessageComponentInterface {

    const   MIN_WAIT_TIME = 2;//15;
    const   MAX_WAIT_TIME = 600;//600;
    const   PERIODIC_TIMER = 2;
    const   CONNECTION_TIMER = 1800;

    private $_class, $_loop;
    private $_clients=array();
    private $_stack=array();
    private $_apps=array();
    private $_players=array();
    private $_bots=array();
    private $_rating=array();
    private $_defaultMode='POINT-0';

    public function __construct($loop) {

        echo $this->time(0,'START')." ". "Server have started\n";
        $this->_loop=$loop;
        $this->_loop->addPeriodicTimer(self::PERIODIC_TIMER, function () { $this->periodicTimer();});
        $this->_loop->addPeriodicTimer(self::CONNECTION_TIMER, function () { $this->checkConnections();});
        $this->_bots=\SettingsModel::instance()->getSettings('gameBots')->getValue();
        $this->_clients = array();
    }

    public function checkConnections()
    {
        foreach($this->_players as $player)
            if($player['Ping']<time()-self::CONNECTION_TIMER){
                echo $this->time()." ". "#{$player['Id']} ping timeout\n";
                $this->quitPlayer($player['Id']);
                unset($this->_players[$player['Id']]);
                if(isset($this->_clients[$player['Id']]) && $this->_clients[$player['Id']] instanceof ConnectionInterface){
                    echo $this->time(0,'CLOSE')." #{$player['Id']} {$this->_clients[$player['Id']]->Session->getId()} \n";
                    $this->_clients[$player['Id']]->close();
                }
                else
                    echo $this->time(0,'ERROR')." client #{$player['Id']} не найден в коллекции\n";
            }
    }

    public function periodicTimer()
    {
        foreach($this->_stack as $key=>$modes)
            foreach($modes as $mode=>$stacks)
                foreach($stacks as $id=>$client){
                    $game=OnlineGamesModel::instance()->getGame($key);
                    if($client->time + self::MIN_WAIT_TIME < time() && $game->getOption('b')){
                        $clients=array();
                        $clients[$id] = $client;
                        $bot=(object) $this->_bots[array_rand($this->_bots)];
                        $clients[$bot->id] = $bot;
                        $this->initGame($clients,$key,$mode,$id);
                    } elseif($client->time + self::MAX_WAIT_TIME < time()){
                        echo $this->time(0) . " $key " . "Игрок {$id} удален из стека {$this->_players[$id]['appMode']} по таймауту\n";
                        unset($this->_stack[$this->_players[$id]['appName']][$this->_players[$id]['appMode']][$id]);
                    }
                }

        foreach($this->_apps as $class=>$apps)
            foreach($apps as $id=>$app) {
                if ($app->_isOver && $app->_bot) {
                    if ($app->currentPlayer()['timeout'] - $app->getOption('t') + 10 < time()) {
                        #echo " -- таймер на выход после 10 сек \n";
                        $this->runGame($class, $app->getIdentifier(), 'quitAction', $app->_bot);
                    } elseif(!$app->_botReplay && $app->currentPlayer()['timeout'] + rand(2,4) - $app->getOption('t') < time()) {
                        if(rand(1,5)==1){
                            #echo " -- таймер на случайный выход\n";
                            $this->runGame($class, $app->getIdentifier(), 'quitAction', $app->_bot);
                        } else {
                            #echo " -- таймер на повтор \n";
                            $this->runGame($class, $app->getIdentifier(), 'replayAction', $app->_bot);
                        }
                    }
                } elseif (!$app->_isOver && $app->getTime()+$app->getOption('t') < time() && $app->currentPlayer()['timeout'] < time() && $app->currentPlayer()['pid']) {
                    #echo " -- таймер на таймаут \n";
                    $this->runGame($class, $app->getIdentifier(), 'timeoutAction', $app->currentPlayer()['pid']);
                } elseif ($app->_isOver && $app->currentPlayer()['timeout'] + 60 < time()) {
                    #echo " -- таймер на выход \n";
                    $this->runGame($class, $app->getIdentifier(), 'quitAction', $app->currentPlayer()['pid']);
                }
            }
    }

    public function initGame($clients,$name,$mode,$id)
    {
        $this->_class = $class='\\' . $name;
        $app = new $class(OnlineGamesModel::instance()->getGame($name));//new $this->_class;
        $keys = array_keys($clients);
        list($currency, $price) = explode("-", $mode);
        echo $this->time()." $name инициируем приложение $currency-$price: №".implode(', №',$keys)."\n";

        #echo $this->time()." чистим стек\n";
        foreach ($keys as $key) {
            if(isset($this->_players[$key])) {
                unset ($this->_stack[$name][$mode][$key]);
                if(isset($this->_players[$key]['appId']))
                    $this->quitPlayer($key);
                $this->_players[$key]['appName'] = $name;
                $this->_players[$key]['appId'] = $app->getIdentifier();
            }
        }

        if (isset($this->_stack[$name][$mode]) AND count($this->_stack[$name][$mode]) == 0){
            unset($this->_stack[$name][$mode]);
            #echo $this->time()." никого не осталось, удаляем стек\n";
        }

        #echo $this->time()." запускаем и кешируем приложение\n";
        $app->setClients($clients)
            ->setClient($id)
            ->setCurrency($currency)
            ->setPrice((float)$price);;
        $this->_apps[$name][$app->getIdentifier()] = $app;
        $this->runGame($name,$app->getIdentifier(),'startAction',$id);
    }


    public function runGame($name,$id,$action,$pid=null,$data=null)
    {
        if(isset($this->_apps[$name][$id]) && $app=$this->_apps[$name][$id]) {
            $this->_class = $class='\\' . $name;
            echo $this->time() . " " . "$name {$app->getIdentifier()} $action ".(!isset($app->_bot) || $pid != $app->_bot ? "игрок №" : 'бот №').$pid.($action != 'startAction'?' (текущий №'.$app->currentPlayer()['pid'].")":'')." \n";

            if ($app->_bot!=$pid && ($action == 'replayAction' && !$this->checkBalance($pid, $app->getCurrency(), $app->getPrice()))) {
                #echo $this->time() . " " . "Игрок {$from->resourceId} - недостаточно средств для игры\n";
                if($this->_clients[$pid])
                    $this->_clients[$pid]->send(json_encode(array('error' => 'INSUFFICIENT_FUNDS')));
                #echo $this->time() . " " . "прошли проверку, устанавливаем клиента \n";
            } else {

                if(isset($pid))
                    $app->setClient($pid);

                #echo $this->time() . " " . "пробуем вызвать экшн \n";
                if(!$app->_isOver OR $action != 'moveAction' ) {
                    call_user_func(array($app, $action), $data);
                }

                #echo $this->time() . " " . "рассылаем игрокам результат обработки \n";
                $this->sendCallback($app->getResponse(), $app->getCallback());

                if($app->_botTimer AND $app->currentPlayer()['pid']==$app->_bot AND !$app->_isOver) {
                    $bot = $app->_bot;
                    $this->_loop->addTimer($app->_botTimer, function () use ($name, $id, $bot) {
                        $this->runGame($name, $id, 'moveAction', $bot);
                        //echo $this->time() . " " . "$name {$this->_apps[$name][$id]->getIdentifier()} moveAction Бот \n";
                    });
                }

                if (!$app->isSaved() && $app->isOver()) {
                    echo $this->time(1) . " $name $id приложение завершилось, записываем данные\n";
                    $this->saveGame($app);
                }

                if ($app->isOver() && count($app->getClients()) < $app->getOption('p')) {
                    unset($this->_apps[$name][$id]);
                    echo $this->time(1) . " $name $id удаляем приложение \n";
                }
            }
        } else {
            echo $this->time(0,'WARNING')." $name $id для запуска не найден\n";
        }
    }

    public function onOpen(ConnectionInterface $conn)
    {

        if($conn->Session->has(Player::IDENTITY)) {

            $player = $conn->Session->get(Player::IDENTITY);
            $conn->resourceId = $player->getId();
            $this->_clients[$player->getId()]= $conn;
            $this->_players[$player->getId()] = array_merge(
                array('Id'=>$player->getId(),'Ping'=>time(),'Country'=>$player->getCountry()),
                isset($this->_players[$player->getId()]) ? $this->_players[$player->getId()] : array()
            );

            echo $this->time(0,'OPEN')." "."#{$conn->resourceId} " . $conn->Session->getId() . "\n";

            /*
            if(isset($this->_players[$player->getId()])){
                echo $this->time()." ". "Выход игрока при соединении {$player->getId()}\n";
                $this->quitPlayer($player->getId());
            }
            */

            $sql = "SELECT Points, Money FROM `Players` WHERE `Id`=:id LIMIT 1";

            try {
                $sth = DB::Connect()->prepare($sql);
                $sth->execute(array(':id' => $player->getId()));
            } catch (PDOException $e) {
                throw new ModelException("Error processing storage query", 500);
            }

            if (!$sth->rowCount()) {
                echo $this->time(0,'ERROR')." player #{$player['pid']} не найден в таблице Players при получении баланса\n";
            }

            $balance = $sth->fetch();

            // echo $this->time() . " [SQL    ] " . $sql . "\n";

            $conn->send(json_encode(
                    array('path' => 'update',
                        'res' => array(
                            'money' => $balance['Money'],
                            'points' => $balance['Points'],
                            'appName' => isset($this->_players[$player->getId()]['appName'])?$this->_players[$player->getId()]['appName']:'',
                        )))
            );

            if(isset($this->_players[$player->getId()]['appId'])){
                if(isset($this->_players[$player->getId()]['appName']))
                    $this->runGame($this->_players[$player->getId()]['appName'],$this->_players[$player->getId()]['appId'],'startAction',$player->getId());
                else
                    echo $this->time(0,'WARNING')." у игрока {$player->getId()} отсутствует appName\n";
            }
            /*
            // EMULATION GAME
            {
            $clients=array();
            $clients[$player->getId()] = (object) array(
                'time'      =>  time(),
                'id'        =>  $player->getId(),
                'avatar'    =>  $player->getAvatar(),
                'name'      =>  $player->getNicName());;
            $bot=(object) $this->_bots[array_rand($this->_bots)];
            $clients[$bot->id] = $bot;
            $this->initGame($clients,'SeaBattle','POINT-0',$player->getId());
                }
            */
            // $this->_class='chat';
            // $this->sendCallback($this->_clients, array('message'=>$conn->Session->get(Player::IDENTITY)->getNicName().' присоединился'));
        } else
            echo $this->time(0,'ERROR')." onOpen: #{$conn->resourceId} " . $conn->Session->getId() . " без Entity Player \n";

    }

    public function onMessage(ConnectionInterface $from, $msg) {

        if($player = $from->Session->get(Player::IDENTITY))
            if($player instanceof Player) {
                $this->_players[$player->getId()]['Ping'] = time();
                $data = json_decode($msg);
                list($type, $name, $id) = array_pad(explode("/", $data->path), 3, 0);
                echo $this->time(0, 'MESSAGE') . " #{$from->resourceId}: " . $data->path . (isset($data->data->action) ? " - " . $data->data->action : '') . " \n";
                $this->_class = $class = '\\' . $name;
                if (isset($data->data))
                    $data = $data->data;
                $action = (isset($data->action) ? $data->action : '') . 'Action';
                $game = OnlineGamesModel::instance()->getGame($name);
                $mode = ((isset($data->mode) AND $game->isMode($data->mode) ) ? $data->mode : $this->_defaultMode);

                if (!isset($this->_clients[$player->getId()]) || !($this->_clients[$player->getId()] instanceof ConnectionInterface)) {
                    echo $this->time(0, 'WARNING') . "  соединение #{$player->getId()} {$from->Session->getId()} не найдено в коллекции клиентов \n";
                    $this->_clients[$player->getId()]=$from;
                }

                switch ($type) {
                    case 'app':
                        try {

                            if (class_exists($class)) {

                                // нет запущенного приложения, пробуем создать новое или просто записаться в очередь
                                if (!$id) {
                                    #echo $this->time() . " " . "id приложения нет \n";

                                    if ($action == 'cancelAction' || $action == 'quitAction') {

                                        if (isset($this->_players[$from->resourceId]['appMode'])) {
                                            echo $this->time(1) . " {$this->_players[$from->resourceId]['appName']}" . " Игрок {$from->resourceId} отказался ждать в стеке {$this->_players[$from->resourceId]['appMode']}\n";
                                            unset(
                                                $this->_stack[$name][$this->_players[$from->resourceId]['appMode']][$player->getId()],
                                                $this->_players[$from->resourceId]['appName'],
                                                $this->_players[$from->resourceId]['appMode']);
                                        }

                                    } elseif ($action == 'startAction') {

                                        list($currency, $price) = explode("-", $mode);

                                        if($this->checkBalance($player->getId(), $currency, $price)){

                                            if( isset($this->_players[$from->resourceId]['appName'])
                                                && isset($this->_players[$from->resourceId]['appId'])
                                                && isset($this->_apps[$this->_players[$from->resourceId]['appName']][$this->_players[$from->resourceId]['appId']])
                                                && !$this->_apps[$this->_players[$from->resourceId]['appName']][$this->_players[$from->resourceId]['appId']]->_isOver){
                                                echo $this->time(0,'ERROR') . " " . "{$this->_players[$from->resourceId]['appName']} Запуск игроком {$from->resourceId} новой игры при незавершенной {$this->_players[$from->resourceId]['appId']}\n";
                                                return false;
                                            }

                                            if(isset($this->_players[$from->resourceId]['appName'])
                                                && isset($this->_players[$from->resourceId]['appMode'])
                                                && isset($this->_stack[$this->_players[$from->resourceId]['appName']][$this->_players[$from->resourceId]['appMode']][$player->getId()])){
                                                unset($this->_stack[$this->_players[$from->resourceId]['appName']][$this->_players[$from->resourceId]['appMode']][$player->getId()]);
                                                echo $this->time() . " " . "{$this->_players[$from->resourceId]['appName']} Игрок {$from->resourceId} выписался из стека {$this->_players[$from->resourceId]['appMode']}\n";
                                            }

                                            echo $this->time() . " " . "$name Игрок {$from->resourceId} записался в стек {$currency}-{$price}\n";
                                            $this->_stack[$name][$mode][$player->getId()] =
                                                (object) array(
                                                    'time'      =>  time(),
                                                    'id'        =>  $player->getId(),
                                                    'avatar'    =>  $player->getAvatar(),
                                                    'lang'      =>  $player->getLang(),
                                                    'name'      =>  $player->getNicName());
                                            $this->_players[$from->resourceId]['appName'] = $name;
                                            $this->_players[$from->resourceId]['appMode'] = $mode;

                                            $success=false;

                                            // если насобирали минимальную очередь
                                            if (count($this->_stack[$name][$mode]) >= $game->getOption('s')
                                                AND count($this->_stack[$name][$mode]) >= $game->getOption('p')) {

                                                // перемешали игроков
                                                $keys = array_keys($this->_stack[$name][$mode]);
                                                shuffle($keys);

                                                // начали проверять стек на игру, так как могут быть те, кто не желает играть друг с другом
                                                foreach ($keys as $key) {
                                                    $clients[$key] = $this->_stack[$name][$mode][$key];
                                                    // дошли до необходимого числа и прервали
                                                    if (count($clients) == $game->getOption('p')) {
                                                        $success = true;
                                                        break;
                                                    }
                                                }
                                            }

                                            if ($success) {
                                                $this->initGame($clients,$name,$mode,$player->getId());
                                            } else {

                                                $from->send(json_encode(
                                                    array('path' => 'stack',
                                                        'res' => array(
                                                            'stack' => count($this->_stack[$name][$mode]),
                                                            'mode' => $mode)
                                                    )));

                                                /*
                                                $this->sendCallback(array($from->resourceId),
                                                    array(
                                                        'action' => 'stack',
                                                        'stack' => count($this->_stack[$name][$mode]),
                                                        'mode' => $mode));
                                                */
                                            }
                                        }
                                    }

                                    // пробуем загрузить приложение, проверяем наличие, если есть, загружаем и удаляем игрока из стека
                                }  elseif (!isset($this->_apps[$name][$id])) {

                                    if ($action == 'replayAction' || $action == 'quitAction') {
                                        echo $this->time() . " " . "id есть, но приложения $name $id нет, {$from->resourceId} $action заглушка\n";

                                    } else {
                                        echo $this->time() . " " . "id есть, но приложения $name $id нет, сообщаем об ошибке, удаляем из активных игроков \n";
                                        $this->sendCallback(array($from->resourceId), array(
                                            'action' => 'error',
                                            'error' => 'APPLICATION_DOESNT_EXISTS',
                                            'appId' => 0));
                                        $this->quitPlayer($player->getId());
                                        //unset($this->_players[$from->Session->get(Player::IDENTITY)->getId()]);
                                    }

                                    // если нет, сообщаем об ошибке
                                } else {

                                    #echo $this->time() . " " . "приложение нашли $name  $id\n";
                                    $this->runGame($name,$id,$action,$player->getId(),$data);
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

                        $date=mktime(0, 0, 0, date("n"), 1);
                        $sql = "SELECT count(`PlayerGames`.`Id`) Count, sum(`PlayerGames`.`Win`) `Win`,
                        (SELECT count(distinct(Id))  FROM `PlayerGames` WHERE `GameId` = :gameid) `All`
                                        FROM `Players`
                                        LEFT JOIN `PlayerGames`
                                        ON `PlayerGames`.`PlayerId` = `Players`.`Id`
                                        WHERE `Players`.`Id`=:id AND `PlayerGames`.`GameId` = :gameid AND `PlayerGames`.`Date`>:dt AND `PlayerGames`.`Price`>0
                                        LIMIT 1";
                        #echo $this->time() . " SELECT PLAYER INFO" . "\n";

                        try {
                            $sth = DB::Connect()->prepare($sql);
                            $sth->execute(array(':id' => $from->resourceId, ':dt'=>$date, ':gameid' => $game->getId()));
                        } catch (PDOException $e) {
                            throw new ModelException("Error processing storage query", 500);
                        }

                        if (!$sth->rowCount()) {
                            throw new ModelException("Player not found", 404);
                        }

                        $stat = $sth->fetch();
                        $stat['All'] /= $game->getOption('p');

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

                            $sql = "SELECT sum(g.Win) W, count(g.Id) T, p.Nicname N,  p.Avatar A, p.Id I, (sum(g.Win)*25+count(g.Id)) R
                                FROM `PlayerGames` g
                                JOIN Players p On p.Id=g.PlayerId
                                where g.GameId = :gameid AND g.`Date`>:dt AND g.Price>0
                                group by g.PlayerId
                                having T > (SELECT (count(Id) / count(distinct(PlayerId)) / " . $game->getOption('p'). " ) FROM PlayerGames WHERE GameId = :gameid)
                                order by R DESC, T DESC
                                LIMIT 10";

                            #echo $this->time() . " SELECT TOP\n";

                            try {
                                $sth = DB::Connect()->prepare($sql);
                                $sth->execute(
                                    array(
                                        ':gameid' => $game->getId(),
                                        ':dt'   => $date,
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

                        #echo $this->time() . " " . "Топ + обновление данных игрока\n";
                        $from->send(json_encode(array(
                            'path' => 'update',
                            'res' => array(
                                'all' => $stat['All'],
                                'count' => $stat['Count'],
                                'win' => $stat['Win']*25,
                                // кол-во ожидающих во всех стеках игры - количество стеков из-за рекурсии + кол-во игр * кол-во игроков
                                'online' =>
                                    ((isset($this->_stack[$name]) ? count($this->_stack[$name], COUNT_RECURSIVE) - count($this->_stack[$name]) : 0) +
                                        (isset($this->_apps[$name]) ? count($this->_apps[$name]) * $game->getOption('p') : 0))+($game->getOption('b')?rand(9,11):0),
                                'top' => $top
                            ))));

                        break;

                    default:
                        if(isset($data->message)){

                            if ($data->message == 'stop') {
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
                            } elseif ($data->message == 'stats') {
                                $count=0;
                                foreach ($this->_apps as $apps_class)
                                    $count+=count($apps_class);

                                $from->send(json_encode(
                                    array(
                                        'path' => 'appchat',
                                        'res' => array(
                                            'user' => 'system',
                                            'message' => array ('games'=>$count, 'players'=>count($this->_clients))
                                        )
                                    )
                                ));


                            } elseif ($data->message == 'games') {
                                $games='';
                                $count=0;
                                foreach ($this->_apps as $app_title=>$apps_class) {
                                    $games .= $app_title.' ('.count($apps_class).'):<br>';
                                    foreach ($apps_class as $app) {
                                        $count++;
                                        $games .= $app->getIdentifier() . ' [' . $app->getCurrency() . '-' . $app->getPrice() . '] ' . (time() - $app->getTime()) . 's ';
                                        $names = array();
                                        $players = $app->getPlayers();
                                        foreach ($players as $name)
                                            $names[] = $name['pid'];
                                        $games .= (!empty($names) ? implode(':', $names) . '<br>' : '');
                                    }
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
                            echo $this->time().' default '.(json_encode($msg));
                        }
                        break;
                }
                /* */
            } else
                echo $this->time(0,'ERROR')." onMessage: #{$from->resourceId} " . $from->Session->getId() . " без Entity Player \n";
    }

    public function onClose(ConnectionInterface $conn) {

        if($conn->Session->get(Player::IDENTITY)){
            /*
            if(isset($this->_players[$conn->resourceId])){
                echo $this->time()." ". "Выход игрока при разъединении {$conn->resourceId}\n";
                $this->quitPlayer($conn->resourceId);
            }
            */

            /*        foreach ($this->_clients as $client) {
                        $client->send(json_encode(
                            array(
                                'path'=>'appchat',
                                'res'=>array(
                                    'message'=>$conn->Session->get(Player::IDENTITY)->getNicName().' отсоединился')
                            )));
                    }
            */
        } else
            echo $this->time(0,'ERROR')." "."onClose: #{$conn->resourceId} " . $conn->Session->getId() . " без Entity Player \n";

        if(isset($this->_clients[$conn->resourceId])){
            if(isset($this->_players[$conn->resourceId]['appName']) && isset($this->_players[$conn->resourceId]['appMode']) && isset($this->_stack[$this->_players[$conn->resourceId]['appName']][$this->_players[$conn->resourceId]['appMode']][$conn->resourceId])){
                echo $this->time() . " {$this->_players[$conn->resourceId]['appName']}" . "Игрок {$conn->resourceId} удален из стека {$this->_players[$conn->resourceId]['appMode']} при выходе\n";
                unset($this->_stack[$this->_players[$conn->resourceId]['appName']][$this->_players[$conn->resourceId]['appMode']][$conn->resourceId]);
            }

            unset($this->_clients[$conn->resourceId]);
            echo $this->time(0,'OUT')." ". "#{$conn->resourceId} {$conn->Session->getId()}\n";
        } else
            echo $this->time(0,'ERROR')." "."onClose: client #{$conn->resourceId} " . $conn->Session->getId() . " не найден в коллекции \n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {

        if($e->getCode() == 'HY000' || stristr($e->getMessage(), 'server has gone away')) {
            echo $this->time()." ". "{$e->getMessage()} RECONNECT \n";
            DB::Reconnect('default', Config::instance()->dbConnectionProperties);
        } else {
            echo $this->time()." ". "An error has occurred: {$e->getMessage()}\n";
            $conn->close();
        }
    }



    public function sendCallback($response, $callback,$class=null) {

        if(!$class)
            $class=$this->_class;

        if(!isset($response) OR !is_array($response) OR !count($response))
            echo $this->time(0,'WARNING')."  response пустой\n";

        // рассылаем игрокам результат обработки
        foreach($response as $client ) {
            if(!isset($client->bot)) {
                if(is_numeric($client))
                    $client=(object)['id'=>$client];
                if (isset($this->_clients[$client->id]) && ($this->_clients[$client->id] instanceof ConnectionInterface)){
                    #echo $this->time(1) . "  отправляем данные #{$client->id} \n";
                    #print_r((isset($callback[$client->id]) ? $callback[$client->id] : $callback));

                    $this->_clients[$client->id]->send(
                        json_encode(
                            array(
                                'path' => 'app' . $class,
                                'res' => (isset($callback[$client->id]) ? $callback[$client->id] : $callback)
                            )));
                } else
                    echo $this->time(0,'WARNING') . "  соединение #{$client->id} не найдено \n";
            }
        }
    }

    private function checkBalance($pid, $currency, $price)
    {

        if (isset($this->_players[$pid])) {

            $sql = "SELECT Points, Money FROM `Players` WHERE `Id`=:id LIMIT 1";

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

            echo $this->time(0,'SQL') . " #$pid points:" . implode(' money:',$balance) . " price:{$currency}-{$price}\n";

            if ($currency == 'MONEY'
                ? $balance['Money'] < $price * \CountriesModel::instance()->getCountry($this->_players[$pid]['Country'])->loadCurrency()->getCoefficient()
                : $balance['Points'] < $price
            ) {
                return false;
            } else
                return true;
        }   else {
            echo $this->time(0,'WARNING') . " " . "в коллекции нет игрока #{$pid} \n";
            return false;
        }

    }

    public function quitPlayer($playerId) {

        if (isset($this->_players[$playerId]['appName'])){
            $class = $this->_players[$playerId]['appName'];
            $mode = $this->_players[$playerId]['appMode'];
            if(isset($this->_players[$playerId]['appId'])){
                echo $this->time(1)." ". $this->_players[$playerId]['appName'].' '. $this->_players[$playerId]['appId']. " удаление appId у игрока №$playerId\n";
                $id = $this->_players[$playerId]['appId'];
            }
        }

        // сдаемся и выходим, сохраняем и удаляем игру
        if (isset($class))
        {
            if(isset($this->_stack[$class][$mode][$playerId])){
                unset($this->_stack[$class][$mode][$playerId]);
                echo $this->time(1)." ". "$class Удаление игрока из игрового стека ожидающих \n";
            }

            //echo $this->time()." ". "Удаление игрока №$playerId из массива игроков \n";
            //unset($this->_players[$playerId]);

            if(isset($this->_stack[$class][$mode]) AND count($this->_stack[$class][$mode])==0){
                echo $this->time(1)." ". "$class Удаление стека ожидающих игроков {$class} {$mode}\n";
                unset($this->_stack[$class][$mode]);
            }

            if (isset($id) AND isset($this->_apps[$class][$id])) {

                $app = $this->_apps[$class][$id];
                // если есть игра - сдаемся
                $app->setClient($playerId);

                if (!$app->isOver()) {
                    echo $this->time(1)." ". "$class $id Игра активная - сдаемся\n";
                    $app->passAction();
                    $this->sendCallback($app->getResponse(), $app->getCallback());
                }

                // сигнализируем об уходе и отключаемся от игры
                echo $this->time(1)." ". "$class $id Сигнализируем об уходе\n";
                $app->quitAction();
                $this->sendCallback($app->getResponse(), $app->getCallback());

                // если приложение завершилось и не сохранено, сохраняем
                if ($app->isOver() && !$app->isSaved()) {
                    echo $this->time(1)." ". "$class $id Сохраняем результаты"."\n";
                    $this->saveGame($app);
                }

                // если приложение завершилось и сохранено, выгружаем из памяти
                if ($app->isOver() && $app->isSaved()) {
                    echo $this->time(1) . " $class $id удаляем приложение \n";
                    unset($this->_apps[$class][$id]);
                }
            }
        }

    }

    function saveGame($app){

        echo $this->time(1)." ".$app->getKey().' '.$app->getIdentifier(). " Состояние игры: ".$app->_isSaved."/".$app->_isOver." - Сохраняем игру: \n";
        $app->_isSaved = 1;
        print_r($app->getPlayers());

        $sql_results = "INSERT INTO `PlayerGames`
        (`PlayerId`, `GameId`, `GameUid`, `Date`, `Win`, `Lose`, `Draw`, `Result`, `Currency`, `Price`)
        VALUES (?,?,?,?,?,?,?,?,?,?)".
            str_repeat(',(?,?,?,?,?,?,?,?,?,?)', count($app->getPlayers())-1);

        if($app->getPrice())
            $sql_transactions = "INSERT INTO `Transactions` (`PlayerId`, `Currency`, `Sum`, `Balance`, `Description`, `Date`) VALUES ";

        $results = $transactions = array();
        $players = $app->getPlayers();

        foreach($players as $player)
        {
            if($app->getPrice() AND $player['result']!=0 AND !isset($app->getClients()[$player['pid']]->bot)) {
                $sql_transactions_players[]='(?,?,?,?,?,?)';

                $currency=$app->getCurrency()=='MONEY'?'Money':'Points';
                $price=($currency=='Money'?
                    $app->getPrice()*\CountriesModel::instance()->getCountry($this->_players[$pid]['Country'])->loadCurrency()->getCoefficient()
                    :$app->getPrice());

                /* update balance after game */
                $sql="UPDATE Players SET ".$currency." = ".$currency.($player['result'] < 0 ? '' : '+').
                    ($player['result']*$price)." WHERE Id=".$player['pid'];

                #echo $this->time()." ".$sql."\n";

                try{
                    DB::Connect()->query($sql);
                }
                catch(\Exception $e)
                {
                    #echo $this->time()." ". $e->getMessage();
                }


                $sql = "SELECT Points, Money FROM `Players` WHERE `Id`=:id LIMIT 1";
                #echo $this->time() . " " . $sql . "\n";
                try {
                    $sth = DB::Connect()->prepare($sql);
                    $sth->execute(array(':id' => $player['pid']));
                } catch (PDOException $e) {
                    echo $this->time(0,'ERROR')." Error processing storage query в таблице Players при получении баланса\n";
                }

                if (!$sth->rowCount()) {
                    echo $this->time(0,'ERROR')." player #{$player['pid']} не найден в таблице Players при получении баланса\n";
                }

                $balance = $sth->fetch();

                /* send new balance to player */
                if(isset($this->_clients[$player['pid']])) {
                    $this->_clients[$player['pid']]->send(json_encode(
                            array('path'=>'update',
                                'res'=>array(
                                    'money'=>$balance['Money'],
                                    'points'=>$balance['Points']
                                )))
                    );
                } else
                    echo $this->time(0,'ERROR')." client #{$player['pid']} не найден в коллекции при отправке баланса\n";

                /* prepare transactions */
                array_push($transactions,
                    $player['pid'],
                    $app->getCurrency(),
                    $price*$player['result'],
                    (isset($balance)?$balance[$currency]:null),
                    $app->getTitle($player['lang']),
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

        // echo $this->time(); print_r($results); echo "\n";

        try {
            DB::Connect()->prepare($sql_results)->execute($results);
            echo $this->time(0,'SQL')." Записали результаты игры в базу\n";
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query" . $e->getMessage(), 500);
        }

        if($app->getPrice()) {
            $sql=$sql_transactions.implode(",",$sql_transactions_players);
            // echo $this->time()." ".$sql."\n";
            try {
                DB::Connect()->prepare($sql)->execute($transactions);
                echo $this->time(0,'SQL')." Записали транзакции в базу\n";
            } catch (PDOException $e) {
                throw new ModelException("Error processing storage query" . $e->getMessage(), 500);
            }
        }
    }

    private function time($spaces=0,$str=null){
        return str_repeat (' ',$spaces).date( 'H:i:s', time() ).($str?' ['.str_pad($str,7,'.').'] ':'');
    }
}