<?php

Application::import(PATH_APPLICATION . 'model/Game.php');

class Durak extends Game
{

    protected $_cards24 = array(
        '1x9', '1x10', '1x11', '1x12', '1x13', '1x14', // пики  # числа 2-10 + валет, дама, король, туз
        '2x9', '2x10', '2x11', '2x12', '2x13', '2x14', // треф  # числа 2-10 + валет, дама, король, туз
        '3x9', '3x10', '3x11', '3x12', '3x13', '3x14', // буби  # числа 2-10 + валет, дама, король, туз
        '4x9', '4x10', '4x11', '4x12', '4x13', '4x14', // червы # числа 2-10 + валет, дама, король, туз
    );

    protected $_cards36 = array(
        '1x6', '1x7', '1x8', '1x9', '1x10', '1x11', '1x12', '1x13', '1x14', // пики  # числа 2-10 + валет, дама, король, туз
        '2x6', '2x7', '2x8', '2x9', '2x10', '2x11', '2x12', '2x13', '2x14', // треф  # числа 2-10 + валет, дама, король, туз
        '3x6', '3x7', '3x8', '3x9', '3x10', '3x11', '3x12', '3x13', '3x14', // буби  # числа 2-10 + валет, дама, король, туз
        '4x6', '4x7', '4x8', '4x9', '4x10', '4x11', '4x12', '4x13', '4x14', // червы # числа 2-10 + валет, дама, король, туз
    );

    protected $_cards52 = array(
        '1x2', '1x3', '1x4', '1x5', '1x6', '1x7', '1x8', '1x9', '1x10', '1x11', '1x12', '1x13', '1x14', // пики  # числа 2-10 + валет, дама, король, туз
        '2x2', '2x3', '2x4', '2x5', '2x6', '2x7', '2x8', '2x9', '2x10', '2x11', '2x12', '2x13', '2x14', // треф  # числа 2-10 + валет, дама, король, туз
        '3x2', '3x3', '3x4', '3x5', '3x6', '3x7', '3x8', '3x9', '3x10', '3x11', '3x12', '3x13', '3x14', // буби  # числа 2-10 + валет, дама, король, туз
        '4x2', '4x3', '4x4', '4x5', '4x6', '4x7', '4x8', '4x9', '4x10', '4x11', '4x12', '4x13', '4x14', // червы # числа 2-10 + валет, дама, король, туз
    );

    protected $_cards4 = array(
        '1x6', '1x7', '1x8', '1x9', // '1x10', '1x11', '1x12', '1x13', '1x14' // пики  # числа 2-10 + валет, дама, король, туз
    );

    const   CARDS_ON_THE_HANDS = 6;

    // protected $_trump_card = null;

    protected $_trump = null;
    protected $_beater = null; // отбивающийся
    protected $_starter = null; // первая рука

    protected $_gameVariation = array('type'=>'throw', 'cards'=>36);

    public function init()
    {
    }
/*
    public function replayAction($data=null)
    {
        #echo $this->time().' '. "Повтор игры {$this->getUid()} ".(isset($this->getClient()->bot) ?'бот':'игрок')." №{$this->getClient()->id} \n";
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
*/
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
            #echo $this->time().' '. "Удаляем из клиентов игры {$playerId}\n";

            if(in_array($playerId, $this->_bot))
                $this->_bot=array_diff($this->_bot,array($playerId));

            $this->unsetClients($playerId);
            $this->setPlayers($this->getClients(), false)
                ->currentPlayers(array());

