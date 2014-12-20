<?php namespace WebSocket;

 use Ratchet\MessageComponentInterface;
 use Ratchet\ConnectionInterface;
 use \Player;
 use Ratchet\Wamp\Exception;
 use \DB;

 \Application::import(PATH_APPLICATION . '/model/entities/Player.php');
 require_once dirname(__DIR__) . '/../../system/DB.php';
 require_once dirname(__DIR__) . '/../../system/Config.php';
 require_once dirname(__DIR__) . '/../../../protected/configs/config.php';

class WebSocketController implements MessageComponentInterface {

    private $_clients;
    private $_stack;
    private $_apps;
    private $_players;
    private $_class;
    private $_mode='POINT-0';

    public function __construct() {
    echo "Server have started\n";
        // Create a collection of clients
        $this->_clients = array();
    }

    public function onOpen(ConnectionInterface $conn) {


        $player = $conn->Session->get(Player::IDENTITY);
        $conn->resourceId = $player->getId();
        $this->_clients[$conn->resourceId] = $conn;

        $class = $this->_players[$conn->resourceId]['appName'];
        $mode = $this->_players[$conn->resourceId]['appMode'];
        $id = $this->_players[$conn->resourceId]['appId'];

        // удаляемся из стека и маячки
        unset(
            $this->_stack[$class][$mode][$conn->resourceId],
            $this->_players[$conn->resourceId]);

        if($app=$this->_apps[$class][$id]){

            // сигнализируем об уходе и отключаемся от игры

            $app -> setClient($conn);
            if(!$app->isOver())
            {
                $app -> passAction();
                $this -> sendCallback($app->getResponse(),$app->getCallback());
            }
            $app -> quitAction();
            $this->sendCallback($app->getResponse(), $app->getCallback());



            // если приложение завершилось, сохраняем и выгружаем из памяти
            if($app->isOver() && !$app->isSaved()){
                $this->saveGame($app);
                unset( $this->_apps[$class][$id] );
            }
        }

        echo "New connection: #{$conn->resourceId} ".$conn->Session->getId()."\n";
        $this->_class='chat';
        $this->sendCallback($this->_clients, array('message'=>$player->getNicName().' присоединился'));
    }

    public function onMessage(ConnectionInterface $from, $msg) {

        $data = json_decode($msg);
        list($type, $name, $id) = explode("/",$data->path);
        echo "#{$from->resourceId}:".$data->path." \n";
        $data=$data->data;
        $this->_class = $class = '\\'.$name;
        $action=$data->action.'Action';
        $mode=($data->mode?$data->mode:$this->_mode);
        $player=$from->Session->get(Player::IDENTITY);



        switch ($type) {
            case 'app':
                try{

                    $this->sendCallback($this->_clients, array('message'=>$class));
                    if(class_exists($class))
                    {
                       // нет запущенного приложения, пробуем создать новое или просто записаться в очередь
                        if(!$id) {
                            echo "id приложения нет \n";
                            // записались

                            if ($action == 'quitAction') {
                                unset($this->_stack[$name][$this->_players[$from->resourceId]['appMode']][$player->getId()]);
                                unset($this->_players[$from->resourceId]['appName']);
                                unset($this->_players[$from->resourceId]['appMode']);
                            } else {
                                $this->_stack[$name][$mode][$player->getId()] = $from;
                                $this->_players[$from->resourceId]['appName'] = $name;
                                $this->_players[$from->resourceId]['appMode'] = $mode;


                            // если насобирали минимальную очередь
                            if (count($this->_stack[$name][$mode]) >= $class::STACK_PLAYERS
                                AND count($this->_stack[$name][$mode]) >= $class::GAME_PLAYERS) {

                                // перемешали игроков
                                $keys = array_keys($this->_stack[$name][$mode]);
                                shuffle($keys);

                                $app = new $class();
                                list($currency, $price) = explode("-", $mode);
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

                                // запускаем и кешируем приложение
                                $app->setClients($clients);
                                $app->setClient($from);
                                $this->_apps[$name][$app->getIdentifier()] = $app;

                            } else {
                                $this->sendCallback($from, array('action' => 'stack', 'stack' => count($this->_stack[$name][$mode]), 'mode' => $mode));
                            }
                        }
                        }

                        // пробуем загрузить приложение, проверяем наличие, если есть, загружаем и удаляем игрока из стека
                        elseif($app = $this->_apps[$name][$id]){
                            echo "приложение нашли, удаляем игрока из очереди \n";
                            unset ( $this->_stack[$name][$mode][$player->getId()] );
                        }

                        // если нет, сообщаем об ошибке
                        else{
                            echo "id есть, но приложения $name нет, сообщаем об ошибке, удаляем из активных игроков \n";
                            $this->sendCallback($from, array(
                                'action'=>'error',
                                'error'=>'APPLICATION_IS_NOT_EXISTS',
                                'appId'=>0));
                            unset($this->_players[$from->Session->get(Player::IDENTITY)->getId]);
                         }

                        // если приложение запустили или загрузили
                        if(isset($app)){
                            echo "стартуем приложение $name $id \n";

                            // пробуем вызвать экшн
                            $app -> setClient($from);
                            call_user_func( array($app, $action), $data);

                            // рассылаем игрокам результат обработки
                            $this -> sendCallback($app->getResponse(),$app->getCallback());

                            // если приложение завершилось, записываем данные и выгружаем из памяти
                            if(!$app->isSaved() && $app->isOver())
                                $this->saveGame($app);

                        }
                    }
                    // если не нашли класс
                    else{
                        $from->send(json_encode(array('error'=>'WRONG_APPLICATION_TYPE')));
                    }

                } catch(Exception $e) {
                    $from->send($e->getMessage());
                }

                break;

            case 'url':
                break;

            default:
                if($data->message=='stop')
                    die;

                foreach ($this->_clients as $client) {
                    $client->send(json_encode(
                        array(
                            'path'=>'chat',
                            'res'=>array(
                                'uid'=>$player->getId(),
                                'user'=>$player->getNicName(),
                                'message'=>$data->message)
                        )
                    ));
                }
                break;
        }
        /* */
    }

