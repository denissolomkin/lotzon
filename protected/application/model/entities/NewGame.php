<?php

Application::import(PATH_APPLICATION . 'model/Entity.php');
Application::import(PATH_APPLICATION . 'model/entities/Player.php');

class NewGame extends Entity
{
    const   STACK_PLAYERS = 2;
    const   GAME_PLAYERS = 2;
    const   TIME_OUT = 20;
    const   FIELD_SIZE = 7;
    const   GAME_MOVES = 6;

    private $_id = 1;
    private $_gameTitle = '"Кто больше"';
    private $_gameCurrency = '';
    private $_gamePrice = null;

    private $_identifier = '';
    private $_players = array();
    private $_client = '';
    public  $_isOver = 0;
    public  $_isSaved = 0;
    private $_clients = array();
    private $_response = '';
    private $_callback = array();
    private $_field = array();
    private $_fieldPlayed = array();

    public function init()
    {
        $this->setField($this->generateField());
        $this->setIdentifier(uniqid());
    }

    public function quitAction($data=null)
    {
        if($this->getClient()->Session->get(PLAYER::IDENTITY)!==null) {
            #echo "Выход из игры\n";
            $this->unsetCallback();
            $playerId = $this->getClient()->Session->get(PLAYER::IDENTITY)->getId();

            $this->setCallback(array(
                'quit' => $playerId,
                'action' => 'quit'
            ));

            $this->setResponse($this->getClients());
            #echo "Удаляем из клиентов игры {$playerId}\n";
            $this->unsetClients($playerId);
            #echo "Конец выход из игры\n";
        }
    }

    public function passAction($data=null)
    {
        if($this->getClient()->Session->get(PLAYER::IDENTITY)!==null) {
            #echo "Сдаться\n";

            $this->unsetCallback();
            $playerId = $this->getClient()->Session->get(PLAYER::IDENTITY)->getId();
            $this->updatePlayer(array('result' => 1));
            $this->updatePlayer(array('result' => -2), $playerId);

            $this->_isOver = 1;
            $this->_isSaved = 0;

            if (count($this->getPlayers()) > 1)
                while ($this->currentPlayer()['pid'] == $playerId)
                    $this->nextPlayer();

            $this->setCallback(array(
                'winner' => $this->currentPlayer()['pid'],
                'players' => $this->getPlayers(),
                'currency' => $this->getCurrency(),
                'price' => $this->getPrice(),
                'action' => 'move'
            ));

            $this->setResponse($this->getClients());
            #echo "Конец сдаться\n";
        }
    }


    public function replayAction($data=null)
    {
        if($this->getClient()->Session->get(PLAYER::IDENTITY)!==null) {
            #echo "Повтор игры\n";
            $playerId = $this->getClient()->Session->get(PLAYER::IDENTITY)->getId();
            $this->updatePlayer(array('ready' => 1), $playerId);
            $players = $this->getPlayers();

            $ready = 0;
            foreach ($players as $player)
                if (isset($player['ready']))
                    $ready += $player['ready'];

            if ($ready == count($players)) {
                $this->unsetFieldPlayed();
                $this->setField($this->generateField());
                $this->setPlayers($this->getClients());
                $this->_isOver = 0;
                $this->_isSaved = 0;
                $this->startAction();
            } else {
                $this->unsetCallback();
                $this->setResponse($this->getClient());
                $this->setCallback(array(
                    'action' => 'ready',
                    'ready' => $ready
                ));
            }

            #echo "Конец повтора игры\n";
        }
    }

    public function startAction($data=null)
    {
        #echo "Старт\n";
        $this->unsetCallback();

        if(!$this->getPlayers()) {
            $this->setPlayers($this->getClients());
            $this->nextPlayer();
            $this->_isOver=0;
            $this->_isSaved=0;
        }

        $this->setCallback(array(
            'current'   => $this->currentPlayer()['pid'],
            'timeout'   => self::TIME_OUT,
            'gid'       => $this->getIdentifier(),
            'players'   => $this->getPlayers(),
            'field'     => $this->getFieldPlayed(),
            'action'    => 'start'
        ));

        $this->updatePlayer( array('ready','avatar','name','result') );
        $this->setResponse($this->getClients());
    }

