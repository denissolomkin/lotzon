<?php namespace WebSocket;

 use Ratchet\MessageComponentInterface;
 use Ratchet\ConnectionInterface;
 use \Player;
 use Ratchet\Wamp\Exception;

 \Application::import(PATH_APPLICATION . '/model/entities/Player.php');

class WebSocketController implements MessageComponentInterface {

    private $clients;
    private $stack;
    private $apps;

    public function __construct() {
    echo "Server have started\n";
        // Create a collection of clients
        $this->clients = array();
    }

    public function onOpen(ConnectionInterface $conn) {

        $player = $conn->Session->get(Player::IDENTITY);
        $conn->resourceId=$player->getId();
        $this->clients[$conn->resourceId]=$conn;
        echo "New connection: {} (ResId: {$conn->resourceId})\n";
        $conn->send("New connection ID#".$player->getId());
        foreach ($this->clients as $client) {
            $client->send(json_encode(
                array(
                    'path'=>'chat',
                    'res'=>array(
                        'message'=>$player->getNicName().' присоединился')
                )
            ));
        }

    }

    public function onMessage(ConnectionInterface $from, $msg) {

        $data = json_decode($msg);
        list($type, $name, $id) = explode("/",$data->path);
        $data=$data->data;
        $class = '\\'.$name;
        $action=$data->action.'Action';

        switch ($type) {
            case 'app':
                try{

                    if(class_exists($class))
                    {
                       // нет запущенного приложения, пробует создать новое или просто записаться в очередь
                        if(!$id)
                        {
                            // записались
                            $this->stack[$name][$from->Session->get(Player::IDENTITY)->getId()]=$from;


                            // если насобирали минимальную очередь
                            if(count($this->stack[$name]) >= $class::STACK_PLAYERS){

                                // перемешали игроков
                                //shuffle($this->stack[$name]);
                                $keys=array_keys($this->stack[$name]);
                                shuffle($keys);

                                // начали формировать список на игру
                                foreach ( $keys as $key ) {

                                    $clients[$key] = $this->stack[$name][$key];
                                    unset ( $this->stack[$name][$key] );

                                    // дошли до необходимого числа и прервали
                                    if(count($clients)==$class::GAME_PLAYERS)
                                        break;
                                }

                                // запускаем и кешируем приложение
                                $app = new $class();
                                $app -> setClients($clients);
                                //$app -> setPlayers($clients);
                                $this->apps[$name][ $app->getIdentifier() ] = $app;
                            }
                        }
                        // пробуем загрузить приложение, проверяем наличие, если есть, загружаем
                        elseif( isset( $this->apps[$name][$id])){
                                $app = $this->apps[$name][$id];
                        }
                        // если нет, сообщаем об ошибке
                        else{
                            $from->send(json_encode(array('error'=>'APPLICATION_IS_NOT_EXISTS')));
                         }

                        // если приложение запустили или загрузили
                        if($app){
                            // пробуем вызвать экшн
                            call_user_func( array($app, $action), $data);

                            // рассылаем игрокам результат обработки
                            foreach( $app->getClients() as $client ) {
                            //    $this->clients[$client]->send(
                                $client->send(
                                    json_encode(
                                        array(
                                            'path'=>$type.$class,
                                            'res'=>$app->getCallback()
                                        )));
                            }

                            // если приложение завершилось, выгружаем из памяти
                            if($app->isOver){
                                unset( $this->apps[$name][$app->getIdentifier()] );
                            }
                        }

                    }
                    // если не нашли класс
                    else{
                        $from->send(json_encode(array('error'=>'WRONG_APPLICATION_TYPE')));
                    }

                } catch(Exception $e) {
                    $from->send($e->getMessage());
                }

                echo "app: {$class}\n";
                break;

            case 'url':
                echo "url: {$class}\n";
                break;

            default:
                foreach ($this->clients as $client) {
                    $client->send(json_encode(
                        array(
                            'path'=>'chat',
                            'res'=>array(
                                'uid'=>$from->Session->get(Player::IDENTITY)->getId(),
                                'user'=>$from->Session->get(Player::IDENTITY)->getNicName(),
                                'message'=>$data->message)
                        )
                    ));
                }
                echo "chat:\n";
                break;
        }
        /* */
    }

    public function onClose(ConnectionInterface $conn) {
        // The connection is closed, remove it, as we can no longer send it messages
        echo "Connection {$conn->resourceId} has disconnected\n";
        unset($this->clients[$conn->resourceId]);
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "An error has occurred: {$e->getMessage()}\n";
        $conn->close();
    }

}