    public function onClose(ConnectionInterface $conn) {

        // The connection is closed, remove it, as we can no longer send it messages
        $class = $this->_players[$conn->resourceId]['appName'];
        $mode = $this->_players[$conn->resourceId]['appMode'];
        $id = $this->_players[$conn->resourceId]['appId'];

        echo"удаляемся из приложения \n";
        // удаляемся из стека и маячки
        unset(
            $this->_stack[$class][$mode][$conn->resourceId],
            $this->_players[$conn->resourceId]);

        if($app=$this->_apps[$class][$id]){
            // пробуем вызвать экшн
            $app -> setClient($conn);
            if(!$app->isOver())
            {
                $app -> passAction();
                $this -> sendCallback($app->getResponse(),$app->getCallback());
            }

            $app -> quitAction();
            $this -> sendCallback($app->getResponse(),$app->getCallback());

            // если приложение завершилось, сохраняем и выгружаем из памяти
            if($app->isOver() && !$app->isSaved()){
                $this->saveGame($app);
                unset( $this->_apps[$class][$id] );
            }
        }

        echo "Connection {$conn->resourceId} has disconnected ".$conn->Session->get('appId')."\n";
        unset($this->_clients[$conn->resourceId]);

        foreach ($this->_clients as $client) {
            $client->send(json_encode(
                array(
                    'path'=>'appchat',
                    'res'=>array(
                        'message'=>$conn->Session->get(Player::IDENTITY)->getNicName().' отсоединился')
                )));
        }
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "An error has occurred: {$e->getMessage()}\n";
        $conn->close();
    }

    public function sendCallback($response, $callback) {

        // рассылаем игрокам результат обработки
        if(is_array($response))
            foreach( $response as $client )
                $client->send(
                    json_encode(
                        array(
                            'path'=>'app'.$this->_class,
                            'res'=>$callback
                        )));
        else
            $response->send(
                json_encode(
                    array(
                        'path'=>'app'.$this->_class,
                        'res'=>$callback
                    )));
    }

    function saveGame($app){

        $app->_isSaved = 1;
        $sql_results = "INSERT INTO `PlayersGames` (`PlayerId`, `GameId`, `GameUid`, `Date`, `Result`, `Currency`, `Price`) VALUES (?,?,?,?,?,?,?)".
            str_repeat(',(?,?,?,?,?,?,?)', count($app->getPlayers())-1);

        if($app->getPrice())
            $sql_transactions = "INSERT INTO `Transactions` (`PlayerId`, `Currency`, `Sum`, `Description`, `Date`) VALUES ";

        $results = $transactions = array();

        foreach($app->getPlayers() as $player)
        {
            if($app->getPrice() AND $player['result']!=0) {
                $sql_transactions_players[]='(?,?,?,?,?)';

                array_push($transactions,
                    $player['pid'],
                    $app->getCurrency(),
                    $app->getPrice()*$player['result'],
                    'Игра '.$app->getTitle().' №'.$app->getIdentifier(),
                    time()
                );

                $currency=$app->getCurrency()=='MONEY'?'Money':'Points';
                $sql="UPDATE Players SET ".$currency." = ".$currency.($player['result'] < 0 ? '' : '+').
                    ($player['result']*$app->getPrice())." WHERE Id=".$player['pid'];
                echo $sql;
                DB::Connect()->query($sql);

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

            array_push($results,
                $player['pid'],
                $app->getId(),
                $app->getIdentifier(),
                time(),
                $player['result'],
                $app->getCurrency(),
                $app->getPrice()
            );
        }
        try {
            DB::Connect()->prepare($sql_results)->execute($results);
            if($app->getPrice())
                DB::Connect()->prepare($sql_transactions.implode(",",$sql_transactions_players))->execute($transactions);
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query" . $e->getMessage(), 500);
        }
    }
}