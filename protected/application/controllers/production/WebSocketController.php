<?php namespace controllers\production;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Ratchet\Wamp\Exception;
use \Player, \Cache, \DB, \Config, \SettingsModel, \Application, \OnlineGamesModel, \CountriesModel;


Application::import(PATH_APPLICATION . '/model/Game.php');
Application::import(PATH_APPLICATION . '/model/entities/Player.php');
// Application::import(PATH_APPLICATION . '/model/entities/LotterySettings.php');
Application::import(PATH_GAMES . '*');

class WebSocketController implements MessageComponentInterface {

    const   MIN_WAIT_TIME = 2;//15;
    const   MAX_WAIT_TIME = 600;//600;
    const   PERIODIC_TIMER = 2;
    const   CONNECTION_TIMER = 1800;
    const   COMISSION = 10; //percent
    const   DEFAULT_MODE = 'POINT-0-2';
    const   EMULATION = false;

    // private $_settings = array();
    // private $_bots     = array();
    private $_rating    = array();
    private $_class     = null;
    private $_loop      = null;

    private $_clients   = array();
    private $_stack     = array();
    private $_apps      = array();
    private $_players   = array();

    private $memcache;

    public function __construct($loop) {

        echo $this->time(0,'START')." ". "Server have started\n";

        $this->_loop = $loop;
        $this->_loop->addPeriodicTimer(self::PERIODIC_TIMER, function () { $this->periodicTimer();});
        $this->_loop->addPeriodicTimer(self::CONNECTION_TIMER, function () { $this->checkConnections();});
        $this->memcache = new \Memcache;
        $this->memcache->connect('localhost', 11211);
        // $this->_bots = SettingsModel::instance()->getSettings('gameBots')->getValue();
        // $this->_settings = LotterySettingsModel::instance()->loadSettings();
    }

    public function checkConnections()
    {
        foreach($this->players() as $player)
            if($player['Ping']<time()-self::CONNECTION_TIMER){
                echo $this->time()." ". "#{$player['Id']} ping timeout\n";
                $this->quitPlayer($player['Id']);

                $this->players('unset', $player['Id']);
                //unset($this->_players[$player['Id']]);

                if(($client = $this->clients($player['Id'])) && $client instanceof ConnectionInterface){
                    echo $this->time(0,'CLOSE')." #{$player['Id']} {$client->Session->getId()} \n";
                    $client->close();
                }
                else
                    echo $this->time(0,'ERROR')." client #{$player['Id']} не найден в коллекции\n";
            }
    }

    public function periodicTimer()
    {
        foreach($this->stack() as $key=>$modes)
            foreach($modes as $mode=>$stacks)
                foreach($stacks as $id=>$client){
                    $game=OnlineGamesModel::instance()->getGame($key);
                    if($client->time + self::MIN_WAIT_TIME < time() && $game->getOption('b')){
                        $clients=array();
                        $clients[$id] = $client;
                        while(count($clients)<$game->getOption('p')){
                            do {
                                $bot = (object)SettingsModel::instance()->getSettings('gameBots')->getValue()[array_rand(SettingsModel::instance()->getSettings('gameBots')->getValue())];
                            } while (array_key_exists($bot->id,$clients));
                            $clients[$bot->id] = $bot;
                        }
                        $this->initGame($clients,$key,$mode,$id);
                    } elseif($client->time + self::MAX_WAIT_TIME < time()){
                        echo $this->time(0) . " $key " . "Игрок {$id} удален из стека {$this->players($id)['appMode']} по таймауту\n";
                        $this->stack('unset',$this->players($id)['appName'],$this->players($id)['appMode'],$id);
                    }
                }

        foreach($this->apps() as $class=>$apps)
            foreach($apps as $id=>$app) {
                $current = $app->currentPlayer();
                if ($app->_isOver && !empty($app->_bot)) {
                    foreach($app->_bot as $bot) {
                        if ($current['timeout'] - $app->getOption('t') + 10 < time()) {
                            #echo " -- таймер на выход после 10 сек \n";
                            $this->runGame($class, $app->getIdentifier(), 'quitAction', $bot);
                        } elseif (!in_array($bot,$app->_botReplay) && $current['timeout'] + rand(2, 4) - $app->getOption('t') < time()) {
                            if (rand(1, 5) == 1) {
                                #echo " -- таймер на случайный выход\n";
                                $this->runGame($class, $app->getIdentifier(), 'quitAction', $bot);
                            } else {
                                #echo " -- таймер на повтор \n";
                                $this->runGame($class, $app->getIdentifier(), 'replayAction', $bot);
                            }
                        }
                    }
                } elseif (!$app->_isOver && $app->getTime()+$app->getOption('t') < time() && $current['timeout'] < time() && $current['pid']) {
                    #echo " -- таймер на таймаут \n";
                    $this->runGame($class, $app->getIdentifier(), 'timeoutAction', $current['pid']);
                } elseif ($app->_isOver && $current['timeout'] + 60 < time()) {
                    #echo " -- таймер на выход \n";
                    $this->runGame($class, $app->getIdentifier(), 'quitAction', $current['pid']);
                }
            }
    }

