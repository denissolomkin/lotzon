<?php

Application::import(PATH_APPLICATION . 'model/Game.php');

class Durak extends Game
{
    protected $_cards = array(
        '1x6', '1x7', '1x8', '1x9', '1x10', '1x11', '1x12', '1x13', '1x14', // пики  # числа 2-10 + валет, дама, король, туз
        '2x6', '2x7', '2x8', '2x9', '2x10', '2x11', '2x12', '2x13', '2x14', // треф  # числа 2-10 + валет, дама, король, туз
        '3x6', '3x7', '3x8', '3x9', '3x10', '3x11', '3x12', '3x13', '3x14', // буби  # числа 2-10 + валет, дама, король, туз
        '4x6', '4x7', '4x8', '4x9', '4x10', '4x11', '4x12', '4x13', '4x14', // червы # числа 2-10 + валет, дама, король, туз
    );

    protected $_cards2 = array(
        '1x6', '1x7', '1x8', '1x9', '1x10', '1x11', '1x12', '1x13', '1x14' // пики  # числа 2-10 + валет, дама, король, туз
    );

    const   CARDS_ON_THE_HANDS = 6;
    const   REVERT_MODE = false;

    protected $_trump = null;
    protected $_trump_card = null;
    protected $_beater = null; // отбивающийся
    protected $_starter = null; // первая рука


    public function init()
    {
    }

