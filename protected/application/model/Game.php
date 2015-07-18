<?php

class Game
{
    protected $_gameId = 0;
    protected $_gameKey = '';
    protected $_gameModes = array();
    protected $_gameTitle = array();
    protected $_gameOptions = array();
    protected $_gameVariation = array();

    private $_gameCurrency = '';
    private $_gamePrice = null;
    private $_numberPlayers = null;
    private $_gameTime = null;
    private $_gameIdentifier = '';

    public  $_bot = array();
    public  $_botTimer = array();
    public  $_botReplay = array();

    public  $_isOver = 0;
    public  $_isRun = 0;
    public  $_isSaved = 0;

    protected $_players = array();
    private $_clients = array();
    private $_client = '';
    private $_callback = array();
    private $_response = '';
    private $_winner = null;
    private $_loser = null;
    private $_current = array();

    protected $_field = array();
    protected $_fieldPlayed = array();

    public function __construct($game) {

        $this->setId($game->getId())
            ->setKey($game->getKey())
            ->setOptions($game->getOptions())
            ->setTitle($game->getTitle())
            ->setModes($game->getModes())
            ->setIdentifier(uniqid())
            ->setTime(time())
            ->init();
    }

    public function init()
    {
        $this->setField($this->generateField());
    }

    public function quitAction($data=null)
    {
        #echo $this->time().' '. "Выход из игры\n";

        if(!$this->_isOver) {
            $this->startAction($data);
        } else {
            $playerId = $this->getClient()->id;
            $this->unsetCallback();
            $this->setCallback(array(
                'quit' => $playerId,
                'action' => 'quit'
            ));


            $this->setResponse($this->getClients());
            #echo $this->time().' '. "Удаляем из клиентов игры {$playerId}\n";
            $this->unsetClients($playerId)
                ->setPlayers($this->getClients());
        }

        #echo $this->time().' '. "Конец выход из игры\n";
    }