    public function moveAction($data=null)
    {
        #echo "Ход \n";
        $this->unsetCallback();
        list($x,$y)=explode('x',$data->cell);

        if($error=$this->checkError($x, $y))
            $this->setCallback(array('error' => $error));
        else {

            $this->doMove($x,$y);
            $this->nextPlayer();

            if($winner=$this->checkWinner()){
                $this->setCallback(array(
                    'winner' => $winner['pid'],
                    'currency' => $this->getCurrency(),
                    'price'    => $this->getPrice()
                ));
            }

            $this->setCallback(array(
                'current'   => $this->currentPlayer()['pid'],
                'timeout'   => $this->currentPlayer()['timeout']-time(),
                'cell'      => $this->getCell($x,$y),
                'players'   => $this->getPlayers()
            ));
        }

        $this->setResponse($this->getClients());

        if (array_key_exists('error',$this->getCallback()))
            $this->setCallback(array('action' => 'error'))
                ->setResponse($this->getClient());

        elseif(!array_key_exists('action',$this->getCallback()))
        $this->setCallback(array('action' => 'move'));
        #echo "Конец хода \n";
    }

    public function timeoutAction($data=null)
    {
        #echo "Тайм-аут \n";
        $this->unsetCallback();
        if(isset($this->currentPlayer()['timeout']) AND $this->currentPlayer()['timeout']<=time())
        {
            #echo "Переход хода \n";
            if(!$this->isOver()) {
            $this->passMove();
            $this->nextPlayer();
            }

        }

        if($winner=$this->checkWinner())
            $this->setCallback(array(
                'winner' => $winner['pid'],
                'price' => $this->getPrice(),
                'currency' => $this->getCurrency()));

        $this->setCallback(array(
            'current'   => $this->currentPlayer()['pid'],
            'timeout'   => $this->currentPlayer()['timeout']-time(),
            'players'   => $this->getPlayers(),
            'action'    => 'move'
            ));

        $this->setResponse($this->getClients());

        #echo "Конец тайм-аута \n";
    }

    public function checkError($x, $y)
    {
        #echo "Проверка ошибок \n";
        if (!$this->isMove())
            return 'NOT_YOUR_MOVE';
        elseif (!$this->isCell($x,$y))
            return 'WRONG_CELL '.$x.'x'.$y;
        elseif ($this->isClicked($x,$y))
            return 'CELL_IS_PLAYED';
        elseif($this->isOver())
            return 'GAME_IS_OVER';
    }

    public function isCell($x,$y)
    {
        return ($x>0 && $y>0 && $x<=self::FIELD_SIZE && $y<=self::FIELD_SIZE);
    }

    public function isClicked($x,$y)
    {
        return $this->_field[$x][$y]['player'];
    }


    public function isMove()
    {
        if($this->getClient()->Session->get(PLAYER::IDENTITY)!==null) {
            $current = $this->currentPlayer();
            return ($current['pid'] == $this->getClient()->Session->get(PLAYER::IDENTITY)->getId());
        }
    }

    public function isOver()
    {
        return $this->_isOver;
    }

    public function isSaved()
    {
        return $this->_isSaved;
    }

    public function getCell($x,$y)
    {
        return $this->_field[$x][$y];
    }

    public function passMove()
    {
        #echo "Пас хода \n";
        $current=$this->currentPlayer();
        $this->updatePlayer(array(
                'moves'=>-1), $current['pid']);

        return $this;
    }

    public function doMove($x,$y)
    {
        if($this->getClient()->Session->get(PLAYER::IDENTITY)!==null) {
            #echo "Запись хода \n";
            $playerId = $this->getClient()->Session->get(PLAYER::IDENTITY)->getId();
            $points = $this->_field[$x][$y]['points'];
            $this->_field[$x][$y]['player'] = $playerId;

            $this->updatePlayer(array(
                'points' => $points,
                'moves' => -1), $playerId);

            $this->_fieldPlayed[$x][$y] = $this->_field[$x][$y];

            return $this;
        }
    }

    public function nextPlayer()
    {
        #echo "Следующий игрок \n";
        if (next($this->_players) === false) {
            reset($this->_players);
        }
       // #echo (current($this->_players)['pid']);
        $this->_players[(current($this->_players)['pid'])]['timeout']=time()+self::TIME_OUT;
        return current($this->_players);
    }

    public function currentPlayer()
    {
        return current($this->_players);
    }

