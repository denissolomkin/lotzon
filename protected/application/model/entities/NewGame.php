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

    private $_gameid = 1;
    public  $_bot = 0;
    public  $_botTimer = 0;
    public  $_botReplay =0;
    private $_gameTitle = '"Кто больше"';
    private $_gameCurrency = '';
    private $_gamePrice = null;
    private $_gameTime = null;

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
        $this->setField($this->generateField())
            ->setIdentifier(uniqid())
            ->setTime(time());
    }

    public function quitAction($data=null)
    {
        #echo ' '.time().' '. "Выход из игры\n";

        $playerId = $this->getClient()->id;
        $this->unsetCallback();
        $this->setCallback(array(
            'quit' => $playerId,
            'action' => 'quit'
        ));

        $this->setResponse($this->getClients());
        #echo ' '.time().' '. "Удаляем из клиентов игры {$playerId}\n";
        $this->unsetClients($playerId);

        #echo ' '.time().' '. "Конец выход из игры\n";
    }

    public function passAction($data=null)
    {
        #echo ' '.time().' '. "Сдаться\n";

        $playerId = $this->getClient()->id;

        $this->updatePlayer(array('result' => 1))
            ->updatePlayer(array('result' => -2), $playerId)
            ->unsetCallback();
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
            #echo ' '.time().' '. "Конец сдаться\n";
    }


    public function replayAction($data=null)
    {
            echo ''.time().' '. "Повтор игры {$this->getClient()->id} \n";

            $clientId = $this->getClient()->id;
            $this->updatePlayer(array('ready' => 1), $clientId );
            $players = $this->getPlayers();

            if(isset($this->getClient()->bot) AND !$this->_botReplay){
                $this->_botReplay=1;
            }

            $ready = 0;
            foreach ($players as $player){
                if (isset($player['ready']))// || isset($this->getClients()[$player['pid']]->bot))
                    $ready += 1;
            }

            if ($ready == count($players)) {
                $this->_isOver = 0;
                $this->_isSaved = 0;
                #echo ' '.time().' '. "Переустановка игроков\n";
                $this->unsetFieldPlayed()
                    ->setField($this->generateField())
                    ->setPlayers($this->getClients())
                    ->nextPlayer()
                    ->startAction();
            } else {
                $this->unsetCallback()
                    ->setResponse($clientId)
                    ->setCallback(array(
                    'action' => 'ready',
                    'ready' => $ready
                ));
            }

            #echo ' '.time().' '. "Конец повтора игры\n";
    }

    public function startAction($data=null)
    {
        #echo ''.time().' '. "Старт\n";
        $this->unsetCallback();

        if(!$this->getPlayers()) {
            #echo ' '.time().' '. "Первичная установка игроков\n";
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
        $this->unsetCallback();
        if(isset($this->getClients()[$this->currentPlayer()['pid']]->bot)){
            #echo ''.time().' '. "ход бота\n";
            $this->setClient($this->currentPlayer()['pid']);
            list($x,$y) = $this->generateMove();}
        else{
            #echo ''.time().' '. "ход игрока\n";
            list($x,$y) = explode('x',$data->cell);}

        if($error=$this->checkError($x, $y))
            $this->setCallback(array('error' => $error));
        else {
            #echo ' '.time().' '. "ход";
            $this->doMove($x,$y);
            #echo ' '.time().' '. "следующий игрок";
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
                ->setResponse($this->getClient()->id);

        elseif(!array_key_exists('action',$this->getCallback()))
            $this->setCallback(array('action' => 'move'));
        #echo ' '.time().' '. "Конец хода \n";
    }

    public function timeoutAction($data=null)
    {

        #echo ' '.time().' '. "Тайм-аут \n";
        $this->unsetCallback();
        if(!$this->isOver() AND isset($this->currentPlayer()['timeout']) AND $this->currentPlayer()['timeout']<=time())
        {
            #echo ' '.time().' '. "Переход хода \n";
            $this->passMove();
            #echo ' '.time().' '. 'разница времени после пасса '.$this->currentPlayer()['pid'].' - '.time().' - '.$this->currentPlayer()['timeout']."\n";
            $this->nextPlayer();
            #echo ' '.time().' '. 'разница времени после перехода '.$this->currentPlayer()['pid'].' - '.time().' - '.$this->currentPlayer()['timeout']."\n";
        }

        if($winner=$this->checkWinner())
            $this->setCallback(array(
                'winner' => $winner['pid'],
                'price' => $this->getPrice(),
                'currency' => $this->getCurrency()));

        $currentPlayer=$this->currentPlayer();

        $this->setCallback(array(
            'current'   => $this->currentPlayer()['pid'],
            'timeout'   => $currentPlayer['timeout']-time(),//($this->currentPlayer()['timeout']-time()>0?$this->currentPlayer()['timeout']-time():1),
            'players'   => $this->getPlayers(),
            'action'    => 'move'
            ));

        $this->setResponse($this->getClients());
        #echo ' '.time().' '. "Конец тайм-аута \n";
    }

    public function checkError($x, $y)
    {
        #echo ' '.time().' '. "Проверка ошибок \n";
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
        $current = $this->currentPlayer();
        return ($current['pid'] == $this->getClient()->id);

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
        #echo ' '.time().' '. "Пас хода \n";
        $current=$this->currentPlayer();
        $this->updatePlayer(array(
                'moves'=>-1), $current['pid']);

        return $this;
    }

    public function doMove($x,$y)
    {

            $playerId = $this->getClient()->id;
            $points = $this->_field[$x][$y]['points'];
            $this->_field[$x][$y]['player'] = $playerId;

            $this->updatePlayer(array(
                'points' => $points,
                'moves' => -1), $playerId);

            $this->_fieldPlayed[$x][$y] = $this->_field[$x][$y];

            return $this;
    }

    public function generateMove()
    {

        //$minimum=(rand(1,2)<2?(pow(self::FIELD_SIZE,2)-(self::GAME_MOVES*(self::GAME_PLAYERS+1))):0);
        // $minimum=( rand(1,5) < 2 ? (pow(self::FIELD_SIZE,2)/2):0 );
        $minimum=0;

        #echo ' '.time().' '. "Генерация поля для бота\n";
        do {
            do {
                $x=rand(1,self::FIELD_SIZE);
                $y=rand(1,self::FIELD_SIZE);
            } while($this->_field[$x][$y]['player']);
        } while($this->_field[$x][$y]['points']<$minimum);

        #echo ' '.time().' '. "Ход бота $x, $y = $minimum ".$this->_field[$x][$y]['points']."\n";
        return array($x, $y);
    }

    public function nextPlayer()
    {
        #echo ' '.time().' '. "Следующий игрок \n";
        if (next($this->_players) === false) {
            reset($this->_players);
        }

        $this->_players[(current($this->_players)['pid'])]['timeout']=time()+self::TIME_OUT;

        if(isset($this->_clients[(current($this->_players)['pid'])]->bot))
            $this->_botTimer = rand(1,3);
        else
            $this->_botTimer = 0;

        return $this; //current($this->_players);
    }

    public function currentPlayer()
    {
        return current($this->_players);
    }

    public function checkWinner()
    {
        #echo ' '.time().' '. "Проверка победителя \n";
        $current = $this->currentPlayer();
        if ($current['moves'] < 1)
        {
            $players=$this->getPlayers();
            $winner=array();
            foreach ($players as $player){
                if(array_key_exists($player['points'],$winner))
                    $winner[$player['points']]['count']+=1;
                else
                    $winner[$player['points']]['count']=1;
                $winner[$player['points']]['player']=$player;
            }

            krsort($winner);

            if (isset(current($winner)['count']) && current($winner)['count'] == 1) {
                #echo ' '.time().' '. "Победитель #".current($winner)['player']['pid']."\n";
                $this->updatePlayer(array('result'=>-1));
                $this->updatePlayer(array('result'=>2),current($winner)['player']['pid']);
                $this->_isOver=1;
                $this->_botReplay=0;
                return current($winner)['player'];
            } else {
                #echo ' '.time().' '. "Экстра время \n";
                $this->setCallback(array('extra' => 1));
                $this->updatePlayer(array('moves' => 1));
            }

        }
        #echo ' '.time().' '. "Победителя нет \n";

    }

    public function updatePlayer($data,$id=null)
    {
        $currentPlayer=$this->currentPlayer();
        #echo ' '.time().' '. "Обновление данных\n";
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
                        #echo ' '.time().' '. "Удаление из игроков {$value}\n";
                    }
                }
            }
        }

        if(!$id){
            reset($players);
            while(each($players)==$currentPlayer){}
                #echo ' '.time().' '. "\n".(1)."\n";//print_r($this->currentPlayer());
        }


        return $this;
    }

    public function getPlayers()
    {
        return $this->_players;
    }

    public function setPlayers($clients)
    {
        rand(0,1)?arsort($clients):asort($clients);

        foreach ($clients as $id => $client){

            if(isset($client->bot))
                $this->_bot=$id;

            $this->_players[$id] = array(
                'pid' => $id,
                'moves' => self::GAME_MOVES,
                'points' => 0,
                'avatar' => $client->avatar,
                'name' => $client->name
            );
        }

        #echo ' '.time().' '. "Инициализация игроков";
        return $this;
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
        $this->_client=$this->getClients()[$client];

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

    public function setIdentifier($identifier)
    {
        $this->_identifier = $identifier;

        return $this;
    }

    public function getIdentifier()
    {
        return $this->_identifier;
    }

    public function getId()
    {
        return $this->_gameid;
    }

    public function getTitle()
    {
        return $this->_gameTitle;
    }

    public function setPrice($price)
    {
        $this->_gamePrice = $price;
        return $this;
    }

    public function getTime()
    {
        return $this->_gameTime;
    }

    public function setTime($time)
    {
        $this->_gameTime = $time;
        return $this;
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

    public function setCurrency($currency)
    {
        $this->_gameCurrency = $currency;
        return $this;
    }

    public function getFieldPlayed()
    {
        return $this->_fieldPlayed;
    }

    public function unsetFieldPlayed()
    {
        $this->_fieldPlayed=array();
        return $this;
    }

    public function setField($field)
    {
        $this->_field = $field;
        return $this;
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