    public function surrenderAction($data=null)
    {
        #echo $this->time().' '. "Сдаться\n";

        $playerId = $this->getClient()->id;

        $this->updatePlayer(array('result' => 1))
            ->updatePlayer(array('result' => -2), $playerId)
            ->unsetCallback();
        $this->_isOver = 1;
        $this->_isRun = 0;
        $this->_isSaved = 0;

        if (count($this->getPlayers()) > 1)
            while ($this->currentPlayer()['pid'] == $playerId)
                $this->nextPlayer();

        $this->setWinner($this->currentPlayer()['pid']);

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


    public function readyAction($data=null)
    {
        #echo $this->time().' '. "Повтор игры {$this->getIdentifier()} ".(isset($this->getClient()->bot) ?'бот':'игрок')." №{$this->getClient()->id} \n";
        #echo " REPLAY  \n";

        if($this->_isRun == 1){
            $this->startAction();
        } else {
            $playerId = $this->getClient()->id;
            $this->updatePlayer(array('ready' => 1), $playerId);
            $players = $this->getPlayers();

            $ready = array();
            foreach ($players as $player) {
                if (isset($player['ready']) || isset($this->getClients($player['pid'])->bot))
                    $ready[$player['pid']] = $player;
            }

            if (count($ready) == count($players)) {

                $this->_isSaved = 0;
                $this->_isRun = 1;
                $this->unsetPlayers()
                    ->startAction();

            } else {

                $this->_isOver = 0;
                $this->currentPlayers(array_values(array_diff($this->currentPlayers(), array($playerId))));
                $this->startAction();
                /*
                $this->unsetCallback()
                    ->setResponse($this->getClient())
                    ->setCallback(array(
                        'action' => 'ready',
                        'ready'  => $ready
                    ));
                */
            }
        }

        #echo $this->time().' '. "Конец повтора игры\n";
    }

    public function replayAction($data=null)
    {
        #echo $this->time().' '. "Повтор игры {$this->getIdentifier()} ".(isset($this->getClient()->bot) ?'бот':'игрок')." №{$this->getClient()->id} \n";
        #echo " REPLAY  \n";

        $clientId = $this->getClient()->id;
        $this->updatePlayer(array('reply' => 1), $clientId );
        $players = $this->getPlayers();

        if(isset($this->getClient()->bot) AND !in_array($clientId,$this->_botReplay)){
            $this->_botReplay[]=$clientId;
        }

        $reply = 0;
        foreach ($players as $player){
            if (isset($player['reply']))// || isset($this->getClients()[$player['pid']]->bot))
                $reply += 1;
        }

        if ($reply == count($players)) {
            $this->_isSaved = 0;
            $this->_isOver = 0;
            $this->_isRun = 1;
            #echo $this->time().' '. "Переустановка игроков\n";

            $this->unsetFieldPlayed()
                ->setField($this->generateField())
                ->setPlayers($this->getClients())
                ->nextPlayer()
                ->setWinner(null)
                ->setTime(time())
                ->startAction();
        } else {
            $this->unsetCallback()
                ->setResponse($this->getClient())
                ->setCallback(array(
                    'action' => 'reply',
                    'reply' => $reply
                ));
        }

        #echo $this->time().' '. "Конец повтора игры\n";
    }

    public function startAction($data=null)
    {
        #echo $this->time(0).' '. "Старт\n";
        $this->unsetCallback();

        if(!$this->getPlayers()) {
            #echo $this->time().' '. "Первичная установка игроков\n";
            $this->setPlayers($this->getClients());
            $this->nextPlayer();
            $this->setWinner(null);
            $this->_isOver = 0;
            $this->_isSaved = 0;
            $this->_isRun = 1;
        }

        if($this->getWinner())
            $this->setCallback(array(
                'winner' => $this->getWinner(),
                'price' => $this->getPrice(),
                'currency' => $this->getCurrency()
            ));

        $this->setCallback(array(
            'current'   => $this->currentPlayer()['pid'],
            'timeout'   => $this->currentPlayer()['timeout']-time(),
            'appId'       => $this->getIdentifier(),
            'appMode' => $this->getCurrency().'-'.$this->getPrice(),
            'appName' => $this->getKey(),
            'players'   => $this->getPlayers(),
            'field'     => $this->getFieldPlayed(),
            'action'    => 'start'
        ));

        $this->updatePlayer( array('reply','ready','result') );
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
            #echo $this->time().' '. "делаем ход\n";
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
        if(!$this->isOver() AND isset($this->currentPlayer()['timeout']) AND $this->currentPlayer()['timeout']<=time()) {

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
            #echo " NOT_YOUR_MOVE\n";
            return 'NOT_YOUR_MOVE';
        } elseif (!$this->isCell($cell)){
            #echo " WRONG_CELL\n";
            return 'WRONG_CELL '.$x.'x'.$y;
        } elseif ($this->isClicked($cell)){
            #echo " CELL_IS_PLAYED\n";
            return 'CELL_IS_PLAYED';
        } elseif($this->isOver()) {
            #echo " GAME_IS_OVER\n";
            return 'GAME_IS_OVER';
        }
    }

    public function isCell($cell)
    {
        list($x,$y)=$cell;
        return ($x>0 && $y>0 && $x<= $this->getOption('x') && $y<= $this->getOption('y'));
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

    public function isRun()
    {
        return $this->_isRun;
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
            'moves' => -1), $current['pid']);

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

        $this->nextPlayer();
        return $this;
    }

    public function generateMove()
    {

        //$minimum=( rand(1,2) < 2 ? ($this->FIELD_SIZE_X*$this->FIELD_SIZE_Y)-($this->GAME_MOVES*($this->GAME_PLAYERS+1))):0);
        //$minimum=( rand(1,5) < 2 ? ($this->FIELD_SIZE_X*$this->FIELD_SIZE_Y/2):0 );
        //$minimum=0;

        if($this->getMode()>0)
            $minimum=(!rand(0,$this->getMode()-1) ? ($this->getOption('x') * $this->getOption('y')) - ($this->getOption('m') * ($this->getOption('p') + 1)) : 0);
        else
            $minimum=0;

        #echo $this->time().' '. "Генерация поля для бота\n";
        do {
            do {
                #echo "Ход бота\n";
                $x=rand(1,$this->getOption('x'));
                $y=rand(1,$this->getOption('y'));
            } while($this->_field[$x][$y]['player']);
        } while($this->_field[$x][$y]['points']<$minimum);

        #echo $this->time().' '. "Ход бота $x, $y = $minimum ".$this->_field[$x][$y]['points']."\n";
        return array($x, $y);
    }

