<?php

class Game
{
    const   STACK_PLAYERS = 2;
    const   GAME_PLAYERS = 2;
    const   TIME_OUT = 20;
    const   FIELD_SIZE_X = 7;
    const   FIELD_SIZE_Y = 7;
    const   GAME_MOVES = 6;
    const   BOT_ENABLED = 1;

    protected $_gameid = 0;
    protected $_gameTitle = '';

    public  $_bot = 0;
    public  $_botTimer = 0;
    public  $_botReplay =0;
    private $_gameCurrency = '';
    private $_gamePrice = null;
    private $_gameTime = null;

    private $_identifier = '';
    private $_client = '';
    public  $_isOver = 0;
    public  $_isSaved = 0;
    private $_clients = array();
    private $_response = '';
    private $_callback = array();

    protected $_players = array();
    protected $_field = array();
    protected $_fieldPlayed = array();

    public function __construct() {
        $this->init();
    }

    public function init()
    {
        $this->setField($this->generateField())
            ->setIdentifier(uniqid())
            ->setTime(time());
    }

    public function quitAction($data=null)
    {
        #echo $this->time().' '. "Выход из игры\n";

        $playerId = $this->getClient()->id;
        $this->unsetCallback();
        $this->setCallback(array(
            'quit' => $playerId,
            'action' => 'quit'
        ));

        $this->setResponse($this->getClients());
        #echo $this->time().' '. "Удаляем из клиентов игры {$playerId}\n";
        $this->unsetClients($playerId);

        #echo $this->time().' '. "Конец выход из игры\n";
    }

    public function passAction($data=null)
    {
        #echo $this->time().' '. "Сдаться\n";

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
            #echo $this->time().' '. "Конец сдаться\n";
    }


    public function replayAction($data=null)
    {
            #echo $this->time().' '. "Повтор игры {$this->getIdentifier()} ".(isset($this->getClient()->bot) ?'бот':'игрок')." №{$this->getClient()->id} \n";
        echo " REPLAY  \n";

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
                #echo $this->time().' '. "Переустановка игроков\n";

                $this->unsetFieldPlayed()
                    ->setField($this->generateField())
                    ->setPlayers($this->getClients())
                    ->nextPlayer()
                    ->setTime(time())
                    ->startAction();
            } else {
                $this->unsetCallback()
                    ->setResponse($this->getClient())
                    ->setCallback(array(
                    'action' => 'ready',
                    'ready' => $ready
                ));
            }