    public function initGame($clients,$name,$mode,$id)
    {
        $this->_class = $class='\\' . $name;
        $app = new $class(OnlineGamesModel::instance()->getGame($name));
        $keys = array_keys($clients);
        list($currency, $price, $number) = array_pad(explode("-", $mode),3,2);
        echo $this->time()." $name инициируем приложение $currency-$price-$number: №".implode(', №',$keys)."\n";

        #echo $this->time()." чистим стек\n";
        foreach ($keys as $key) {
            if($_player=$this->players($key)) {
                $this->stack('unset',$name,$mode,$key);
                if(isset($_player['appId']))
                    $this->quitPlayer($key);
                $_player['appName'] = $name;
                $_player['appId'] = $app->getIdentifier();
                $this->players($key,$_player);
            }
        }

        if ($this->stack($name,$mode) AND count($this->stack($name,$mode)) == 0){
            $this->stack('unset',$name,$mode);
            #echo $this->time()." никого не осталось, удаляем стек\n";
        }

        #echo $this->time()." запускаем и кешируем приложение\n";
        $app->setClients($clients)
            ->setClient($id)
            ->setNumberPlayers($number)
            ->setCurrency($currency)
            ->setPrice((float)$price);
        $this->apps($name,$app->getIdentifier(),$app);
        $this->runGame($name,$app->getIdentifier(),'startAction',$id);
    }