    public function checkWinner()
    {
        #echo "Проверка победителя \n";
        $current = $this->currentPlayer();
        if ($current['moves'] < 1)
        {
            $winner=array();
            foreach ($this->getPlayers() as $player){
                if(array_key_exists($player['points'],$winner))
                    $winner[$player['points']]['count']+=1;
                else
                    $winner[$player['points']]['count']=1;
                $winner[$player['points']]['player']=$player;
            }

        krsort($winner);

        if (isset(current($winner)['count']) && current($winner)['count'] == 1) {
            $this->updatePlayer(array('result'=>-1));
            $this->updatePlayer(array('result'=>2),current($winner)['player']['pid']);
            $this->_isOver=1;
            // $this->_isSaved=0;
            #echo "Победитель #".current($winner)['player']['pid']."\n";
            return current($winner)['player'];
        } else {
            $this->setCallback(array('extra' => 1));
            $this->updatePlayer(array('moves' => 1));
        }

        }
        #echo "Победителя нет \n";

    }

    public function updatePlayer($data,$id=null)
    {
        #echo "Обновление игроков \n";
        if($id)
            $players[]=$this->getPlayers()[$id];
        else
            $players=$this->getPlayers();


        foreach ($players as $player){
            foreach ($data as $key => $value)
            {
                if(!is_numeric($key)){
                    if(array_key_exists($key,$this->_players[$player['pid']]))
                        $this->_players[$player['pid']][$key]+=$value;
                    else
                        $this->_players[$player['pid']][$key]=$value;
                } else {
                    if(array_key_exists($value,$this->_players[$player['pid']])) {
                        unset($this->_players[$player['pid']][$value]);
                        #echo "Удаление из игроков {$value}\n";
                    }
                }
            }
        }

        return $this;
    }

    public function getPlayers()
    {
        return $this->_players;
    }

    public function setPlayers($clients)
    {
        if($this->getClient()->Session->get(PLAYER::IDENTITY)!==null) {
            //shuffle($clients);
            #echo "Инициализация игроков \n";
            foreach ($clients as $pid => $player)
                $this->_players[$pid] = array(
                    'pid' => $pid,
                    'moves' => self::GAME_MOVES,
                    'points' => 0,
                    'avatar' => $player->Session->get(Player::IDENTITY)->getAvatar(),
                    'name' => $player->Session->get(Player::IDENTITY)->getNicName());

            return $this;
        }
    }

    public function setResponse($clients)
    {
        $this->_response=$clients;

        return $this;
    }

    public function getResponse()
    {
        return $this->_response;
    }

    public function setClient($client)
    {
        $this->_client=$client;

        return $this;
    }

    public function getClient()
    {
        return $this->_client;
    }

    public function getClients()
    {
        return $this->_clients;
    }

    public function unsetClients($id=null)
    {
        if($id)
            unset($this->_clients[$id]);
        else
            unset($this->_clients);

        return $this;
    }

    public function setClients($clients)
    {
        if($clients)
            $this->_clients=$clients;

        return $this;
    }

    public function setIdentifier($identity)
    {
        $this->_identifier = $identity;

        return $this;
    }

    public function getIdentifier()
    {
        return $this->_identifier;
    }

    public function getId()
    {
        return $this->_id;
    }

    public function getTitle()
    {
        return $this->_gameTitle;
    }

    public function getPrice()
    {
        return $this->_gamePrice;
    }

    public function getCurrency()
    {
        return $this->_gameCurrency;
    }

    public function unsetCallback()
    {
        unset($this->_callback);
        return $this;
    }

    public function setCallback($callback)
    {
        foreach($callback as $key=>$value)
            $this->_callback[$key] = $value;
        return $this;
    }

    public function getCallback()
    {
        return $this->_callback;
    }

    public function setPrice($price)
    {
        $this->_gamePrice = $price;
        return $this;
    }

    public function setCurrency($currency)
    {
        $this->_gameCurrency = $currency;
        return $this;
    }

    public function setField($field)
    {
        $this->_field = $field;
        return $this;
    }

    public function getFieldPlayed()
    {
        return $this->_fieldPlayed;
    }

    public function unsetFieldPlayed()
    {
        $this->_fieldPlayed=array();
    }

    public function getField()
    {
        return $this->_field;
    }

    public function generateField() {

        $numbers = range(1, pow(self::FIELD_SIZE,2));
        shuffle($numbers);

        for ($i = 1; $i <= self::FIELD_SIZE ; ++$i) {
            for ($j = 1; $j <= self::FIELD_SIZE; ++$j) {
                $gameField[$i][$j]['points'] = $numbers[(($i-1)*self::FIELD_SIZE+$j)-1];
                $gameField[$i][$j]['player'] = null;
                $gameField[$i][$j]['coord'] = $i.'x'.$j;
            }
        }

        return $gameField;
    }



}