    public function replayAction($data=null)
    {
        #echo $this->time().' '. "Повтор игры {$this->getIdentifier()} ".(isset($this->getClient()->bot) ?'бот':'игрок')." №{$this->getClient()->id} \n";
        #echo " REPLAY  \n";

        $clientId = $this->getClient()->id;
        $this->updatePlayer(array('ready' => 1), $clientId );
        $players = $this->getPlayers();

        if(isset($this->getClient()->bot) AND !in_array($clientId,$this->_botReplay)){
            $this->_botReplay[]=$clientId;
        }

        $ready = 0;
        foreach ($players as $player){
            if (isset($player['ready']))// || isset($this->getClients()[$player['pid']]->bot))
                $ready += 1;
        }

        if ($ready == count($players)) {

            $this->unsetPlayers()
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

    public function quitAction($data=null)
    {
        #echo $this->time().' '. "Выход из игры\n";

        if($this->isRun()) {
            $this->startAction($data);
        } else {

            $playerId = $this->getClient()->id;
            $this->unsetCallback();
            $this->setCallback(array(
                'quit' => $playerId,
                'action' => 'quit'
            ));

            $this->setResponse($this->getClients());
            echo $this->time().' '. "Удаляем из клиентов игры {$playerId}\n";

            if(in_array($playerId, $this->_bot))
                $this->_bot=array_diff($this->_bot,array($playerId));

            $this->unsetClients($playerId);
            $this->setPlayers($this->getClients(), false)
                ->currentPlayers(array());

            $this->_isRun = 0;
            $this->_isOver = 0;
            $this->_isSaved = 0;
            $this->startAction();
        }

        #echo $this->time().' '. "Конец выход из игры\n";
    }

    public function startAction($data = null)
    {
        #echo $this->time().' '. "Старт\n";
        $this->unsetCallback();

        if ($this->getNumberPlayers() != count($this->getClients())) {

            $this->_isRun = 0;
            $this->setLoser(null);
            $this->setPlayers($this->getClients(), false);
            $this->setResponse($this->getClients());
            $this->setCallback(array(
                'price' => $this->getPrice(),
                'playerNumbers' => $this->getNumberPlayers(),
                'currency' => $this->getCurrency(),
                'appId' => $this->getIdentifier(),
                'appMode' => $this->getCurrency() . '-' . $this->getPrice(),
                'appName' => $this->getKey(),
                'players' => $this->getPlayers(),
                'current' => $this->currentPlayers(),
                'timeout' => null,
                'action' => 'wait'
            ));

        } elseif(!$this->isRun()) {

            if($this->getNumberPlayers() != count($this->getPlayers()))
                $this->setPlayers($this->getClients(), false)
                    ->currentPlayers(array_keys($this->getPlayers()));
            elseif(!count($this->currentPlayers()))
                $this->currentPlayers(array_keys($this->getPlayers()));

            $this->setResponse($this->getClients());

            $callback = array(
                'appId' => $this->getIdentifier(),
                'appMode' => $this->getCurrency() . '-' . $this->getPrice(),
                'appName' => $this->getKey(),
                'price' => $this->getPrice(),
                'playerNumbers' => $this->getNumberPlayers(),
                'currency' => $this->getCurrency(),
                'players' => $this->getPlayers(),
                'timeout' => (isset($this->currentPlayer()['timeout']) ? $this->currentPlayer()['timeout'] : time() + 1) - time(),
                'timestamp' => (isset($this->currentPlayer()['timeout']) ? $this->currentPlayer()['timeout'] : time()),
                'current' => $this->currentPlayers(),
                'starter' => null,
                'beater' => null,
                'action' => 'ready'
            );

            if ($this->getLoser()) {
                echo "нашли програвшего!!";
                $callback += array(
                    'trump' => $this->getTrump('full'),
                    'winner' => $this->getWinner(),
                    'loser' => $this->getLoser(),
                    'fields' => $this->getField(),
                );
            }

            $this->setCallback($callback);

        } else {

            if (!$this->getPlayers() || $this->getNumberPlayers() != count($this->getPlayers())) {
                echo $this->time().' '. "Первичная установка игроков\n";
                $this->setPlayers($this->getClients(), false)
                    ->generateField()// перетасовали и расдали карты, назначили козырь
                    ->currentPlayers(array($this->initStarter()))// текущий = первая рука, определили первую руку
                    ->nextPlayer(true)// добавили таймеры
                    ->setBeater($this->nextPlayer('getBeater'))// установили отбивающегося
                    ->setWinner(array())// обнулили победителя
                    ->setLoser(null)// обнулили победителя
                    ->setTime(time()); // время игры заново
                $this->_isOver = 0;
                $this->_isRun = 1;
                $this->_isSaved = 0;
            }

            $this->setResponse(isset($data['response']) ? $data['response'] : $this->getClients());

            $fields = $this->getField();

            if ($this->getLoser()) {
                echo "нашли програвшего!!";
                $this->setCallback(array(
                    'winner' => $this->getWinner(),
                    'loser' => $this->getLoser(),
                    'fields' => $fields,
                    'price' => $this->getPrice(),
                    'currency' => $this->getCurrency(),
                    'appId' => $this->getIdentifier(),
                    'appMode' => $this->getCurrency() . '-' . $this->getPrice(),
                    'appName' => $this->getKey(),
                    'beater' => $this->getBeater(),
                    'starter' => $this->getStarter(),
                    'current' => $this->currentPlayers(),
                    'timeout' => (isset($this->currentPlayer()['timeout']) ? $this->currentPlayer()['timeout'] : time() + 1) - time(),
                    'players' => $this->getPlayers(),
                    'trump' => $this->getTrump('full'),
                    'action' => 'move'
                ));
            } else {

                /*
                 * заменяем все хранимые поля-массивы пустыми массивами, за исключением поля "стол"
                 */

                $count_off = 0;
                foreach ($fields as $key => &$field) {
                    if (!in_array($key, array('off', 'table')))
                        $field = array_pad(array(), count($field), null);

                    if ($key == 'off' && count($field)) {
                        foreach ($field as $round)
                            $count_off += count($round, COUNT_RECURSIVE) - count($round);
                        $field = array_pad(array(), $count_off, null);
                    }
                }

                foreach ($this->getClients() as $client) {
                    if (!isset($client->bot)) {

                        $this->setCallback(array(
                            'appId' => $this->getIdentifier(),
                            'appMode' => $this->getCurrency() . '-' . $this->getPrice(),
                            'appName' => $this->getKey(),
                            'action' => $data['action']?:'start',
                            'timeout' => (isset($this->currentPlayer()['timeout']) && $this->currentPlayer()['timeout'] > time()? $this->currentPlayer()['timeout'] : time() + 1) - time(),
                            'timestamp' => (isset($this->currentPlayer()['timeout']) ? $this->currentPlayer()['timeout'] : time()),
                            'beater' => $this->getBeater(),
                            'starter' => $this->getStarter(),
                            'current' => $this->currentPlayers(),
                            'players' => $this->getPlayers(),
                            'fields' => array($client->id => $this->getField()[$client->id]) + $fields,
                            'trump' => $this->getTrump('full'),
                        ), $client->id);
                    }
                }

            }
        }
    }

    public function moveAction($data = null)
    {
        $this->unsetCallback();

        $table = isset($data->table) ? $data->table : null;
        $cell  = isset($data->cell) ? explode('x', $data->cell) : null;
        $error = false;

        if($cell){
            $error = $this->checkError($cell, $table);
        } elseif($this->isOver()){
            $error = 'GAME_IS_OVER';
        }

        if(!$error && (!$cell AND isset($this->getClient()->bot)) OR $cell) {

            if (isset($this->getClient()->bot))
                echo $this->time() . ' ' . "ход бота\n";

            if(!($cell = $this->generateMove(null, $cell, $table)) && !isset($this->getClient()->bot)){
                $error = 'WRONG_MOVE';
            } elseif (is_array($cell[1])) { // if revert
                $table = $cell[0];
                $cell = $cell[1];
            }

        }


        if ($error) {
            echo $error;
            $this->setCallback(array('action' => 'error','error' => $error))
                ->setResponse($this->getClient());
        } else {


            // print_r($cell);
            // print_r($table);

            #echo $this->time().' '. "делаем ход\n";
            $this->doMove($cell, $table);
            $this->startAction(array('action'=>'move'));

            /*
            if ($this->getLoser()){
                $this->setCallback(array('action' => 'move'));
            } else {
                $callbackArray = $this->getCallback();
                foreach ($callbackArray as &$callback)
                    $callback['action']='move';
                $this->setCallback($callbackArray);
            }
            */


        }

            #echo $this->time().' '. "Конец хода \n";

    }

    public function checkError($card, $table=null)
    {
        list($x,$y)=$card;
        echo $this->time().' '. "Проверка ошибок \n";
        if (!$this->isMove()){
            #echo " NOT_YOUR_MOVE\n";
            return 'NOT_YOUR_MOVE';
        } elseif (!$this->isCard($card)){
            #echo " WRONG_CARD\n";
            return 'WRONG_CARD '.$x.'x'.$y;
        //} elseif (($table=='revert' && !$this->checkRevert(null,$card)) || ($table!='revert' && !$this->generateMove(null,$card,$table))){
        //  #echo " WRONG_MOVE\n";
        //  return 'WRONG_MOVE';
        } elseif($this->isOver()) {
            #echo " GAME_IS_OVER\n";
            return 'GAME_IS_OVER';
        }
    }

    public function passAction($data = null)
    {
        $this->unsetCallback();

        if ($this->isOver() || !in_array($this->getClient()->id,$this->currentPlayers())) {
            $this->setCallback(array('action' => 'error','error' => 'GAME_IS_OVER'))
                ->setResponse($this->getClient());
        } else {

            $playerId = $this->getClient()->id;
            $this->updatePlayer(array('status' => $this->initStatus($playerId)), $playerId);

            $this->doMove();
            $this->startAction(array('action'=>'pass'));
        }
    }

    public function timeoutAction($data = null)
    {

        if((isset($this->currentPlayer()['timeout']) && $this->currentPlayer()['timeout']<=time())) {


            echo $this->time() . "время истекло\n";
            if ($this->isRun()) {

                foreach ($this->currentPlayers() as $playerId) {

                    if (!in_array($this->getClient()->id, $this->currentPlayers()) || $this->getPlayers($playerId)['timeout'] > time())
                        continue;

                    $this->updatePlayer(array('status' => $this->initStatus($playerId)), $playerId);
                }

                $this->doMove();

            } else {

                echo $this->time() . "не нажали на готов \n";
                foreach ($this->getPlayers() as $player) {
                    if (!isset($player['ready']))
                        $this->unsetClients($player['pid']);
                }
                $this->startAction(array('action' => 'timeout'));
            }

            $this->startAction(array('action' => 'timeout'));

        } else { // && $this->getNumberPlayers() == count($this->getClients()))

            echo $this->time() . "время не истекло\n";
            $this->startAction(array('action'=>'timeout','response'=>$this->getClient()->id));
        }

    }

    public function initStatus($playerId){

        /* если отбивающийся, то окончательное "беру" */
        if ($playerId == $this->getBeater())
            $status = 2;

        /* если первая рука, то возможно как первичное "пас", так и окончательное "отбой" */
        elseif ($playerId == $this->getStarter())
            $status = $this->getNumberPlayers()-count($this->getWinner()) > 2 && !isset($this->getPlayers($playerId)['status']) ? 1 : 2; //(isset($this->getPlayers($playerId)['status']) ? 2 : 1);


        /* для подкидывающих только окончательный "отбой" */
        else
            $status = 2;

        return (isset($this->getPlayers($playerId)['status']) ? $status - $this->getPlayers($playerId)['status'] : $status);
    }

    public function revertMove(){

        echo $this->time() . ' ' . "#{$this->getClient()->id} переводит ход на {$this->getBeater()}\n";

        $this->currentPlayers(array($this->getBeater())) //установили текущим отбивающегося
            ->setBeater($this->nextBeater()); // установили отбивающимся следующего с картами на руках за текущим игроком

        return $this;
    }

    public function doMove($card=null,$table=null)
    {

        if($card) {

            list($x, $y) = $card;
            $playerId = $this->getClient()->id;

            if($table && $table=='revert'){
                $this->revertMove();
            }

            $beaterId = $this->getBeater();
            $tables = $table && is_numeric($table) && array_key_exists($table,$this->getField('table')) ? array($table=>$this->getField('table')[$table]): $this->getField('table');

            echo $this->time() . ' ' . "Делает ход #$playerId \n";
            print_r(implode('x',$card));

            unset($this->_field[$playerId][array_search($x . 'x' . $y, $this->_field[$playerId])]);

            if ($playerId == $beaterId) {

                foreach ($tables as $key => $table) {
                    if ($this->checkBeat(null, $card, $key)) {
                        echo " бъемся картой на столе $key \r\n";
                        $this->_field['table'][$key][] = $x . 'x' . $y;
                        break;
                    }
                }
            } else {
                $this->_field['table'][] = array($x . 'x' . $y);
            }

            print_r($this->_field['table']);

        }


        $currentIds = $this->currentPlayers();

        // если бот пробовал походить, но не нашло возможности и он в текущих
        if (!$card && isset($this->getClient()->bot) && in_array($this->getClient()->id,$currentIds)){

            // если это заходящий и он еще не пасовал
            if($this->getClient()->id == $this->getStarter() && !isset($this->getPlayers()[$this->getClient()->id]['status'])){
                $card = true;
            }

            echo $this->time() . ' ' . "Обновили статус #{$this->getClient()->id} на ".($this->initStatus($this->getClient()->id))."\n";
            $this->updatePlayer(array('status' => $this->initStatus($this->getClient()->id)), $this->getClient()->id);
        }

        // добавляем тех, у кого появилась возможность, удаляем тех, кто спасовал
        foreach ($this->getPlayers() as $player) {

            echo $this->time() . ' ' . "Проверка на возможность походить #{$player['pid']}\n";
            $hasMove = $this->generateMove($player['pid']);

                // если нет статуса пропуска или пропуск не окончательный AND не текущий игрок или текущий и есть карта AND есть возможность походить и еще не в текущих
                // если игрое не текущий клиент и является либо отбивающимся, либо заходящим, или заходящий уже спасовал
                // и есть карты на руках
                // и есть ход либо не является бъющим либо бъющийся и есть неотбитые карты

            if ((!isset($player['status']) || $player['status'] != 2)
                && (($this->getClient()->id != $player['pid'] && (isset($this->getPlayers()[$this->getStarter()]['status']) || $player['pid']==$this->getStarter() || $player['pid']==$this->getBeater()))
                    || ($this->getClient()->id == $player['pid'] && $card))
                && !in_array($player['pid'],$currentIds)
                && !empty($this->_field[$player['pid']])
                && ($hasMove || ($this->getBeater() != $player['pid']) ||
                    ($this->getBeater() == $player['pid']
                        && !empty($this->getField()['table'])
                        && (count($this->getField()['table'],COUNT_RECURSIVE) - (count($this->getField()['table']) * 3) != 0)))

            ) {
                echo $this->time() . ' ' . "Добавляем в текущие #{$player['pid']}\n";
                $currentIds[] = $player['pid'];

                // если бот, то для переназначения botTimer
                if($this->getClients($player['pid'])->bot && !$card)
                    $card = true;


                // если спасовал
            } else if (isset($player['status'])
                && ($player['status']==2 || $player['pid']!=$this->getStarter())
                && in_array($player['pid'],$currentIds)){
                echo $this->time() . ' ' . "Пас или таймаут, удаляем из текущих #{$player['pid']}\n";
                unset($currentIds[array_search($player['pid'],$currentIds)]);

                // если текущий всё отбил
            } else if (($player['pid'] == $this->getBeater()) && !empty($this->getField()['table'])
                && (count($this->getField()['table'],COUNT_RECURSIVE) - (count($this->getField()['table']) * 3) == 0)
                && in_array($player['pid'],$currentIds)){
                echo $this->time() . ' ' . "Отбился, больше нечего ждать #{$player['pid']}\n";
                unset($currentIds[array_search($player['pid'],$currentIds)]);

                // если текущий бот и не может отбиться и будет брать
            } else if (isset($this->getClients($player['pid'])->bot)
                && ($player['pid'] == $this->getBeater())
                && !empty($this->getField()['table'])
                && (count($this->getField()['table'],COUNT_RECURSIVE) - (count($this->getField()['table']) * 3) != 0)
                && !$hasMove){

                echo $this->time() . ' ' . "Не может отбиться, будет брать #{$player['pid']}\n";

                if(!isset($player['status']))
                    $this->updatePlayer(array('status'=>2),$player['pid']);

                if(in_array($player['pid'],$currentIds))
                    unset($currentIds[array_search($player['pid'],$currentIds)]);

            }
        }


        /*

        $currentIds = array();

        // даем ходить только тем, у кого есть возможные варианты походить
        foreach ($this->getPlayers() as $player) {
            echo $this->time() . ' ' . "Проверка на возможность походить #{$player['pid']}\n";
            if ((!isset($player['status']) || $player['status'] != 2)
                && ($this->getClient()->id != $player['pid'] || ($this->getClient()->id == $player['pid'] && $card))
                && $this->generateMove($player['pid'])) {
                echo $this->time() . ' ' . "Добавляем в текущие #{$player['pid']}\n";
                $currentIds[] = $player['pid'];
            }
        }

        */

        $this->currentPlayers(array_values($currentIds));
        // все спасовали или согласились на отбой, больше некому ходить
        print_r ($this->currentPlayers());

        if(!count($this->currentPlayers())) {

            $this->currentPlayers(array($this->getBeater())); //установили текущим отбивающегося
            $this->nextPlayer(true); // добавили таймер


            if((count($this->_field['table'],COUNT_RECURSIVE)-count($this->_field['table']))/2==count($this->_field['table'])){

                // отбился
                echo "отбился либо пропуск без карт \r\n";
                if(count($this->_field['table']))
                    $this->_field['off'][] = $this->_field['table'];

            } else {

                //взял
                echo "взял \r\n";
                foreach($this->_field['table'] as $table)
                    foreach($table as $card){
                        $this->_field[$this->getBeater()][]=$card;
                        $this->_fieldPlayed[array_search($card,$this->_fieldPlayed)]=$this->getBeater();
                    }

                $this->sortCards($this->getBeater()) //сортируем карты на руках
                    ->nextPlayer(); // отбивающийся пропускает ход
            }



            $this->setField(array(),'table') // обнулили игровой стол
                ->shuffleCards() // дорасдали карты на руки
                ->updatePlayer(array('status'));

            while(!count($this->getField(reset($this->currentPlayers())))) {
                echo "перебираем, пока на руках не будет карт\n";
                $this->nextPlayer();
            }

            $this->setStarter(reset($this->currentPlayers())) // установили первую руку
                ->setBeater($this->nextBeater()); // установили отбивающимся следующего за первым игроком


        } elseif($card) {

            // совершили ход, добавили всем время на раздумья
            $this->nextPlayer(true);
        }

        $this->checkWinner();

        return $this;
    }

    public function sortCards($playerId)
    {
        usort($this->_field[$playerId], function($a, $b){
            $a=explode('x',$a);
            $b=explode('x',$b);
            return (($a[0]==$this->getTrump() && $b[0]!=$this->getTrump()) || ($a[0]!=$this->getTrump() && $b[0]!=$this->getTrump() && $a[0] > $b[0]) || ($a[0] == $b[0] && $a[1] > $b[1])) ? 1 : -1 ;
        });

        return $this;
    }


    public function shuffleCards()
    {

        $players = $this->getStarter() && $this->_isOver == 0 ? $this->sortPlayers($this->getStarter()) : $this->getPlayers();

        echo "расдаем карты на руки игрокам:"; print_r($players);

        if(count($this->getField()['deck']))
            foreach($players as $player) {

                if(!isset($this->_field[$player['pid']]))
                    $this->_field[$player['pid']] = array();

                if(count($this->getField($player['pid'])) < self::CARDS_ON_THE_HANDS && count($this->getField('deck'))) {
                    $count = 0;
                    while (count($this->getField($player['pid'])) < self::CARDS_ON_THE_HANDS && count($this->getField('deck'))) {
                        $count++;
                        $card = array_shift($this->_field['deck']);
                        $this->_field[$player['pid']][] = $card;
                        $this->_fieldPlayed[array_search($card, $this->_fieldPlayed)] = $player['pid'];
                    }

                    echo $this->time()." дорасдали $count карт игроку {$player['pid']}\n";

                    $this->sortCards($player['pid']);

                }
            }

        return $this;
    }

    public function sortPlayers($playerId)
    {
        $players=$this->getPlayers();

        // установили указатель на заходившего игрока
        while(current($players)['pid']!=$playerId)
            if (next($players) === false)
                reset($players);

        // начали сортировать игроков с первого заходившего, отбивающегося, если на него не перевели, пропускаем
        while($player=current($players)){
            unset($players[key($players)]);
            if($player['pid']!=$this->getBeater() || $this->getBeater() == $this->getStarter())
                $sortPlayers[]=array('pid'=>$player['pid']);

            if(!current($players) && count($players))
                reset($players);
        }

        // если на отбивающегося не перевели по кругу, добавляем в конец очереди
        if($this->getBeater()!=$this->getStarter())
            $sortPlayers[]=array('pid'=>$this->getBeater());


        return $sortPlayers;
    }

    public function nextBeater($playerId=null)
    {

        $current = $playerId ? array('pid'=>$playerId) : $this->currentPlayer();

        // установили указатель на первую руку
        while($current && current($this->_players)['pid']!=$current['pid']){
            if (next($this->_players) === false)
                reset($this->_players);

            // echo " установили указатель на текущего игрока";
        }

        // один раз перешагнули
        do {
            if (next($this->_players) === false)
                reset($this->_players);

            // echo " и перешагнули, пока на руках не будет карт \n";
        } while(!count($this->getField(current($this->_players)['pid'])));

        return current($this->_players)['pid'];

    }

    public function initStarter()
    {
        $starter = null;

        foreach($this->getPlayers() as $player) {
            foreach ($this->getField()[$player['pid']] as $candidate) {

                $candidate = explode('x', $candidate);
                /*  1) еще нет
                    2) есть, но не козырь, в отличие от кандидата
                    3) есть, но не козырь или одной масти и больше кандидата */
                if (!$starter
                    OR ($candidate[0] == $this->getTrump() && $starter[0] != $this->getTrump())
                    OR ($starter[1] > $candidate[1] && ($starter[0] != $this->getTrump() || $starter[0] == $candidate[0]))) {
                    $starter = $candidate;
                    $starter['pid'] = $player['pid'];
                }

            }
        }

        $this->setStarter($starter['pid']);

        return $starter['pid'];

    }

    public function generateField()
    {

        shuffle($this->_cards);
        $this->_field = array('deck'=>$this->_cards,'table'=>array(),'off'=>array());
        $this->_fieldPlayed = array_fill_keys(array_flip($this->_cards),null);

        $this->setTrump(count($this->_field['deck']) ? end($this->_field['deck']): $this->_cards[array_rand($this->_cards)]);
        $this->shuffleCards();

        return $this;
    }

    public function isCard($card)
    {
        return (array_search(implode('x',$card), $this->_field[$this->getClient()->id])!==false);
    }

    public function isMove()
    {
        return (in_array($this->getClient()->id,$this->currentPlayers()));
    }

    public function getTrump($full=false)
    {
        if($full)
            return $this->_trump;
        else
            return $this->_trump[0];
    }

    public function setTrump($trump)
    {
        $this->_trump = $trump;
        return $this;
    }

    public function getBeater()
    {
        return $this->_beater;
    }

    public function setBeater($beater)
    {
        $this->_beater = $beater;
        return $this;
    }

    public function getStarter()
    {
        return $this->_starter;
    }

    public function setStarter($starter)
    {
        $this->_starter = $starter;
        return $this;
    }

    public function checkWinner()
    {
        if(!count($this->getField()['deck'])){
            foreach($this->getPlayers() as $player){

                if(!count($this->getField()[$player['pid']]) && !in_array($player['pid'],$this->getWinner())) {
                    $this->addWinner($player['pid'])
                        ->updatePlayer(array('result' => 1, 'win' => $this->getPrice()*$this->getWinCoefficient()), $player['pid']);
                    print_r($this->getPlayers($player['pid']));
                }

                if(count($this->getWinner())==count($this->getPlayers())-1){
                    $this->setTime(time());
                    $this->_isOver = 1;
                    $this->_isRun = 0;
                    $this->_botReplay   = array();
                    $this->_botTimer    = array();
                    $loser = current(array_diff(array_keys($this->getPlayers()),$this->getWinner()));

                    $this->setLoser($loser)
                        ->setStarter(null)
                        ->setBeater(null)
                        ->setTrump(null);

                    $this->updatePlayer(array('result' => -1, 'win' => $this->getPrice()*-1), $loser)
                        ->updatePlayer(array('status'))
                        ->currentPlayers(array());

                    print_r($this->getPlayers($loser));

                    return $this;
                }
            }
        }


        return $this;
    }


    public function checkThrow($playerId=null,$card=null){


        $check = false;
        $cards = is_array($card) ? array(implode('x',$card)) : (is_numeric($playerId) ? $this->getField()[$playerId] : array());
        $tables = count($this->getField()['table']);

        if($tables < self::CARDS_ON_THE_HANDS &&
            ((count($this->getField()['table'],COUNT_RECURSIVE) - $tables + count($this->getField()[$this->getBeater()])) / 2 > $tables)) {
            foreach ($this->getField()['table'] as $table) {
                foreach ($table as $waiting) {

                    $waiting = explode('x', $waiting);

                    foreach ($cards as $candidate) {
                        $candidate = explode('x', $candidate);
                        if ($candidate[1] == $waiting[1])
                            $check = $candidate;
                    }

                }
            }
        }

        echo $this->time().' '. "Возможность подкинуть: ".($check?'да':'нет')."\n";
        return $check;

    }

    public function checkBeat($playerId=null, $card=null, $tableId=false){ // $check == playerId OR candidate

        $check = array();
        $cards = is_array($card) ? array(implode('x',$card)) : (is_numeric($playerId) ? $this->getField($playerId) : array());
        $tables = $tableId!==false && isset($this->getField()['table'][$tableId]) ? array($tableId=>$this->getField()['table'][$tableId]) : $this->getField()['table'];

        foreach ($tables as $id=>$table) {
            if (count($table) == 1) {

                echo $this->time().' '. "Проверяем стол $id \n";

                $check[$id]=null;
                $waiting = reset($table);
                $waiting = explode('x', $waiting);

                /*
                 * 1) решить проблему!!! генерирует карту для последнего стола, и не всегда пропускает на первой стол
                 * 2) мне выпадает вторая карта (всегда козырная), которую не могу побить, но не выдает ошибку, только после первого отбития
                */

                foreach ($cards as $candidate) {
                    $candidate = explode('x', $candidate);

                    if ( ($candidate[0] == $waiting[0] && $candidate[1] > $waiting[1]) || ($candidate[0] == $this->getTrump() && $waiting[0] != $this->getTrump())) {
                        if (!$check[$id] || ($candidate[0] != $this->getTrump() && ($check[$id][0] == $this->getTrump() || $check[$id][1] > $candidate[1])))
                            $check[$id] = $candidate;
                    }
                }

                /*
                // вторая проблема
                if(!$check){
                    echo $this->time().' '. "Возможность отбиться: ".($check?'да':'нет')."\n";
                    return false;
                } elseif($card){
                    echo $this->time().' '. "Возможность отбиться: ".($check?'да':'нет')."\n";
                    return $check;
                }
                */
            }
        }

        echo $this->time().' '. "Возможность отбиться: ".(count($check)==count(array_filter($check))?'да':'нет')."\n";

        return (count($check) >= ($card ? 1 : count(array_filter($check)))) ? reset($check) : false;

    }

    public function checkRevert($playerId=null, $card=null){ // $check == playerId OR candidate

        if(!self::REVERT_MODE) return false;

        $check = false;
        $playerId = $playerId ? : $this->getClient()->id;
        $cards = is_array($card) ? array(implode('x',$card)) : (is_numeric($playerId) ? $this->getField($playerId) : array());
        $tables = $this->getField()['table'];
        $count = count($tables);


        if ($playerId == $this->getBeater() && $count == count($tables,COUNT_RECURSIVE) / 2 && $count < count($this->getField($this->nextBeater($this->getBeater())))) {
            foreach ($tables as $id => $table) {

                echo $this->time() . ' ' . "Проверяем стол $id \n";

                $waiting = reset($table);
                $waiting = explode('x', $waiting);

                foreach ($cards as $candidate) {
                    $candidate = explode('x', $candidate);

                    if ($candidate[1] == $waiting[1]) {
                        $check = array('revert',$candidate);
                        break 2;
                    }
                }

                break;

            }
        }

        echo $this->time().' '. "Возможность перевести: ".($check?'да':'нет')."\n";

        return $check;

    }

    public function generateMove($playerId=null,$card=null,$table=null)
    {
        if($card)
            echo $this->time().' '. "Проверка хода\n";
        else
            echo $this->time().' '. "Генерация хода для бота или проверка возможностей текущих игроков\n";

        $check = false;
        $playerId = $playerId ? : $this->getClient()->id;

        // минимальная карта, что бы зайти
        if(!count($this->getField()['table']) && $playerId == $this->getStarter()) {

            if(!$card){
                foreach ($this->getField()[$playerId] as $candidate) {
                    $candidate = explode('x', $candidate);
                    /*  1) еще нет
                        2) есть, но козырная, нашли некозырную
                        3) есть, но нашли такой же масти или не козырную меньше номиналом */
                    if (!$check
                        || ($check[0] == $this->getTrump() && $candidate[0] != $this->getTrump())
                        || (($candidate[0] == $check[0] || $candidate[0] != $this->getTrump()) && $check[1] > $candidate[1]))
                        $check = $candidate;
                }
                echo $this->time().' '. "Возможность первого хода: ".($check?'да':'нет')."\n";
            } else
                $check = $card;

        // пробуем подкинуть
        } elseif($playerId != $this->getBeater()) {
            $check = $this->checkThrow($playerId, $card);

        // пробуем либо перевести, либо отбиться
        } elseif ($playerId == $this->getBeater() && isset($this->getField()['table']) && count($this->getField()['table'])) {
            $revert = $table === null || $table=='revert' ? $this->checkRevert($playerId, $card) : false;
            $check = $revert ? : ($table === null || $table!='revert' ? $this->checkBeat($playerId, $card, $table) : false);
        }

        return $check;
    }
}