    public function nextPlayer($skip=false)
    {

        if($skip!==true) {

            while($this->currentPlayer() && current($this->_players)['pid']!=$this->currentPlayer()['pid'])
                if (next($this->_players) === false)
                    reset($this->_players);

            #echo $this->time().' '. "Следующий игрок \n";
            if (next($this->_players) === false)
                reset($this->_players);

            if($skip===false) {
                $this->currentPlayers(array(current($this->_players)['pid']));
            } else {
                return current($this->_players)['pid'];
            }
        }

        $this->_botTimer = array();

        foreach($this->currentPlayers() as $playerId){
            $this->_players[$playerId]['timeout']=time()+$this->getOption('t');
            if(isset($this->_clients[$playerId]->bot))
                $this->_botTimer[$playerId] = rand(8,30)/10; // 0.1;
        }

        return $this;
    }

    public function currentPlayer()
    {
        return count($this->currentPlayers()) ? $this->_players[reset($this->currentPlayers())] : false;
        /* return current($this->_players); */
    }

    public function currentPlayers($playerIds=false)
    {
        if($playerIds!==false){
            $this->_current = $playerIds;
            return $this;
        } else
            return $this->_current;
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
                $this->setWinner(current($winner)['player']['pid']);
                $this->updatePlayer(array(
                    'result' => -1,
                    'win' => $this->getPrice()*-1));
                $this->updatePlayer(array(
                    'result' => 2,
                    'win' => $this->getPrice()+$this->getWinCoefficient()),current($winner)['player']['pid']);
                $this->setTime(time());
                $this->_isOver = 1;
                $this->_isRun = 0;
                $this->_botReplay   = array();
                $this->_botTimer    = array();
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
            #echo $this->time().' '. "Возврат указателя в массиве игроков \n";//print_r($this->currentPlayer());
        }