            #echo $this->time().' '. "Конец повтора игры\n";
    }

    public function startAction($data=null)
    {
        #echo ''.time().' '. "Старт\n";
        $this->unsetCallback();

        if(!$this->getPlayers()) {
            #echo $this->time().' '. "Первичная установка игроков\n";
            $this->setPlayers($this->getClients());
            $this->nextPlayer();
            $this->_isOver=0;
            $this->_isSaved=0;
        }

        $this->setCallback(array(
            'current'   => $this->currentPlayer()['pid'],
            'timeout'   => $this->currentPlayer()['timeout']-time(),
            'appId'       => $this->getIdentifier(),
            'appMode' => $this->getCurrency().'-'.$this->getPrice(),
            'players'   => $this->getPlayers(),
            'field'     => $this->getFieldPlayed(),
            'action'    => 'start'
        ));

        $this->updatePlayer( array('ready','result') );
        $this->setResponse($this->getClients());
    }

    public function moveAction($data=null)
    {
        $this->unsetCallback();
        if(!isset($data->cell) OR isset($this->getClients()[$this->currentPlayer()['pid']]->bot)){
            #echo ''.time().' '. "ход бота\n";
            $cell = $this->generateMove();}
        else{
            #echo ''.time().' '. "ход игрока\n";
            $cell = explode('x',$data->cell);}

        if($error=$this->checkError($cell))
            $this->setCallback(array('error' => $error));
        else {
            #echo $this->time().' '. "ход";
            $this->doMove($cell);

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
                'cell'      => $this->getCell($cell),
                'players'   => $this->getPlayers()
            ));
        }

        $this->setResponse($this->getClients());

        if (array_key_exists('error',$this->getCallback()))
            $this->setCallback(array('action' => 'error'))
                ->setResponse($this->getClient());

        elseif(!array_key_exists('action',$this->getCallback()))
            $this->setCallback(array('action' => 'move'));
        #echo $this->time().' '. "Конец хода \n";
    }

    public function timeoutAction($data=null)
    {

        #echo $this->time().' '. "Тайм-аут \n";
        $this->unsetCallback();
        if(!$this->isOver() AND isset($this->currentPlayer()['timeout']) AND $this->currentPlayer()['timeout']<=time())
        {
            #echo $this->time().' '. "Переход хода \n";
            $this->passMove();
            #echo $this->time().' '. 'разница времени после пасса '.$this->currentPlayer()['pid'].' - '.time().' - '.$this->currentPlayer()['timeout']."\n";
            $this->nextPlayer();
            #echo $this->time().' '. 'разница времени после перехода '.$this->currentPlayer()['pid'].' - '.time().' - '.$this->currentPlayer()['timeout']."\n";
        }

        if($winner=$this->checkWinner())
            $this->setCallback(array(
                'winner' => $winner['pid'],
                'price' => $this->getPrice(),
                'currency' => $this->getCurrency()));

        $currentPlayer=$this->currentPlayer();

        $this->setCallback(array(
            'current'   => $this->currentPlayer()['pid'],
            'timeout'   => (isset($currentPlayer['timeout'])?$currentPlayer['timeout']:time()+1)-time(),//($this->currentPlayer()['timeout']-time()>0?$this->currentPlayer()['timeout']-time():1),
            'players'   => $this->getPlayers(),
            'action'    => 'move'
            ));

        $this->setResponse($this->getClients());
        #echo $this->time().' '. "Конец тайм-аута \n";
    }

    public function checkError($cell)
    {
        list($x,$y)=$cell;
        #echo $this->time().' '. "Проверка ошибок \n";
        if (!$this->isMove()){
            echo " NOT_YOUR_MOVE\n";
            return 'NOT_YOUR_MOVE';
        } elseif (!$this->isCell($cell)){
            echo " WRONG_CELL\n";
            return 'WRONG_CELL '.$x.'x'.$y;
        } elseif ($this->isClicked($cell)){
            echo " CELL_IS_PLAYED\n";
            return 'CELL_IS_PLAYED';
        } elseif($this->isOver()) {
            echo " GAME_IS_OVER\n";
            return 'GAME_IS_OVER';
        }
    }

    public function isCell($cell)
    {
        list($x,$y)=$cell;
        return ($x>0 && $y>0 && $x<=static::FIELD_SIZE_X && $y<=static::FIELD_SIZE_Y);
    }

    public function isClicked($cell)
    {
        list($x,$y)=$cell;
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

    public function getCell($cell)
    {
        list($x,$y)=$cell;
        return $this->_field[$x][$y];
    }

    public function passMove()
    {
        #echo $this->time().' '. "Пас хода \n";
        $current=$this->currentPlayer();
        $this->updatePlayer(array(
                'moves'=>-1), $current['pid']);

        return $this;
    }

    public function doMove($cell)
    {
        list($x,$y)=$cell;
            $playerId = $this->getClient()->id;
            $points = $this->_field[$x][$y]['points'];
            $this->_field[$x][$y]['player'] = $playerId;

            $this->updatePlayer(array(
                'points' => $points,
                'moves' => -1), $playerId);

            $this->_fieldPlayed[$x][$y] = $this->_field[$x][$y];

            #echo $this->time().' '. "следующий игрок";
            $this->nextPlayer();
            return $this;
    }

    public function generateMove()
    {

        //$minimum=(rand(1,2)<2?(static::FIELD_SIZE_X*static::FIELD_SIZE_Y)-(self::GAME_MOVES*(self::GAME_PLAYERS+1))):0);
        // $minimum=( rand(1,5) < 2 ? (static::FIELD_SIZE_X*static::FIELD_SIZE_Y/2):0 );
        $minimum=0;

        #echo $this->time().' '. "Генерация поля для бота\n";
        do {
            do {
                $x=rand(1,static::FIELD_SIZE_X);
                $y=rand(1,static::FIELD_SIZE_Y);
            } while($this->_field[$x][$y]['player']);
        } while($this->_field[$x][$y]['points']<$minimum);

        #echo $this->time().' '. "Ход бота $x, $y = $minimum ".$this->_field[$x][$y]['points']."\n";
        return array($x, $y);
    }

    public function nextPlayer($skip=false)
    {
        if(!$skip) {
            #echo $this->time().' '. "Следующий игрок \n";
            if (next($this->_players) === false) {
                reset($this->_players);
            }
        }

        $this->_players[(current($this->_players)['pid'])]['timeout']=time()+static::TIME_OUT;

        if(isset($this->_clients[(current($this->_players)['pid'])]->bot))
            $this->_botTimer = rand(5,30)/10; // 0.1;
        else
            $this->_botTimer = 0;

        return $this;
    }

    public function currentPlayer()
    {
        return current($this->_players);
    }

    public function checkWinner()
    {
        #echo $this->time().' '. "Проверка победителя \n";
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
                #echo $this->time().' '. "Победитель #".current($winner)['player']['pid']."\n";
                $this->updatePlayer(array('result'=>-1));
                $this->updatePlayer(array('result'=>2),current($winner)['player']['pid']);
                $this->_isOver=1;
                $this->_botReplay=0;
                return current($winner)['player'];
            } else {
                #echo $this->time().' '. "Экстра время \n";
                $this->setCallback(array('extra' => 1));
                $this->updatePlayer(array('moves' => 1));
            }

        }
        #echo $this->time().' '. "Победителя нет \n";

    }

    public function updatePlayer($data,$id=null)
    {
        $currentPlayer=$this->currentPlayer();
        #echo $this->time().' '. "Обновление данных\n";
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
                        #echo $this->time().' '. "Удаление из игроков {$value}\n";
                    }
                }
            }
        }

        if(!$id){
            reset($players);
            while(each($players)==$currentPlayer){}
                #echo $this->time().' '. "\n".(1)."\n";//print_r($this->currentPlayer());
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
                'moves' => static::GAME_MOVES,
                'points' => 0,
                'avatar' => $client->avatar,
                'name' => $client->name,
                'timeout' => time()+static::TIME_OUT
            );
        }

        #echo $this->time().' '. "Инициализация игроков";
        return $this;
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

    public function setCallback($callback,$playerId=null)
    {
        foreach($callback as $key=>$value)
            if(!$playerId)
                $this->_callback[$key] = $value;
            else
                $this->_callback[$playerId][$key] = $value;

        return $this;
    }

    public function getCallback()
    {
        return $this->_callback;
    }

    public function setResponse($clients)
    {
        is_array($clients) ? $this->_response=$clients : $this->_response=array($clients);
        return $this;
    }

    public function getResponse()
    {
        return $this->_response;
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

    public function setField($field, $playerId=null)
    {
        if($playerId)
            $this->_field[$playerId] = $field;
        else
            $this->_field = $field;
        return $this;
    }

    public function getField()
    {
        return $this->_field;
    }

    public function generateField() {

        $numbers = range(1, static::FIELD_SIZE_X*static::FIELD_SIZE_Y);
        shuffle($numbers);

        for ($i = 1; $i <= static::FIELD_SIZE_X ; ++$i) {
            for ($j = 1; $j <= static::FIELD_SIZE_Y; ++$j) {
                $gameField[$i][$j]['points'] = $numbers[(($i-1)*self::FIELD_SIZE_Y+$j)-1];
                $gameField[$i][$j]['player'] = null;
                $gameField[$i][$j]['coord'] = $i.'x'.$j;
            }
        }
        return $gameField;
    }

    public function time() {
        return ' '.date('H:i:s',time());
    }




}