    public function runGame($name,$id,$action,$pid=null,$data=null)
    {
        if($app=$this->apps($name,$id)) {
            $this->_class = $class='\\' . $name;
            echo "###################################################\n".$this->time() . " " . "$name {$app->getIdentifier()} $action ".(empty($app->_bot) || !in_array($pid,$app->_bot) ? "игрок №" : 'бот №').$pid.($action != 'startAction'?' (текущий №'.implode(',',$app->currentPlayers()).")":'')." \n";

            if (!in_array($pid,$app->_bot) && (($action == 'replayAction' || $action == 'startAction') && (!$this->checkBalance($pid, $app->getCurrency(), $app->getPrice())))) {
                #echo $this->time() . " " . "Игрок {$from->resourceId} - недостаточно средств для игры\n";
                if($client = $this->clients($pid))
                    $client->send(json_encode(array('error' => 'INSUFFICIENT_FUNDS')));
                #echo $this->time() . " " . "прошли проверку, устанавливаем клиента \n";
            } else {

                if(isset($pid)){
                    $app->setClient($pid);
                }

                #echo $this->time() . " " . "пробуем вызвать экшн \n";
                if(!$app->_isOver OR $action != 'moveAction' ) {
                    call_user_func(array($app, $action), $data);
                }

                #echo $this->time() . " " . "рассылаем игрокам результат обработки \n";
                $this->sendCallback($app->getResponse(), $app->getCallback());

                if(!$app->_isOver){
                    foreach($app->_botTimer as $bot=>$timer) {
                        unset($app->_botTimer[$bot]);
                        if (in_array($bot, $app->currentPlayers())) {
                            $this->_loop->addTimer($timer, function () use ($name, $id, $bot) {
                                $this->runGame($name, $id, 'moveAction', $bot);
                                //echo $this->time() . " " . "$name {$this->_apps[$name][$id]->getIdentifier()} moveAction Бот \n";
                            });
                        }
                    }
                }

                if($app->isOver()) {

                    if (!$app->isSaved()) {
                        echo $this->time(1) . " $name $id приложение завершилось, записываем данные\n";
                        $this->saveGame($app);
                    } elseif ((count($app->getClients()) < $app->getOption('p') && !$app->getOption('f')) || !count($app->getClients())) {
                        echo $this->time(1) . " $name $id удаляем приложение \n";
                        $this->apps('unset', $name, $id);
                    }

                } else {
                    echo $this->time(1) . " $name $id сохраняем приложение \n";
                    $this->apps($name,$id, $app);
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
            $_player = array_merge(
                array('Id'=>$player->getId(),'Ping'=>time(),'Country'=>$player->getCountry()),
                $this->players($player->getId()) ? : array()
            );
            $conn->resourceId = $player->getId();
            $this->clients($player->getId(), $conn);
            $this->players($player->getId(), $_player);
            echo $this->time(0,'OPEN')." "."#{$conn->resourceId} " . $conn->Session->getId() . "\n";

            /*
            if($this->players($player->getId()))){
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
                            //'appName' => isset($_player['appName']) ? $_player['appName'] : '',
                        )))
            );

            if(isset($_player['appId'])){
                if(isset($_player['appName']))
                    $this->runGame($_player['appName'],$_player['appId'],'startAction',$_player['Id']);
                else {
                    unset($_player['appId']);
                    echo $this->time(0, 'WARNING') . " у игрока {$player->getId()} отсутствует appName\n";
                    $this->players($player->getId(),$_player);
                }
            }

            // EMULATION GAME
            if(self::EMULATION){
                $mode='POINT-0-2';
                $name='Durak';

                $this->apps('unset',$name);

                $clients=array();
                $clients[$player->getId()] = (object) array(
                    'time'      =>  time(),
                    'id'        =>  $player->getId(),
                    'avatar'    =>  $player->getAvatar(),
                    'name'      =>  $player->getNicName());
                $bot = (object)SettingsModel::instance()->getSettings('gameBots')->getValue()[array_rand(SettingsModel::instance()->getSettings('gameBots')->getValue())];
                $clients[$bot->id] = $bot;
                $this->initGame($clients,$name,$mode,$player->getId());

            }

            // $this->_class='chat';
            // $this->sendCallback($this->clients(), array('message'=>$conn->Session->get(Player::IDENTITY)->getNicName().' присоединился'));
        } else
            echo $this->time(0,'ERROR')." onOpen: #{$conn->resourceId} " . $conn->Session->getId() . " без Entity Player \n";

    }

    public function onMessage(ConnectionInterface $from, $msg) {

        if($player = $from->Session->get(Player::IDENTITY))
            if($player instanceof Player) {

                $_player=$this->players($player->getId());
                $_player['Ping']= time();
                $this->players($player->getId(),$_player);
                //$this->_players[$player->getId()]['Ping'] = time();

                $data = json_decode($msg);
                list($type, $name, $id) = array_pad(explode("/", $data->path), 3, 0);
                echo $this->time(0, 'MESSAGE') . " #{$from->resourceId}: " . $data->path . (isset($data->data->action) ? " - " . $data->data->action : '') . " \n";
                $this->_class = $class = '\\' . $name;
                if (isset($data->data))
                    $data = $data->data;
                $action = (isset($data->action) ? $data->action : '') . 'Action';

                if(!$game = OnlineGamesModel::instance()->getGame($name)){
                    $from->send(json_encode(array('error' => 'WRONG_APPLICATION_TYPE')));
                    return;
                }

                $mode = ((isset($data->mode) AND $game->isMode($data->mode) ) ? $data->mode : self::DEFAULT_MODE);

                if (!($client = $this->clients($player->getId())) || !($client instanceof ConnectionInterface)) {
                    echo $this->time(0, 'WARNING') . "  соединение #{$player->getId()} {$from->Session->getId()} не найдено в коллекции клиентов \n";
                    $this->clients($player->getId(),$from);
                }

                switch ($type) {
                    case 'app':
                        try {

                            if (class_exists($class)) {

                                $_player = $this->players($from->resourceId);
                                // нет запущенного приложения, пробуем создать новое или просто записаться в очередь
                                if (!$id) {
                                    #echo $this->time() . " " . "id приложения нет \n";

                                    if (isset($_player['appName']) && isset($_player['appId']) && $this->apps($_player['appName'],$_player['appId']) ) {

                                        echo $this->time(0, 'DANGER') . " {$_player['appName']}" . " Игрок {$from->resourceId} отправил запрос без ID при активной игре {$_player['appId']}\n";
                                        $this->runGame($_player['appName'], $_player['appId'], 'startAction', $_player['Id']);

                                    } elseif (in_array($action, array('cancelAction','quitAction','backAction'))) {

                                        if (isset($_player['appMode'])) {

                                            echo $this->time(1) . " {$_player['appName']}" . " Игрок {$from->resourceId} отказался ждать в стеке {$_player['appMode']}\n";
                                            $this->stack('unset',$name,$_player['appMode'],$player->getId());

                                            unset(
                                                $_player['appName'],
                                                $_player['appMode']);
                                            $this->players($from->resourceId,$_player);
                                        }

                                        $from->send(json_encode(array('path' => $data->action)));

                                    } elseif ($action == 'startAction') {

                                        list($currency, $price, $number) = explode("-", $mode);

                                        if($this->checkBalance($player->getId(), $currency, $price)){

                                            if( isset($_player['appName']) && isset($_player['appId'])
                                                && ($app=$this->apps($_player['appName'],$_player['appId'])) && !$app->_isOver){
                                                echo $this->time(0,'ERROR') . " " . "{$_player['appName']} Запуск игроком {$from->resourceId} новой игры при незавершенной {$_player['appId']}\n";
                                                $this->runGame($_player['appName'], $_player['appId'], 'startAction', $_player['Id']);
                                                return false;
                                            }

                                            if( isset($_player['appName']) && isset($_player['appMode'])
                                                && $this->stack($_player['appName'],$_player['appMode'],$player->getId())){
                                                $this->stack('unset',$_player['appName'],$_player['appMode'],$player->getId());
                                                echo $this->time() . " " . "{$_player['appName']} Игрок {$from->resourceId} выписался из стека {$_player['appMode']}\n";
                                            }

                                            echo $this->time() . " " . "$name Игрок {$from->resourceId} записался в стек $mode\n";

                                            $this->stack($name,$mode,$player->getId(),
                                                (object) array(
                                                    'time'      =>  time(),
                                                    'id'        =>  $player->getId(),
                                                    'avatar'    =>  $player->getAvatar(),
                                                    'lang'      =>  $player->getLang(),
                                                    'name'      =>  $player->getNicName()));

                                            $_player['appName'] = $name;
                                            $_player['appMode'] = $mode;
                                            $this->players($from->resourceId,$_player);

                                            $success=false;

                                            $stack = $this->stack($name,$mode);
                                            // если насобирали минимальную очередь
                                            if ((count($stack) >= $game->getOption('s')
                                                AND count($stack) >= $number) || $game->getOption('f')) {

                                                // перемешали игроков
                                                $keys = array_keys($stack);
                                                shuffle($keys);

                                                // начали проверять стек на игру, так как могут быть те, кто не желает играть друг с другом
                                                foreach ($keys as $key) {
                                                    $clients[$key] = $stack[$key];
                                                    // дошли до необходимого числа и прервали
                                                    if (count($clients) == $number || $game->getOption('f')) {
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
                                                            'stack' => count($stack),
                                                            'mode' => $mode)
                                                    )));

                                            }
                                        }
                                    }

                                    // пробуем загрузить приложение, проверяем наличие, если есть, загружаем и удаляем игрока из стека
                                }  elseif (!($app=$this->apps($name,$id))) {

                                    if ($action == 'replayAction' || $action == 'quitAction') {
                                        echo $this->time() . " " . "id есть, но приложения $name $id нет, {$from->resourceId} $action заглушка\n";

                                    } else {
                                        echo $this->time() . " " . "id есть, но приложения $name $id нет, сообщаем об ошибке, удаляем из активных игроков \n";
                                        $this->sendCallback(array($from->resourceId), array(
                                            'action' => 'error',
                                            'error' => 'APPLICATION_DOESNT_EXISTS',
                                            'appId' => 0));
                                        $this->quitPlayer($player->getId());
                                    }

                                    // если нет, сообщаем об ошибке
                                } else {

                                    if(!in_array($player->getId(),array_keys($app->getClients()))) {

                                        $_player = $this->players($player->getId());
                                        if (isset($_player['appName']) && isset($_player['appId']) && $this->apps($_player['appName'], $_player['appId'])) {

                                            echo $this->time(0, 'DANGER') . " {$_player['appName']}" . " Игрок {$player->getId()} отправил запрос на новую при активной игре {$_player['appId']}\n";
                                            $this->runGame($_player['appName'], $_player['appId'], 'startAction', $_player['Id']);
                                            return;

                                        } else if(!$app->getOption('v') && count($app->getClients())==$app->getNumberPlayers()){
                                                $client->send(json_encode(array('error' => 'GAME_IS_FULL')));
                                            return;

                                        } else if(isset($_player['appMode'])) {
                                            echo $this->time(1) . " {$_player['appName']}" . " Игрок {$player->getId()} запускает другую игру, пребывая в стеке {$_player['appMode']}\n";
                                            $this->stack('unset', $_player['appName'], $_player['appMode'], $player->getId());
                                        }
                                        $_player['appName'] = $name;
                                        $_player['appMode'] = $app->getMode();
                                        $_player['appId'] = $id;

                                        $this->players($player->getId(),$_player);

                                        $app->addClient(array($player->getId()=>(object)array(
                                            'time'      =>  time(),
                                            'id'        =>  $player->getId(),
                                            'avatar'    =>  $player->getAvatar(),
                                            'lang'      =>  $player->getLang(),
                                            'name'      =>  $player->getNicName())));
                                    }

                                    #echo $this->time() . " " . "приложение нашли $name  $id\n";
                                    $this->runGame($name,$id,$action,$player->getId(),$data);

                                    if ($action == 'quitAction')
                                        $from->send(json_encode(array('path' => $data->action)));
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

                        $date = mktime(0, 0, 0, date("n"), 1);

                        $sql = "SELECT COUNT(*) FROM ( SELECT 1 FROM `PlayerGames` WHERE `GameId` =:gameid GROUP BY `GameUid` , `Date` ) `All`";
                        $sql = "SELECT SUM(Price) Total, Currency FROM (SELECT Price,Currency FROM `PlayerGames` WHERE `GameId` =:gameid AND Price>0 AND Date>:dt GROUP BY `GameUid` , `Date`) a  GROUP BY Currency";

                        try {
                            $sth = DB::Connect()->prepare($sql);
                            $sth->execute(
                                array(
                                    ':gameid' => $game->getId(),
                                    ':dt' => $date
                                ));
                        } catch (PDOException $e) {
                            throw new ModelException("Error processing storage query", 500);
                        }

                        $fund = array();
                        $comission = (self::COMISSION) / 100;
                        foreach ($sth->fetchAll() as $data) {
                            $fund[$data['Currency']] = $data['Currency'] == 'MONEY' ? ceil($data['Total'] * $comission * 100) / 100 : ceil($data['Total'] * $comission);
                        }


                        $sql = "SELECT count(`PlayerGames`.`Id`) Count, sum(`PlayerGames`.`Win`) `Win`
                                        FROM `Players`
                                        LEFT JOIN `PlayerGames`
                                        ON `PlayerGames`.`PlayerId` = `Players`.`Id`
                                        WHERE `Players`.`Id`=:id AND `PlayerGames`.`GameId` = :gameid AND `PlayerGames`.`Date`>:dt AND `PlayerGames`.`Price`>0
                                        LIMIT 1";

                        #echo $this->time() . " SELECT PLAYER INFO" . "\n";

                        try {

                            $sth = DB::Connect()->prepare($sql);
                            $sth->execute(
                                array(
                                    ':id' => $from->resourceId,
                                    ':dt' => $date,
                                    ':gameid' => $game->getId()
                                ));

                        } catch (PDOException $e) {
                            throw new ModelException("Error processing storage query", 500);
                        }

                        if (!$sth->rowCount()) {
                            throw new ModelException("Player not found", 404);
                        }

                        $stat = $sth->fetch();



                    #echo $this->time() . " " . "Список текущих игр \n";

                    $modes = $game->getModes();
                    $res = array(
                        'key'       => $game->getKey(),
                        'audio' => array_filter($game->getAudio()),
                        'modes' => (is_array($modes) && array_walk($modes, function (&$value, $index) {
                            $value = array_keys($value);
                        }) ? $modes : null),
                        'fund'       => $fund,
                        'count'      => $stat['Count'],
                        'win'        => $stat['Win']*25,
                        'maxPlayers' => $game->getOption('p'),
                        'create'     => $game->getOption('f'),
                    );

                    $from->send(json_encode(array(
                        'path' => $type, // 'update'
                        'res' => $res)));

                        break;

                    case 'now':

                        $games=array();

                        foreach($this->apps($name) as $id => $game) {
                            $players = array();
                            foreach ($game->getPlayers() as $player)
                                $players[] = $player['name'];

                            $games[] = array(
                                'id' => $id,
                                'mode' => $game->getCurrency() . '-' . $game->getPrice() . '-' . $game->getNumberPlayers(),
                                'players' => $players
                            );
                        }

                        foreach($this->stack($name) as $mode=>$clients)
                            foreach($clients as $id=>$client){
                                $games[]=array(
                                    'id'=>0,
                                    'mode'=>$mode,
                                    'players'=>array($client->name)
                                );
                            }

                        $res = array(
                            'key'       => $game->getKey(),
                            'now'       => $games,
                        );

                        $from->send(json_encode(array(
                            'path' => $type, // 'now'
                            'res' => $res)));

                        break;

                    case 'rating':

                        $date = mktime(0, 0, 0, date("n"), 1);

                        $_rating = $this->rating($name);

                        if ($_rating AND $_rating['timeout'] > time()) {
                            $top  = $_rating['top'];
                        } else {

                            $sql = "(SELECT g.Currency Currency, sum(g.Win) W, count(g.Id) T, p.Nicname N,  p.Avatar A, p.Id I, (sum(g.Win)*25+count(g.Id)) R
                                FROM `PlayerGames` g
                                JOIN Players p On p.Id=g.PlayerId
                                where g.GameId = :gameid AND g.`Date`>:dt AND g.Price>0 AND g.Currency='MONEY'
                                group by g.PlayerId
                                having T >
                                (SELECT COUNT(*) FROM ( SELECT 1 FROM `PlayerGames` WHERE `GameId` = :gameid AND `Date`>:dt AND Price>0 AND Currency='MONEY' GROUP BY `GameUid` , `Date` ) `All`) / count(*)
                                order by R DESC, T DESC
                                LIMIT 10)
                                UNION
                                (SELECT g.Currency Currency, sum(g.Win) W, count(g.Id) T, p.Nicname N,  p.Avatar A, p.Id I, (sum(g.Win)*25+count(g.Id)) R
                                FROM `PlayerGames` g
                                JOIN Players p On p.Id=g.PlayerId
                                where g.GameId = :gameid AND g.`Date`>:dt AND g.Price>0 AND g.Currency='POINT'
                                group by g.PlayerId
                                having T >
                                (SELECT COUNT(*) FROM ( SELECT 1 FROM `PlayerGames` WHERE `GameId` = :gameid AND `Date`>:dt AND Price>0 AND Currency='POINT' GROUP BY `GameUid`, `Date` ) `All`) / count(*)
                                order by R DESC, T DESC
                                LIMIT 10)";

                            #echo $this->time() . " SELECT TOP\n";

                            try {
                                $sth = DB::Connect()->prepare($sql);
                                $sth->execute(
                                    array(
                                        ':gameid'   => $game->getId(),
                                        ':dt'       => $date
                                    ));
                            } catch (PDOException $e) {
                                throw new ModelException("Error processing storage query", 500);
                            }

                            $top = array();
                            foreach ($sth->fetchAll() as $player) {
                                $player['O'] = ((isset($this->players($player['I'])['appName']) && $this->players($player['I'])['appName'] == $name ? 1 : 0));
                                $cur=$player['Currency'];unset($player['Currency']);
                                $top[$cur][] = $player;
                            }

                            $this->rating($name,array(
                                'top'     => $top,
                                'timeout' => time() + 1 * 60
                            ));

                        }

                        #echo $this->time() . " " . "Топ + обновление данных игрока\n";

                        $from->send(json_encode(array(
                            'path' => $type, // 'update'
                            'res' => array(
                                'top'      => $top,
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
                                            'message' => 'Игроков онлайн - ' . count($this->clients()))
                                    )
                                ));

                            } elseif ($data->message == 'players') {

                                foreach ($this->clients() as $client)
                                    $names[] = $client->Session->get(Player::IDENTITY)->getNicName();
                                $from->send(json_encode(
                                    array(
                                        'path' => 'appchat',
                                        'res' => array(
                                            'user' => 'system',
                                            'message' => 'Игроки онлайн - '. count($this->clients()).': ' . implode(', ', $names))
                                    )
                                ));
                            } elseif ($data->message == 'stats') {
                                $count=0;
                                foreach ($this->apps() as $apps_class)
                                    $count+=count($apps_class);

                                $from->send(json_encode(
                                    array(
                                        'path' => 'appchat',
                                        'res' => array(
                                            'user' => 'system',
                                            'message' => array ('games'=>$count, 'players'=>count($this->clients()))
                                        )
                                    )
                                ));


                            } elseif ($data->message == 'games') {
                                $games='';
                                $count=0;
                                foreach ($this->apps() as $app_title=>$apps_class) {
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

                                foreach ($this->stack() as $class=>$stack_class)
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

                                foreach ($this->clients() as $client) {
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

            /*        foreach ($this->clients() as $client) {
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

        if($this->clients($conn->resourceId)){
            $_player = $this->players($conn->resourceId);
            if(isset($_player['appName']) && isset($_player['appMode']) && $this->stack($_player['appName'],$_player['appMode'],$conn->resourceId)){
                echo $this->time() . " {$_player['appName']}" . "Игрок {$conn->resourceId} удален из стека {$_player['appMode']} при выходе\n";
                $this->stack('unset',$_player['appName'],$_player['appMode'],$conn->resourceId);
                unset($_player['appName'],$_player['appMode']);
                $this->players($conn->resourceId,$_player);
            }

            $this->clients('unset',$conn->resourceId);

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



    public function sendCallback($clients, $response,$class=null) {

        if(!$class)
            $class=$this->_class;

        if(!isset($clients) OR !is_array($clients) OR !count($clients))
            echo $this->time(0,'WARNING')."  response пустой\n";

        // рассылаем игрокам результат обработки
        foreach($clients as $client) {
            if(!isset($client->bot)) {
                if(is_numeric($client))
                    $client=(object)['id'=>$client];
                if (($con = $this->clients($client->id)) && ($con instanceof ConnectionInterface)){
                    $con->send(
                        json_encode(
                            array(
                                'path' => 'app' . $class,
                                'res' => (isset($response[$client->id]) ? $response[$client->id] : $response)
                            )));
                } else
                    echo $this->time(0,'WARNING') . "  соединение #{$client->id} не найдено \n";
            }
        }
    }

    private function checkBalance($pid, $currency, $price)
    {

        if ($player = $this->players($pid)) {

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
                ? $balance['Money'] < $price *
                CountriesModel::instance()->getCountry($player['Country'])->loadCurrency()->getCoefficient()
                //$this->_settings->getCountryCoefficient((in_array($player['Country'], Config::instance()->langs) ? $player['Country'] : Config::instance()->defaultLang))
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

        if ($player = $this->players($playerId) && isset($player['appName'])){
            $class = $player['appName'];
            $mode = $player['appMode'];
            if(isset($player['appId'])){
                echo $this->time(1)." ". $player['appName'].' '. $player['appId']. " удаление appId у игрока №$playerId\n";
                $id = $player['appId'];
            }
        }

        // сдаемся и выходим, сохраняем и удаляем игру
        if (isset($class))
        {
            if($this->stack($class,$mode,$playerId)){
                $this->stack('unset',$class,$mode,$playerId);
                echo $this->time(1)." ". "$class Удаление игрока из игрового стека ожидающих \n";
            }

            //echo $this->time()." ". "Удаление игрока №$playerId из массива игроков \n";
            //unset($this->_players[$playerId]);

            if($this->stack($class,$mode) AND count($this->stack($class,$mode))==0){
                echo $this->time(1)." ". "$class Удаление стека ожидающих игроков {$class} {$mode}\n";
                $this->stack('unset',$class,$mode);
            }

            if (isset($id) AND $app = $this->apps($class,$id)) {

                //$app = $this->_apps[$class][$id];
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
                    //unset($this->_apps[$class][$id]);
                    $this->apps('unset',$class,$id);
                }
            }
        }

    }

    function saveGame($app){

        echo $this->time(1)." ".$app->getKey().' '.$app->getIdentifier(). " Состояние игры: ".$app->_isSaved."/".$app->_isOver." - Сохраняем игру: \n";
        $app->_isSaved = 1;
        print_r($app->getPlayers());

        $sql_results = "INSERT INTO `PlayerGames`
        (`PlayerId`, `GameId`, `GameUid`, `Date`, `Win`, `Lose`, `Draw`, `Result`, `Prize`, `Currency`, `Price`)
        VALUES (?,?,?,?,?,?,?,?,?,?,?)".
            str_repeat(',(?,?,?,?,?,?,?,?,?,?,?)', count($app->getPlayers())-1);

        if($app->getPrice()){
            $sql_transactions = "INSERT INTO `Transactions` (`PlayerId`, `Currency`, `Sum`, `Balance`, `Description`, `Date`) VALUES ";
            $comission = (100 - self::COMISSION) / 100;
        }

        $results = $transactions = array();
        $players = $app->getPlayers();

        foreach($players as $player)
        {
            if($app->getPrice() AND $player['result']!=0 AND !isset($app->getClients()[$player['pid']]->bot)) {
                $sql_transactions_players[]='(?,?,?,?,?,?)';

                $currency = $app->getCurrency()=='MONEY'?'Money':'Points';
                $win = isset($player['win']) ? $player['win'] : $player['result']*$app->getPrice();
                if($currency=='Money')
                    $win *= CountriesModel::instance()->getCountry($this->players($player['pid'])['Country'])->loadCurrency()->getCoefficient();

                if($win>0)
                    $win = $currency=='Money' ? ceil($win * $comission * 100) / 100 : ceil($win * $comission);

                /* update balance after game */
                $sql="UPDATE Players SET ".$currency." = ".$currency.($win < 0 ? '' : '+').($win)." WHERE Id=".$player['pid'];

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
                if($client= $this->clients($player['pid'])) {
                    $client->send(json_encode(
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
                    $win,
                    (isset($balance)?$balance[$currency]:null),
                    $app->getTitle($player['lang']),
                    $app->getTime()
                );
            }

            array_push($results,
                $player['pid'],
                $app->getId(),
                $app->getIdentifier(),
                $app->getTime(),
                ($player['result'] == 1?1:0),  // win
                ($player['result'] == -1?1:0), // lose
                ($player['result'] == 0?1:0),  // draw
                $player['result'],
                isset($player['win']) ? $player['win'] : $player['result']*$app->getPrice(),
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


    public function players($first=null, $second=null)
    {
        // $first = id, $second = value;
        $key='_players';
        $array=$second;

        if (Config::instance()->cacheEnabled) {

            if ($first == 'set')
                Cache::init()->set('websocket::' . $key, $second);
            else {
                $array = Cache::init()->get('websocket::' . $key);
                if ($first) {
                    if ($first == 'unset') {
                        unset($array[$second]);
                        Cache::init()->set('websocket::' . $key, $array);
                    } else {
                        if ($second) {
                            $array[$first] = $second;
                            Cache::init()->set('websocket::' . $key, $array);
                        }
                        $array = isset($array[$first]) ? $array[$first] : null;
                    }
                }
            }

        } else {

            if ($first == 'set')
                $this->{$key}=$second;
            else {
                $array = $this->{$key};
                if ($first) {
                    if ($first == 'unset') {
                        unset($this->{$key}[$second]);
                    } else {
                        if ($second) {
                            $this->{$key}[$first] = $second;
                        }
                        $array = isset($this->{$key}[$first]) ? $this->{$key}[$first] : null;
                    }
                }
            }

        }

        return $array;
    }

    public function clients($first=null, $second=null)
    {
        $key='_clients';
        $array=$second;

        if (Config::instance()->cacheEnabled && 0) {

            if($first=='set')
                Cache::init()->set('websocket::'.$key, $second);
            else{
                $array = Cache::init()->get('websocket::' . $key);
                if($first) {
                    if ($first == 'unset') {
                        unset($array[$second]);
                        Cache::init()->set('websocket::'.$key, $array);
                    } else {
                        if ($second) {
                            $array[$first] = $second;
                            Cache::init()->set('websocket::' . $key, $array);
                        }
                        $array = isset($array[$first]) ? $array[$first] : null;
                    }
                }
            }

        } else {

            if ($first == 'set')
                $this->{$key}=$second;
            else {
                $array = $this->{$key};
                if ($first) {
                    if ($first == 'unset') {
                        unset($this->{$key}[$second]);
                    } else {
                        if ($second) {
                            $this->{$key}[$first] = $second;
                        }
                        $array = isset($this->{$key}[$first]) ? $this->{$key}[$first] : null;
                    }
                }
            }

        }

        return $array;
    }

    public function stack($first=null, $second=null, $third=null, $fourth=null)
    {
        $key='_stack';
        $array=$fourth;

        if (Config::instance()->cacheEnabled) {

            if ($first == 'set')
                Cache::init()->set('websocket::' . $key, $second);
            else {
                $array = Cache::init()->get('websocket::' . $key);
                if ($first) {
                    if ($first == 'unset') {

                        if ($fourth)
                            unset($array[$second][$third][$fourth]);
                        elseif ($third)
                            unset($array[$second][$third]);
                        elseif ($second)
                            unset($array[$second]);

                        Cache::init()->set('websocket::' . $key, $array);

                    } else {

                        if ($second && $third && $fourth) {
                            $array[$first][$second][$third] = $fourth;
                            Cache::init()->set('websocket::' . $key, $array);
                        }

                        $array = $third
                            ? (isset($array[$first]) && isset($array[$first][$second]) && isset($array[$first][$second][$third]) ? $array[$first][$second][$third] : null)
                            : ($second
                                ? (isset($array[$first]) && isset($array[$first][$second]) ? $array[$first][$second] : null)
                                : (isset($array[$first]) ? $array[$first] : null));
                    }
                }
            }

        } else {

            if ($first == 'set')
                $this->{$key} = $second;
            else {
                $array = $this->{$key};
                if ($first) {
                    if ($first == 'unset') {

                        if ($fourth)
                            unset($this->{$key}[$second][$third][$fourth]);
                        elseif ($third)
                            unset($this->{$key}[$second][$third]);
                        elseif ($second)
                            unset($this->{$key}[$second]);

                    } else {
                        if ($second && $third && $fourth) {
                            $this->{$key}[$first][$second][$third] = $fourth;
                        }

                        $array = $third
                            ? (isset($this->{$key}[$first]) && isset($this->{$key}[$first][$second]) && isset($this->{$key}[$first][$second][$third]) ? $this->{$key}[$first][$second][$third] : null)
                            : ($second
                                ? (isset($this->{$key}[$first]) && isset($this->{$key}[$first][$second]) ? $this->{$key}[$first][$second] : null)
                                : (isset($this->{$key}[$first]) ? $this->{$key}[$first] : null));
                    }
                }
            }

        }

        return $array;
    }

    public function apps($first=null, $second=null, $third=null)
    {
        $key='_apps';
        $array = $third;

        if (Config::instance()->cacheEnabled AND 0) {

            if ($first == 'set')
                Cache::init()->set('websocket::' . $key, $second);

            else {
                $array = Cache::init()->get('websocket::' . $key);
                if ($first) {
                    if ($first == 'unset' && $second) {

                        if(!$third)
                            unset($array[$second]);
                        else
                            unset($array[$second][$third]);

                        Cache::init()->set('websocket::' . $key, $array);

                    } else {

                        if ($second && $third) {
                            $array[$first][$second] = $third;
                            Cache::init()->set('websocket::' . $key, $array);
                        }

                        $array = $second
                            ? (isset($array[$first]) && isset($array[$first][$second]) ? $array[$first][$second] : null)
                            : (isset($array[$first]) ? $array[$first] : null);
                    }
                }
            }

        } else {

            if ($first == 'set')
                $this->{$key} = $second;

            else {
                $array = $this->{$key};
                if ($first) {
                    if ($first == 'unset' && $second) {

                        if(!$third)
                            unset($this->{$key}[$second]);
                        else
                            unset($this->{$key}[$second][$third]);

                    } else {

                        if ($second && $third) {
                            $this->{$key}[$first][$second] = $third;
                        }
                        $array = $second
                            ? (isset($this->{$key}[$first]) && isset($this->{$key}[$first][$second]) ? $this->{$key}[$first][$second] : null)
                            : (isset($this->{$key}[$first]) ? $this->{$key}[$first] : null);
                    }
                }
            }

        }

        return $array;
    }

    public function rating($first=null, $second=null)
    {
        $key='_rating';
        $array=$second;

        if (Config::instance()->cacheEnabled AND 0) {

            if($first=='set')
                Cache::init()->set('websocket::'.$key, $second);
            else{
                $array = Cache::init()->get('websocket::' . $key);
                if($first) {
                    if ($first == 'unset') {
                        unset($array[$second]);
                        Cache::init()->set('websocket::'.$key, $array);
                    } else {
                        if ($second) {
                            $array[$first] = $second;
                            Cache::init()->set('websocket::' . $key, $array);
                        }
                        $array = isset($array[$first]) ? $array[$first] : null;
                    }
                }
            }

        } else {

            if ($first == 'set')
                $this->{$key}=$second;
            else {
                $array = $this->{$key};
                if ($first) {
                    if ($first == 'unset') {
                        unset($this->{$key}[$second]);
                    } else {
                        if ($second) {
                            $this->{$key}[$first] = $second;
                        }
                        $array = isset($this->{$key}[$first]) ? $this->{$key}[$first] : null;
                    }
                }
            }

        }

        return $array;
    }

    private function time($spaces=0,$str=null){
        return str_repeat (' ',$spaces).date( 'H:i:s', time() ).($str?' ['.str_pad($str,7,'.').'] ':'');
    }
}