        return $this;
    }

    public function getPlayers($id=false)
    {
        if($id){
            if(isset($this->_players))
                return $this->_players[$id];
            else
                return false;
        } else
            return $this->_players;

    }

    public function unsetPlayers()
    {
        $this->_players = array();
        return $this;
    }

    public function setPlayers($clients, $rand=true)
    {
        if($rand)
            rand(0,1)?arsort($clients):asort($clients);

        $order = 0;

        $this->unsetPlayers();

        if(!empty($clients))
            foreach ($clients as $id => $client) {

                if ($this->getNumberPlayers() > 2) ;
                $order++;

                if (isset($client->bot))
                    $this->_bot[$id] = $id;

                $this->_players[$id] = array(
                    'pid'       => $id,
                    'moves'     => $this->getOption('m'),
                    'points'    => 0,
                    'avatar'    => $client->avatar,
                    'lang'      => isset($client->lang) ? $client->lang : 'RU',
                    'name'      => $client->name,
                    'timeout'   => time() + $this->getOption('t')
                );

                if ($order)
                    $this->_players[$id]['order'] = $order;
            }

        #echo $this->time().' '. "Инициализация игроков\n";
        return $this;
    }

    public function setClient($client)
    {
        $this->_client=$this->getClients($client);

        return $this;
    }

    public function getClient()
    {
            return $this->_client;
    }

    public function getClients($id=null)
    {
        if($id)
            return isset($this->_clients[$id])?$this->_clients[$id]:false;
        else
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

    public function addClient($clients)
    {
        foreach($clients as $id => $client)
            $this->_clients[$id]=$client;

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
        $this->_gameIdentifier = $identifier;

        return $this;
    }

    public function getIdentifier()
    {
        return $this->_gameIdentifier;
    }

    public function setId($id)
    {
        $this->_gameId = $id;
        return $this;
    }

    public function getId()
    {
        return $this->_gameId;
    }

    public function setTitle($array)
    {
        $this->_gameTitle = $array;
        return $this;
    }

    public function getTitle($lang=null)
    {

        if(isset($lang)) {
            if(isset($this->_gameTitle[$lang]) && $this->_gameTitle[$lang] && $this->_gameTitle[$lang]!='')
                $title = $this->_gameTitle[$lang];
            else
                $title = reset($this->_gameTitle);
        } else
            $title = $this->_gameTitle;

        return $title;
    }


    public function setModes($array)
    {
        $this->_gameModes = $array;
        return $this;
    }

    public function getMode()
    {
        return isset($this->_gameModes[$this->_gameCurrency]) && isset($this->_gameModes[$this->_gameCurrency][$this->_gamePrice])
            ? $this->_gameModes[$this->_gameCurrency][$this->_gamePrice] : false;
    }

    public function setOptions($array)
    {
        $this->_gameOptions = $array;
        return $this;
    }

    public function getOption($key)
    {
        return isset($this->_gameOptions[$key]) ? $this->_gameOptions[$key] : false;
    }

    public function setVariation($variation)
    {
        $this->_gameVariation = $variation;

        return $this;
    }

    public function getVariation($key=null)
    {
        return isset($key) ? (isset($this->_gameVariation[$key]) ? $this->_gameVariation[$key] : false) : $this->_gameVariation;
    }

    public function setKey($key)
    {
        $this->_gameKey = $key;
        return $this;
    }

    public function getKey()
    {
        return $this->_gameKey;
    }


    public function getWinCoefficient()
    {
        switch (count($this->getPlayers())) {

            default:
            case 2:
                $coef = 1;
                break;

            case 3:

                switch (count($this->getWinner())) {

                    case 1:
                        $coef = !$this->getLoser() ? 0.75 : 0.25;
                        break;

                    case 2:
                        $coef = 0.25;
                        break;

                    default:
                        $coef = !$this->getLoser() ? 0 : 1 / 2;
                        break;
                }

                break;

            case 4:

                switch (count($this->getWinner())) {

                    case 1:
                        $coef = !$this->getLoser() ? 0.75 : 0.25 / 2;
                        break;

                    case 2:
                        $coef = !$this->getLoser() ? 0.2 : 0.05;
                        break;

                    case 3:
                        $coef = 0.05;
                        break;

                    default:
                        $coef = !$this->getLoser() ? 0 : 1 / 3;
                        break;
                }

                break;
        }


        $coef *= ($this->getOption('h') ? (100 - $this->getOption('h')) / 100 : 1) * $this->getPrice();

        if($coef>0)
            $coef = $this->getCurrency()=='MONEY' ? floor($coef * 100) / 100 : floor($coef);


        return $coef;
    }

    public function setWinner($key)
    {
        $this->_winner = $key;
        return $this;
    }

    public function addWinner($key)
    {
        $this->_winner[] = $key;
        return $this;
    }

    public function getWinner()
    {
        return $this->_winner;
    }

    public function setLoser($key)
    {
        $this->_loser = $key;
        return $this;
    }

    public function getLoser()
    {
        return $this->_loser;
    }

    public function setNumberPlayers($number)
    {
        $this->_numberPlayers = $number;
        return $this;
    }

    public function getNumberPlayers()
    {
        return $this->_numberPlayers;
    }

    public function setPrice($price)
    {
        $this->_gamePrice = $price;
        return $this;
    }

    public function getPrice()
    {
        return $this->_gamePrice;
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
        $this->_response = is_array($clients) ? $clients : array($clients);
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

    public function setField($field, $key=null)
    {
        if($key)
            $this->_field[$key] = $field;
        else
            $this->_field = $field;
        return $this;
    }

    public function getField($key=false)
    {
        if($key){
            if(isset($this->_field[$key]))
                return $this->_field[$key];
            else
                return null;
        } else
            return $this->_field;
    }

    public function generateField() {

        $numbers = range(1, $this->getOption('x')*$this->getOption('y'));
        shuffle($numbers);

        for ($i = 1; $i <= $this->getOption('x') ; ++$i) {
            for ($j = 1; $j <= $this->getOption('y'); ++$j) {
                $gameField[$i][$j]['points'] = $numbers[(($i-1)*$this->getOption('y')+$j)-1];
                $gameField[$i][$j]['player'] = null;
                $gameField[$i][$j]['coord'] = $i.'x'.$j;
            }
        }
        return $gameField;
    }

    public function time($space=true) {
        return ($space?' ':'').date('H:i:s',time());
    }




}