            $this->setRun(0)
                ->setOver(0)
                ->setSaved(0);
            $this->startAction();
        }

        #echo $this->time().' '. "Конец выход из игры\n";
    }

    public function startAction($data = null)
    {
        #echo $this->time().' '. "Старт\n";
        $this->unsetCallback();

        if ($this->getNumberPlayers() != count($this->getClients())) {

            $this->setRun(0);
            $this->setLoser(null);
            $this->setPlayers($this->getClients(), false)
                ->currentPlayers(array());
            $this->setResponse($this->getClients());
            $this->setCallback(array(
                'price' => $this->getPrice(),
                'playerNumbers' => $this->getNumberPlayers(),
                'currency' => $this->getCurrency(),
                'app'       => array(
                    'id'   => $this->getId(),
                    'uid'  => $this->getUid(),
                    'key'  => $this->getKey(),
                    'mode' => $this->getCurrency() . '-' . $this->getPrice()
                ),
                'appId' => $this->getUid(),
                'appMode' => $this->getCurrency() . '-' . $this->getPrice(),
                'appName' => $this->getKey(),
                'players' => $this->getPlayers(),
                'current' => $this->currentPlayers(),
                'variation' => $this->getVariation(),
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
                'app'       => array(
                    'id'   => $this->getId(),
                    'uid'  => $this->getUid(),
                    'key'  => $this->getKey(),
                    'mode' => $this->getCurrency() . '-' . $this->getPrice()
                ),
                'appId' => $this->getUid(),
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
                'variation' => $this->getVariation(),
                'beater' => null,
                'action' => 'ready'
            );


            if ($this->getLoser()) {
                #echo "нашли програвшего!!";
                $callback += array(
                    'trump' => $this->getTrump('full'),
                    'winner' => $this->getWinner(),
                    'loser' => $this->getLoser(),
                    'fields' => $this->getField(),
                );

                $this->setField(array());
            }

            $this->setCallback($callback);

        } else {

            if (!$this->getPlayers() || $this->getNumberPlayers() != count($this->getPlayers())) {
                #echo $this->time().' '. "Первичная установка игроков\n";
                $this->setPlayers($this->getClients(), false)
                    ->generateField()// перетасовали и расдали карты, назначили козырь
                    ->currentPlayers(array($this->initStarter()))// текущий = первая рука, определили первую руку
                    ->nextPlayer(true)// добавили таймеры
                    ->setBeater($this->nextPlayer('getBeater'))// установили отбивающегося
                    ->setWinner(array())// обнулили победителя
                    ->setLoser(null)// обнулили победителя
                    ->setTime(time()); // время игры заново
                $this->setRun(1)
                    ->setOver(0)
                    ->setSaved(0);

                $this->setResponse($this->getClients());

            } else {
                $this->setResponse((is_array($data) && isset($data['response']))
                    ? $data['response']
                    : (!is_array($data) || !isset($data['action'])
                        ? $this->getClient()->id
                        : $this->getClients())
                );
            }

            $fields = $this->getField();

            if ($this->getLoser()) {
                #echo "нашли програвшего!!";
                $this->setCallback(array(
                    'winner' => $this->getWinner(),
                    'loser' => $this->getLoser(),
                    'fields' => $fields,
                    'price' => $this->getPrice(),
                    'currency' => $this->getCurrency(),
                    'app'       => array(
                        'id'   => $this->getId(),
                        'uid'  => $this->getUid(),
                        'key'  => $this->getKey(),
                        'mode' => $this->getCurrency() . '-' . $this->getPrice()
                    ),
                    'appId' => $this->getUid(),
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
                            'app'       => array(
                                'id'   => $this->getId(),
                                'uid'  => $this->getUid(),
                                'key'  => $this->getKey(),
                                'mode' => $this->getCurrency() . '-' . $this->getPrice()
                            ),
                            'appId' => $this->getUid(),
                            'appMode' => $this->getCurrency() . '-' . $this->getPrice(),
                            'appName' => $this->getKey(),
                            'action' => (is_array($data) && isset($data['action']))? $data['action'] : 'start',
                            'timeout' => (isset($this->currentPlayer()['timeout']) && $this->currentPlayer()['timeout'] > time()? $this->currentPlayer()['timeout'] : time() + 1) - time(),
                            'timestamp' => (isset($this->currentPlayer()['timeout']) ? $this->currentPlayer()['timeout'] : time()),
                            'beater' => $this->getBeater(),
                            'starter' => $this->getStarter(),
                            'current' => $this->currentPlayers(),
                            'players' => $this->getPlayers(),
                            'fields' => $client->admin ? $this->getField() : array($client->id => $this->getField()[$client->id]) + $fields,
                            'trump' => $this->getTrump('full'),
                            'variation' => $this->getVariation(),
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

            if (isset($this->getClient()->bot)) {
                #echo $this->time() . ' ' . "ход бота\n";
            }

            if(!($cell = $this->generateMove(null, $cell, $table)) && !isset($this->getClient()->bot)){
                $error = 'WRONG_MOVE';
            } elseif (is_array($cell[1])) { // if revert
                $table = $cell[0];
                $cell = $cell[1];
            }

        }


        if ($error) {
            #echo $error;
            $this->setCallback(array('action' => 'error','error' => $error))
                ->setResponse($this->getClient());
        } else {

            // #print_r($cell);
            // #print_r($table);

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
        #echo $this->time().' '. "Проверка ошибок \n";
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

        if ($this->isOver()){ // || !in_array($this->getClient()->id,$this->currentPlayers())) {
            $this->setCallback(array('action' => 'error','error' => 'GAME_IS_OVER'))
                ->setResponse($this->getClient());
        } elseif(count($this->getField('table')) && in_array($this->getClient()->id,$this->currentPlayers())) {

            $playerId = $this->getClient()->id;
            $this->updatePlayer(array('status' => $this->initStatus($playerId)), $playerId);

            $this->doMove();
            $this->startAction(array('action'=>'pass'));
        }
    }

    public function timeoutAction($data = null)
    {

        if((isset($this->currentPlayer()['timeout']) && $this->currentPlayer()['timeout'] <= time())) {

//            echo $this->time() . " время ".time()." истекло, тайм-аут {$this->currentPlayer()['timeout']}\n";
            if ($this->isRun()) {

               $isBeaterCurrent = in_array($this->getBeater(), $this->currentPlayers());
               $isTableEmpty = !count($this->getField('table'));

                foreach ($this->currentPlayers() as $playerId) {

                    //if (!in_array($this->getClient()->id, $this->currentPlayers()) || $this->getPlayers($playerId)['timeout'] > time())
                    if ($this->getPlayers($playerId)['timeout'] > time())
                        continue;

//                    echo " $playerId пропускает ход \n";
                    $this->updatePlayer(array(
                        'status' => $this->initStatus($playerId),
                        'moves'  => (($isTableEmpty && $this->getStarter() == $playerId) || ($isBeaterCurrent && $this->getBeater() == $playerId)) ? -1 : 0
                    ), $playerId);

                    if($this->getPlayers($playerId)['moves'] <= 0 && !$this->getLoser()){
                        $this->setLoser($playerId);
//                        echo " проигравший №$playerId \n";
                    }
                }

                $this->doMove(true);

            } else {

//                echo $this->time() . "не нажали на готов \n";
                foreach ($this->getPlayers() as $player) {
                    if (!isset($player['ready']))
                        $this->unsetClients($player['pid']);
                }
                // $this->startAction(array('action' => 'timeout'));
            }

            $this->startAction(array('action' => 'timeout'));

        } else { // && $this->getNumberPlayers() == count($this->getClients()))

            #echo $this->time() . "время не истекло\n";
            $this->startAction(array('action'=>'timeout','response'=>$this->getClient()->id));
        }

    }

    public function initStatus($playerId){

        /* если отбивающийся, то окончательное "беру" */
        if ($playerId == $this->getBeater())
            $status = 2;

        /* если первая рука, то возможно как первичное "пас", так и окончательное "отбой" */
        elseif ($playerId == $this->getStarter())
            $status = ($this->getNumberPlayers() - count($this->getWinner()) > 2 && !isset($this->getPlayers($playerId)['status']))
                ? 1
                : 2; //(isset($this->getPlayers($playerId)['status']) ? 2 : 1);


        /* для подкидывающих только окончательный "отбой" */
        else
            $status = 2;

        return (isset($this->getPlayers($playerId)['status']) ? $status - $this->getPlayers($playerId)['status'] : $status);
    }

    public function revertMove(){

        #echo $this->time() . ' ' . "#{$this->getClient()->id} переводит ход на {$this->getBeater()}\n";

        $this->currentPlayers(array($this->getBeater())) //установили текущим отбивающегося
            ->setBeater($this->nextBeater()); // установили отбивающимся следующего с картами на руках за текущим игроком

        return $this;
    }

    public function doMove($card=null,$table=null)
    {

        if($card && is_array($card)) {

            list($x, $y) = $card;
            $playerId = $this->getClient()->id;

            echo "Делаем ход $x x $y $table \n";

            if($table && $table=='revert'){
                $this->revertMove();
            }

            $beaterId = $this->getBeater();
            $tables = $table && is_numeric($table) && array_key_exists($table,$this->getField('table')) ? array($table=>$this->getField('table')[$table]): $this->getField('table');

            #echo $this->time() . ' ' . "Делает ход #$playerId \n";
            #print_r(implode('x',$card));

            unset($this->_field[$playerId][array_search($x . 'x' . $y, $this->_field[$playerId])]);

            if ($playerId == $beaterId) {

                foreach ($tables as $key => $table) {
                    if ($this->checkBeat(null, $card, $key)) {
                        #echo " бъемся картой на столе $key \r\n";
                        $this->_field['table'][$key][] = $x . 'x' . $y;
                        break;
                    }
                }
            } else {
                $this->_field['table'][] = array($x . 'x' . $y);
            }

            #print_r($this->_field['table']);

        }


        $currentIds = $this->currentPlayers();

        // если бот пробовал походить, но не нашло возможности и он в текущих
        if ((!$card || !is_array($card)) && isset($this->getClient()->bot) && in_array($this->getClient()->id, $currentIds)){

            // если это заходящий и он еще не пасовал
            if($this->getClient()->id == $this->getStarter() && !isset($this->getPlayers($this->getClient()->id)['status'])){
                $card = true;
            }

            #echo $this->time() . ' ' . "Обновили статус #{$this->getClient()->id} на ".($this->initStatus($this->getClient()->id))."\n";
            $this->updatePlayer(array('status' => $this->initStatus($this->getClient()->id)), $this->getClient()->id);
        }

        $hasUnbeatenCard = (count($this->getField()['table'],COUNT_RECURSIVE) - (count($this->getField()['table']) * 3) != 0);
        $hasTableCards = !empty($this->getField()['table']);
        $hasStarterStatus = isset($this->getPlayers($this->getStarter())['status']);

        /*print_r(array(
            'hasUnbeatenCard' => $hasUnbeatenCard,
            'hasTableCards' => $hasTableCards,
            'hasStarterStatus' => $hasStarterStatus,
            'currentIds' => $currentIds
        ));*/

        // добавляем тех, у кого появилась возможность, удаляем тех, кто спасовал
        foreach ($this->getPlayers() as $player) {

//            echo $this->time() . ' ' . " ============== Проверка #{$player['pid']} ==============\n";

            $isCurrent = in_array($player['pid'], $currentIds);
            $isClient = $this->getClient()->id == $player['pid'];
            $isStatusLast = isset($player['status']) && $player['status'] == 2;
            $isStarter = $player['pid'] == $this->getStarter();
            $isBeater = $player['pid'] == $this->getBeater();
            $isBot = isset($this->getClients($player['pid'])->bot);
            $hasMove = $this->generateMove($player['pid']);
            $hasStatus = isset($player['status']);
            $hasCards = !empty($this->_field[$player['pid']]);
            $willBeCurrent = false;

            if (!$isCurrent && $hasCards && (!$isBeater || $hasUnbeatenCard)) { // еше не в текущих и есть карты
                switch (true) {
                    case !$hasStatus: // нет статуса
                    case !$isStatusLast: // или статус не окончательный
                        switch (true) {
                            case $hasMove: // может походить
                            case !$isBeater: // или не отбивается
                            case $isBeater && $hasUnbeatenCard && $hasTableCards: // или отбивается и есть неотбитая и карты на столе
                                switch (true) {
                                    case $isStarter: // если заходящий положил еще карту
                                    case !$isStarter && $hasStarterStatus:
                                    case $isBeater:
                                        $willBeCurrent = true;
                                        break;
                                }
                                break;
                        }
                        break;
                }
            }

            /*print_r(array(
                'isCurrent' => $isCurrent,
                'isClient' => $isClient,
                'isStarter' => $isStarter,
                'isBeater' => $isBeater,
                'hasMove' => $hasMove,
                'status' => isset($player['status']) ? $player['status'] : 0,
                'timeout' => $player['timeout'],
                'hasCards' => $hasCards,
                'willBeCurrent' => $willBeCurrent,
            ));*/

            switch (true) {

                case $willBeCurrent:

                    $currentIds[] = $player['pid'];
//                    echo $this->time() . ' ' . "Добавляем в текущие #{$player['pid']}\n";

                    if ($isBot && !$card) // если бот, то для переназначения botTimer
                        $card = true;

                    break;

                // если спасовал
                case $hasStatus && ($isStatusLast || !$isStarter) && $isCurrent:

                    unset($currentIds[array_search($player['pid'], $currentIds)]);
//                    echo $this->time() . ' ' . "Пас или таймаут, удаляем из текущих #{$player['pid']}\n";
                    break;

                // если текущий всё отбил
                case $isBeater && $hasTableCards && !$hasUnbeatenCard && $isCurrent:

                    unset($currentIds[array_search($player['pid'], $currentIds)]);
//                    echo $this->time() . ' ' . "Отбился, больше нечего ждать #{$player['pid']}\n";
                    break;

                // если текущий бот и не может отбиться и будет брать
                case $isBot && $isBeater && $hasTableCards && $hasUnbeatenCard && !$hasMove:

                    if (!$hasStatus)
                        $this->updatePlayer(array('status' => 2), $player['pid']);

                    if ($isCurrent)
                        unset($currentIds[array_search($player['pid'], $currentIds)]);

//                    echo $this->time() . ' ' . "Бот не может отбиться, будет брать #{$player['pid']}\n";
                    break;

                default:

//                    echo $this->time() . ' ' . "Ничего не делаем\n";
                    break;
            }

        }


        /*

        $currentIds = array();

        // даем ходить только тем, у кого есть возможные варианты походить
        foreach ($this->getPlayers() as $player) {
            #echo $this->time() . ' ' . "Проверка на возможность походить #{$player['pid']}\n";
            if ((!isset($player['status']) || $player['status'] != 2)
                && ($this->getClient()->id != $player['pid'] || ($this->getClient()->id == $player['pid'] && $card))
                && $this->generateMove($player['pid'])) {
                #echo $this->time() . ' ' . "Добавляем в текущие #{$player['pid']}\n";
                $currentIds[] = $player['pid'];
            }
        }

        */

        $this->currentPlayers(array_values($currentIds));
        // все спасовали или согласились на отбой, больше некому ходить
        #print_r ($this->currentPlayers());

        if(!count($this->currentPlayers())) {

            $this->currentPlayers(array($this->getBeater())); //установили текущим отбивающегося
            $this->nextPlayer(true); // добавили таймер


            if((count($this->_field['table'],COUNT_RECURSIVE)-count($this->_field['table']))/2==count($this->_field['table'])){

                // отбился
//                echo "отбился либо пропуск без карт \r\n";
                if(count($this->_field['table']))
                    $this->_field['off'][] = $this->_field['table'];

            } else {

                //взял
//                echo "взял \r\n";
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

            $currentPlayer = $this->currentPlayers();
            $currentPlayer = reset($currentPlayer);

            while(!count($this->getField($currentPlayer))) {
                #echo "перебираем, пока на руках не будет карт\n";
                $this->nextPlayer();
                $currentPlayer = $this->currentPlayers();
                $currentPlayer = reset($currentPlayer);
            }

            $this->setStarter($currentPlayer) // установили первую руку
                ->setBeater($this->nextBeater()); // установили отбивающимся следующего за первым игроком


        } elseif($card) {

            // совершили ход, добавили всем время на раздумья
            $this->nextPlayer(true);
        }

        $this->checkWinner();

        return $this;
    }

    public function checkDiverseCards()
    {

        #echo "Проверяем карты одной масти на руках";

            foreach($this->getPlayers() as $player) {

                $count = array();

                foreach($this->getField($player['pid']) as $card)
                    isset($count[$card[0]]) ? $count[$card[0]]++ : $count[$card[0]] = 1;

                // если мастей на руках меньше или равно двум и одной из мастей больше 4 либо равно 1
                if(count($count)<=2 && (reset($count)>=5 || reset($count)<=1))
                    return false;

            }

        return true;
    }

    public function shuffleCards()
    {

        $players = $this->getStarter() && !$this->isOver() ? $this->sortPlayers($this->getStarter()) : $this->getPlayers();

        #echo "расдаем карты на руки игрокам:"; #print_r($players);

        if(count($this->getField()['deck'])) {

            $min = min(self::CARDS_ON_THE_HANDS, floor($this->getVariation('cards') / $this->getNumberPlayers()));

            foreach ($players as $player) {

                if (!isset($this->_field[$player['pid']]))
                    $this->_field[$player['pid']] = array();

                if (count($this->getField($player['pid'])) < $min && count($this->getField('deck'))) {
                    $count = 0;
                    while (count($this->getField($player['pid'])) < $min && count($this->getField('deck'))) {
                        $count++;
                        $card = array_shift($this->_field['deck']);
                        $this->_field[$player['pid']][] = $card;
                        $this->_fieldPlayed[array_search($card, $this->_fieldPlayed)] = $player['pid'];
                    }

                    #echo $this->time()." дорасдали $count карт игроку {$player['pid']}\n";

                    $this->sortCards($player['pid']);

                }
            }
        }

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

            // #echo " установили указатель на текущего игрока";
        }

        // один раз перешагнули
        do {
            if (next($this->_players) === false)
                reset($this->_players);

            // #echo " и перешагнули, пока на руках не будет карт \n";
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

        // echo $this->time().' '. "Расдаем карты {$this->getVariation('cards')} \n";
        $cards = $this->{'_cards'.($this->getVariation('cards')?:'36')};

        do {
            //echo $this->time().' '. "Расдаем карты \n";
            shuffle($cards);
            $this->_fieldPlayed = array_fill_keys(array_flip($cards),null);
            $this->_field = array('deck'=>$cards,'table'=>array(),'off'=>array());
            $this->setTrump( count($this->_field['deck']) ? end($this->_field['deck']) : $cards[array_rand($cards)]);
            $this->shuffleCards();

        } while (!$this->checkDiverseCards());

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
        if(($loser = $this->getLoser()) || !count($this->getField()['deck'])){

            #echo $this->time().' '. "Расдаем выигрыши \n";

            $winCoefficient = $loser ? $this->getWinCoefficient() : null;

            foreach($this->getPlayers() as $player) {

                #echo $this->time().' '. "Расдаем выигрыши ".$player['pid']."\n";
                $print = array(
                    $loser,
                    !count($this->getField()[$player['pid']]),
                    !in_array($player['pid'],$this->getWinner()),
                    $player['pid'] != $loser
                    );

                #print_r($print);

                if (($loser || !count($this->getField()[$player['pid']])) && !in_array($player['pid'], $this->getWinner()) && $player['pid'] != $loser) {
                    $this->addWinner($player['pid'])
                        ->updatePlayer(array('result' => 1, 'win' => ($winCoefficient?:$this->getWinCoefficient())), $player['pid']);
                    #print_r($this->getPlayers($player['pid']));
                }

                if (count($this->getWinner()) == count($this->getPlayers()) - 1) {
                    $this->setTime(time());

                    $this->setRun(0)
                        ->setOver(1);
                    $this->_botReplay = array();
                    $this->_botTimer = array();
                    $loser = $loser?:current(array_diff(array_keys($this->getPlayers()),$this->getWinner()));

                    $this->setLoser($loser)
                        ->setStarter(null)
                        ->setBeater(null)
                        ->setTrump(null);

                    $this->updatePlayer(array('result' => -1, 'win' => $this->getPrice() * -1), $loser)
                        ->updatePlayer(array('timeout','status','timeout'=> time() + $this->getOptions('t')))
                        ->currentPlayers(array());

                    #print_r($this->getPlayers($loser));

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

//        echo $this->time().' '. "Отбой: ".(count($this->getField()['off']))."\n";

        if($tables < ((min(self::CARDS_ON_THE_HANDS, floor($this->getVariation('cards') / $this->getNumberPlayers())) ) - (count($this->getField()['off']) ? 0 : 1)) &&
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

//        echo $this->time().' '. "Возможность подкинуть: ".($check?'да':'нет')."\n";
        return $check;

    }

    public function checkBeat($playerId=null, $card=null, $tableId=false){ // $check == playerId OR candidate

        $check = array();
        $cards = is_array($card) ? array(implode('x',$card)) : (is_numeric($playerId) ? $this->getField($playerId) : array());
        $tables = $tableId!==false && isset($this->getField()['table'][$tableId]) ? array($tableId=>$this->getField()['table'][$tableId]) : $this->getField()['table'];

        foreach ($tables as $id=>$table) {
            if (count($table) == 1) {

                #echo $this->time().' '. "Проверяем стол $id \n";

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
                    #echo $this->time().' '. "Возможность отбиться: ".($check?'да':'нет')."\n";
                    return false;
                } elseif($card){
                    #echo $this->time().' '. "Возможность отбиться: ".($check?'да':'нет')."\n";
                    return $check;
                }
                */
            }
        }

        echo $this->time().' '. "Возможность отбиться: ".(count($check)==count(array_filter($check))?'да':'нет')."\n";

        return (count($check) >= ($card ? 1 : count(array_filter($check)))) ? reset($check) : false;

    }

    public function checkRevert($playerId=null, $card=null){ // $check == playerId OR candidate

        if($this->getVariation('type')!='revert')
            return false;

        $check = false;
        $playerId = $playerId ? : $this->getClient()->id;
        $cards = is_array($card) ? array(implode('x',$card)) : (is_numeric($playerId) ? $this->getField($playerId) : array());
        $tables = $this->getField()['table'];
        $count = count($tables);


        if ($playerId == $this->getBeater() && $count == count($tables,COUNT_RECURSIVE) / 2 && $count < count($this->getField($this->nextBeater($this->getBeater())))) {
            foreach ($tables as $id => $table) {

                #echo $this->time() . ' ' . "Проверяем стол $id \n";

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

        #echo $this->time().' '. "Возможность перевести: ".($check?'да':'нет')."\n";

        return $check;

    }

    public function generateMove($playerId=null,$card=null,$table=null)
    {
        if($card){
            #echo $this->time().' '. "Проверка хода\n";
        } else {
            #echo $this->time().' '. "Генерация хода для бота или проверка возможностей текущих игроков\n";
        }

        $check = false;
        $playerId = $playerId ? : $this->getClient()->id;

        // минимальная карта, что бы зайти
        if(!count($this->getField()['table']) && ($playerId == $this->getStarter() || isset($this->getPlayers($this->getStarter())['status']))) {

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
                #echo $this->time().' '. "Возможность первого хода: ".($check?'да':'нет')."\n";
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