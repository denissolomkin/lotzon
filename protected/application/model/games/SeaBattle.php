<?php

Application::import(PATH_APPLICATION . 'model/Game.php');

class SeaBattle extends Game
{
    const   START_TIME_OUT = 90;

    protected $_matrix = array(
        array(-1, -1), array(-1, 0), array(-1, 1),
        array(0, -1),  array(0, 0),  array(0, 1),
        array(1, -1),  array(1, 0),  array(1, 1),
    );
    protected $_hit_matrix = array(
                    array(-1, 0),
        array(0, -1),               array(0, 1),
                    array(1, 0),
    );

    protected $_ships2x4 = array(
        1);

    protected $_ships11x22 = array(
        5,
        4, 4,
        3, 3, 3,
        2, 2, 2, 2,
        1, 1, 1, 1, 1,
    );

    protected $_shipsDefault = array(
        4,
        3, 3,
        2, 2, 2,
        1, 1, 1, 1,
    );

    protected $_playerShips = array();
    protected $_destroy = array();
    protected $_gameVariation = array('field'=>'11x22');
    
    public function init() {}

    public function startAction($data = null)
    {
        #echo $this->time().' '. "Старт\n";
        $this->unsetCallback();

        if (!$this->getPlayers()) {
            #echo $this->time().' '. "Первичная установка игроков\n";
            $this->setPlayers($this->getClients());
            $this->updatePlayer(array(
                'timeout' => static::START_TIME_OUT - $this->getOptions('t'),
                'ships' => array_count_values($this->getShips())
            ));
            $this->nextPlayer('init');
            $this->setWinner(null);

            $this->setRun(1)
                ->setOver(0)
                ->setSaved(0);
        }

        if (count($this->getField()) != count($this->getClients())) {
            foreach ($this->_bot as $bot) {
                if (!isset($this->getField()[$bot])) {
                    #echo $this->time().' '. "Генерация поля для бота \n";
                    $this->setField($this->generateField($bot), $bot);
                    #if(count($this->getField()==count($this->getClients())))
                    #$this->nextPlayer();
                }
            }
        }

        if (count($this->getField()) == count($this->getClients())) {

            #echo $this->time().' '. "Количество полей совпадает с игроками \n";
            $this->setResponse($this->getClients());
            foreach ($this->getClients() as $client) {
                if (!isset($client->bot)) {

                    $fields=array();

                    $fields[$client->id] = $this->getField()[$client->id];
                    if(isset($this->getFieldPlayed()[$client->id]))
                        foreach($this->getFieldPlayed()[$client->id] as $x=>$cell)
                            foreach($cell as $y=>$v){
                                $fields[$client->id][$x][$y] = $v;
                            }

                    if (count($this->getFieldPlayed()))
                        foreach ($this->getFieldPlayed() as $id => $field)
                            if ($id != $client->id)
                                $fields[$id] = $field;

                    if($this->getWinner())
                        $this->setCallback(array(
                            'winner' => $this->getWinner(),
                            'fields' => $this->getField(),
                            'price' => $this->getPrice(),
                            'currency' => $this->getCurrency()
                        ), $client->id);

                    $this->setCallback(array(
                        'current' => $this->currentPlayer()['pid'],
                        'timeout' => (isset($this->currentPlayer()['timeout']) ? $this->currentPlayer()['timeout'] - time() : 1),//($this->currentPlayer()['timeout']-time()>0?$this->currentPlayer()['timeout']-time():1),
                        'app'       => array(
                            'id'   => $this->getId(),
                            'uid'  => $this->getUid(),
                            'key'  => $this->getKey(),
                            'mode' => $this->getCurrency() . '-' . $this->getPrice()
                        ),
                        'appId' => $this->getUid(),
                        'appMode' => $this->getCurrency().'-'.$this->getPrice(),
                        'appName' => $this->getKey(),
                        'players' => $this->getPlayers(),
                        'fields' => $fields,//$this->getField(),// $fields,
                        'variation' => $this->getVariation(),
                        'action' => 'start'
                    ), $client->id);
                }
            }

            $this->updatePlayer(array('ready', 'result'));

        } elseif(isset($this->getField()[$this->getClient()->id]) && !isset($this->getClient()->bot)){

            #echo $this->time().' '. "Ждем заполнения полей остальными игроками \n";
            $this->setResponse($this->getClient());
            $this->setCallback(array(
                'timeout' => isset($this->currentPlayer()['timeout']) ? $this->currentPlayer()['timeout'] - time() : static::START_TIME_OUT,
                'app'       => array(
                    'id'   => $this->getId(),
                    'uid'  => $this->getUid(),
                    'key'  => $this->getKey(),
                    'mode' => $this->getCurrency() . '-' . $this->getPrice()
                ),
                'appId' => $this->getUid(),
                'appMode' => $this->getCurrency().'-'.$this->getPrice(),
                'appName' => $this->getKey(),
                'players' => $this->getPlayers(),
                'fields' => array($this->getClient()->id => $this->getField()[$this->getClient()->id]),
                'variation' => $this->getVariation(),
                'action' => 'wait',
            ));

        } else {

            #echo $this->time().' '. "Количество полей не совпадает с игроками \n";
            $this->setResponse($this->getClients());
            $this->setCallback(array(
                'timeout' => isset($this->currentPlayer()['timeout']) ? $this->currentPlayer()['timeout'] - time() : static::START_TIME_OUT,
                'app'       => array(
                    'id'   => $this->getId(),
                    'uid'  => $this->getUid(),
                    'key'  => $this->getKey(),
                    'mode' => $this->getCurrency() . '-' . $this->getPrice()
                ),
                'appId' => $this->getUid(),
                'appMode' => $this->getCurrency().'-'.$this->getPrice(),
                'appName' => $this->getKey(),
                'players' => $this->getPlayers(),
                'ships' => $this->getShips(),
                'variation' => $this->getVariation(),
                'action' => 'field'
            ));
        }

    }

    public function fieldAction($data)
    {
        if (!($error = $this->checkField($data->field))) {
            if (count($this->getField()) == count($this->getClients())) {
                $this->nextPlayer();
                $this->startAction();
            } else {
                $this->setResponse($this->getClient());
                $this->setCallback(array(
                    'action' => 'wait'
                ));
            }
        } else {
            $this->setResponse($this->getClient());
            $this->setCallback(array(
                'action' => 'error',
                'error' => $error));
        }
    }

    public function timeoutAction($data = null)
    {

        $this->unsetCallback();
        #echo $this->time().' '. "Тайм-аут ".($this->currentPlayer()['timeout']-time())." \n ";
        if (!$this->isOver() AND isset($this->currentPlayer()['timeout']) AND $this->currentPlayer()['timeout'] <= time()) {

            if (count($this->getField()) != count($this->getClients())) {
                #echo $this->time().' '. "Автозаполнение всех полей \n";
                $players = $this->getPlayers();
                foreach ($players as $player)
                    if (!isset($this->_field[$player['pid']])) {
                        $this->setField($this->generateField($player['pid']), $player['pid']);
                    }

                $this->nextPlayer();
                $this->startAction();
                return false;

            } else {

                #echo $this->time().' '. "Переход хода \n";
                $this->passMove();
                #echo $this->time().' '. 'разница времени после пасса '.$this->currentPlayer()['pid'].' - '.time().' - '.$this->currentPlayer()['timeout']."\n";
                $this->nextPlayer();
                #echo $this->time().' '. 'разница времени после перехода '.$this->currentPlayer()['pid'].' - '.time().' - '.$this->currentPlayer()['timeout']."\n";

            }

        }

        #echo $this->time().' '. "Не переход хода \n";

        if ($winner = $this->checkWinner())
            $this->setCallback(array(
                'winner' => $winner['pid'],
                'fields' => $this->getField(),
                'price' => $this->getPrice(),
                'currency' => $this->getCurrency()));

        $currentPlayer = $this->currentPlayer();

        $this->setCallback(array(
            'current' => $this->currentPlayer()['pid'],
            'timeout' => (isset($currentPlayer['timeout']) ? $currentPlayer['timeout'] : time() + 1) - time(),//($this->currentPlayer()['timeout']-time()>0?$this->currentPlayer()['timeout']-time():1),
            'players' => $this->getPlayers(),
            'action' => 'move'
        ));

        $this->setResponse($this->getClients());
        return false;

    }

    public function moveAction($data = null)
    {
        $this->unsetCallback();
        if (count($this->getField()) != count($this->getClients()) || $this->isOver()) {
            #echo '' . time() . ' ' . " поля не готовы для хода\n";
            return false;
        }

        if (!isset($data->cell) OR isset($this->getClients()[$this->currentPlayer()['pid']]->bot)) {
            #echo $this->time().' '. "ход бота\n";
            $cell = $this->generateMove();
        } else {
            #echo $this->time().' '. "ход игрока\n";
            $cell = explode('x', $data->cell);
        }

        if ($error = $this->checkError($cell))
            $this->setCallback(array('error' => $error));
        else {
            #echo $this->time().' '. "ход";
            $this->doMove($cell);

            if ($winner = $this->checkWinner()) {
                $this->setCallback(array(
                    'winner' => $winner['pid'],
                    'fields' => $this->getField(),
                    'currency' => $this->getCurrency(),
                    'price' => $this->getPrice()
                ));
            }

            $this->setCallback(array(
                'current' => $this->currentPlayer()['pid'],
                'timeout' => $this->currentPlayer()['timeout'] - time(),
                'cell' => $this->getCell($cell),
                'players' => $this->getPlayers()
            ));
        }

        $this->setResponse($this->getClients());

        if (array_key_exists('error', $this->getCallback()))
            $this->setCallback(array('action' => 'error'))
                ->setResponse($this->getClient());

        elseif (!array_key_exists('action', $this->getCallback()))
            $this->setCallback(array('action' => 'move'));
        #echo $this->time().' '. "Конец хода \n";
    }


    public function replayAction($data = null)
    {
        #echo $this->time() . ' ' . "Повтор игры {$this->getUid()} " . (isset($this->getClient()->bot) ? 'бот' : 'игрок') . " №{$this->getClient()->id} \n";

        $clientId = $this->getClient()->id;
        $this->updatePlayer(array('ready' => 1), $clientId);
        $players = $this->getPlayers();

        if (isset($this->getClient()->bot) AND !in_array($this->getClient()->id, $this->_botReplay)) {
            $this->_botReplay[] = $this->getClient()->id;
            $this->_botTimer = array();
        }

        $ready = 0;
        foreach ($players as $player) {
            if (isset($player['ready']))// || isset($this->getClients()[$player['pid']]->bot))
                $ready += 1;
        }

        if ($ready == count($players)) {

            $this->setRun(1)
                ->setOver(0)
                ->setSaved(0);

            #echo $this->time().' '. "Переустановка игроков\n";

            $this->unsetFieldPlayed()
                ->setField(array())
                ->setPlayers($this->getClients())
                ->updatePlayer(array(
                    'timeout' => static::START_TIME_OUT - $this->getOptions('t'),
                    'ships' => array_count_values($this->getShips())
                    ))
                ->setWinner(false)
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

    public function doMove($cell)
    {
        $playerId = $this->getClient()->id;
        list($x, $y, $f) = $cell;

        $hit = (isset($this->_field[$f][$x][$y]) ? true : false);
        // $this->_field[$f][$x][$y] = 'h';

        $this->_fieldPlayed[$f][$x][$y] = $hit ? 'd' : 'e';

        if (!empty($this->_destroy)) $this->_destroy = array();

        if ($hit) {
            $this->updatePlayer(array('hit', 'hit' => $cell), $playerId);
            if ($ship = $this->isDestroyed($cell, array($cell))) {
                $this->setCallback(array('fields' => $this->_destroy));
                $this->updatePlayer(array('ships',
                    'ships' => (array($ship => $this->getPlayers()[$f]['ships'][$ship] - 1) + $this->getPlayers()[$f]['ships'])), $f);
                $this->updatePlayer(array('points' => $hit, 'hit'), $playerId);

                foreach(array_shift($this->_destroy) as $x=>$cell)
                    foreach($cell as $y=>$v){
                        $this->_fieldPlayed[$f][$x][$y] = $v;
                    }
            }
        }

        #echo $this->time() . ' ' . "следующий игрок";
        $this->nextPlayer($hit);

        return $this;
    }

    public function isDestroyed($cell, $ignore = array())
    {
        list($x, $y, $f) = $cell;
        $this->_destroy[$f][$x][$y] = 'k';

        #echo $this->time().' '. "Проверка $x x $y \n";
        foreach ($this->_matrix as $mx) {

            list($x1, $y1) = $mx;
            #echo $x1.$y1." ";
            $x1 += $x;
            $y1 += $y;
            $cell1 = array($x1, $y1, $f);

            if ($this->isCell($cell1) && !in_array($cell1, $ignore)) {
                if (isset($this->getField()[$f][$x1][$y1]) && !isset($this->getFieldPlayed()[$f][$x1][$y1])) {
                    #echo $this->time().' '. "Проверка1 $x1 x $y1 \n";
                    return false;
                } elseif (isset($this->getFieldPlayed()[$f][$x1][$y1]) && $this->getFieldPlayed()[$f][$x1][$y1] == 'd' && !$this->isDestroyed($cell1, array($cell, $cell1))) {
                    #echo $this->time().' '. "Проверка2 $x1 x $y1 \n";
                    return false;
                }

                if (isset($this->getField()[$f][$x1][$y1]))
                    $this->_destroy[$f][$x1][$y1] = 'k';
                else
                    $this->_destroy[$f][$x1][$y1] = 'e';

            }
            #else echo $this->time().' '. "Игнор $x1 x $y1 \n";
        }

        return $this->getField()[$f][$x][$y];
    }

    public function generateHit($cell, $ignore = array(),$time)
    {
        if($time+5<time()){
            echo $this->time()." [ERROR] Поиск попадания занимает более 5 секунд\n";
            print_r($this->getFieldPlayed());
            return array(1,1,$this->getClient()->id);
        }
        $hit = rand(0, 1);
        list($x, $y, $f) = $cell;
        foreach ($this->_hit_matrix as $mx) {
            list($x1, $y1) = $mx;
            if(isset($this->getFieldPlayed()[$f][$x1 + $x][$y1 + $y]) && $this->getFieldPlayed()[$f][$x1 + $x][$y1 + $y] == 'd'){
                $vector=($x1!=0?'x':'y');
                #echo $this->time().' '. "Нашли вектор $vector \n";
                break;
            }
        }
        $matrix=$this->_hit_matrix;
        shuffle($matrix);
        foreach ($matrix as $mx) {
            list($x1, $y1) = $mx;
            if(isset($vector) && (($vector=='x' && $x1==0) || ($vector=='y' && $y1==0))){
                #echo $this->time().' '. "Пропускаем $x1, $y1 \n";
                continue;
            }

            $x1 += $x;
            $y1 += $y;
            $cell1 = array($x1, $y1, $f);


            if ($this->isCell($cell1) AND !in_array($cell1,$ignore)) {
                if ((isset($this->getField()[$f][$x1][$y1]) OR $hit) && !isset($this->getFieldPlayed()[$f][$x1][$y1])) {
                    #echo $this->time().' '. "Нашли $x1, $y1 \n";
                    return array($x1,$y1,$f);
                } elseif (isset($this->getFieldPlayed()[$f][$x1][$y1]) && $this->getFieldPlayed()[$f][$x1][$y1] == 'd' && $hit_cell=$this->generateHit($cell1, array($cell, $cell1),$time)) {
                    #echo $this->time().' '. "Нашли {$hit_cell[0]}, {$hit_cell[1]} \n";
                    return $hit_cell;
                }
            }
        }
        return false;
    }

    public function generateMove()
    {
        foreach ($this->getClients() as $client)
//            if (!isset($client->bot))
            if ($client->id!=$this->getClient()->id)
                break;

        #echo $this->time().' '. "Генерация хода для бота\n";

        if (isset($this->currentPlayer()['hit']) && $cell = $this->currentPlayer()['hit']) {
            list($x, $y, $f) = $this->generateHit($cell,array($cell),time());
        } else {

            $miss = $this->isSuccessMove() ? rand(0, $this->isSuccessMove()) : true;
            $i=0;

            do {
                $i++;

                $x = rand(1, $this->getOptions('x'));
                $y = rand(1, $this->getOptions('y'));
                if ($i>1000) {
                    echo $this->time() . ' ' . " [ERROR] Цикл превысил 1000 переборов\n";
                    print_r($this->getPlayers());
                    echo "\n";
                    foreach($this->getFieldPlayed() as $fid=>$field)
                        echo ' '.$fid.':'.(count($field, COUNT_RECURSIVE) - count($field));
                    echo "\n";
                    return array(1,1,$this->getClient()->id);
                    }
            } while ((!isset($this->_field[$client->id][$x][$y]) AND !$miss) OR isset($this->_fieldPlayed[$client->id][$x][$y]));
        }
        return array($x, $y, $client->id);
    }

    public function checkWinner()
    {
        #echo $this->time().' '. "Проверка победителя \n";
        $current = $this->currentPlayer();
        if ($current['points'] >= count($this->getShips()) OR $current['moves'] <= 0) {
            if ($current['moves'] <= 0)
                $this->updatePlayer(array('points', 'points' => -1), $current['pid']);

            $players = $this->getPlayers();
            $winner = array();
            foreach ($players as $player) {
                if (array_key_exists($player['points'], $winner))
                    $winner[$player['points']]['count'] += 1;
                else
                    $winner[$player['points']]['count'] = 1;
                $winner[$player['points']]['player'] = $player;
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

                $this->setRun(0)
                    ->setOver(1);

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

    public function checkField($usr_ships)
    {
        /*$field = array(
            array(array(1, 2), array(1, 3), array(1, 4), array(1, 5)),
            array(array(3, 4),array(4, 4),array(5, 4)),
            array(array(7, 1),array(7, 2),array(7, 3)),
            array(array(6, 10),array(6, 11)),
            array(array(9, 3),array(9, 4)),
            array(array(9, 8),array(9, 9)),
            array(array(6, 7)),
            array(array(6, 19)),
            array(array(10, 1)),
            array(array(6, 13)),
        );
        $this->setClients(array(1 => (object)array('id' => 1)));
        $this->setClient(1);
        $this->getClient()->id;
        */

        if(isset($this->getField()[$this->getClient()->id]))
            return 'FIELD_ALREADY_EXIST';

        // проверка на количество кораблей
        if (count($usr_ships) != count($this->getShips())){
            echo count($usr_ships).'!='.count($this->getShips())."\n";
            return 'ERROR_AMOUNT_SHIPS';
        }

        $ships = array();
        //$player_ships = array();
        $field = array();
        $iterration = 0;

        foreach ($usr_ships as $ship) {
            $prv_cell = array();
            $tpm_field = $field;
            $iterration++;
            $cnt = 0;

            list($x, $y) = $ship[0];

            do {
                $cell = array($x, $y);
//            foreach ($ship as $cell) {
//                  list($x, $y) = $cell;
                $cnt++;

                if (!$this->isCell($cell)) {
                    $this->printField($tpm_field);
                    return 'ERROR_COORDINATES';
                }

                if (!$this->isAlone($cell, $field)) {
                    $this->printField($tpm_field);
                    return 'SHIP_TOO_CLOSE';
                }

                if (count($prv_cell))
                    if (!($x - $prv_cell[0] == 1 AND $y - $prv_cell[1] == 0) AND !($x - $prv_cell[0] == 0 AND $y - $prv_cell[1] == 1)){
                        $this->printField($tpm_field);
                        return 'BROKEN_SHIP';
                    }

                $prv_cell = array($x, $y);
                $tpm_field[$x][$y] = $ship[2];
//                  $tpm_field[$x][$y] = count($ship);
//            }
                $ship[1] ? $x++ : $y++;
            } while ($cnt < $ship[2]);

            if (!isset($ships[$cnt]))
                $ships[$cnt] = 0;
            $ships[$cnt] += 1;
            //$player_ships[$iterration]=array('l'=>$cnt,'d'=>0);
            $field = $tpm_field;//array_merge($tmp_field, $ship_xy);
        }

        if (count(array_diff_assoc(array_count_values($this->getShips()), $ships)) || count(array_diff_assoc($ships, array_count_values($this->getShips()))))
            return 'ERROR_CLASS_SHIPS';

        //$this->_playerShips[$this->getClient()->id]=$player_ships;

        $this->setField($field, $this->getClient()->id);

        return false;

    }

    public function printField($field){


        echo "\n";
        echo "\n   ";
        for ($i = 1; $i <= $this->getOptions('x'); ++$i)
            echo sprintf("%02d", $i) . " ";
        for ($j = 1; $j <= $this->getOptions('y'); ++$j) {
            echo "\n" . sprintf("%02d", $j) . "";
            for ($i = 1; $i <= $this->getOptions('x'); ++$i)
                echo isset($field[$i][$j]) ? "  " . $field[$i][$j] : '   ';
        }
    }

    public function isAlone($cell,$field)
    {
        list($x, $y) = $cell;
        foreach ($this->_matrix as $mx) {
            list($x1, $y1) = $mx;
            if ($x + $x1 > 0 && $x + $x1 <= $this->getOptions('x') || $y + $y1 > 0 || $y + $y1 <= $this->getOptions('y'))
                if (isset($field[$x + $x1][$y + $y1]))
                    return false;
            }
        return true;
    }

    public function isClicked($cell)
    {
        list($x,$y,$f)=$cell;
        return (isset($this->getFieldPlayed()[$f][$x][$y]));
    }

    public function getCell($cell)
    {
        list($x,$y,$f)=$cell;
        return array(
            'coord'=>$x.'x'.$y.'x'.$f,
            'class'=>$this->getFieldPlayed()[$f][$x][$y]
        );
    }

    public function getShips()
    {        
        return isset($this->{'_ships'.$this->getVariation('field')}) ? $this->{'_ships'.$this->getVariation('field')} : $this->_shipsDefault;
    }


    public function generateField($playerId=null)
    {
        $playerShips = array();
        $field = array();
        $ships = $this->getShips();
        rsort($ships);
        $iterration = 0;
        $tries = 0;

        while (count($playerShips) != count($ships)) {

            $x = rand(1, $this->getOptions('x'));
            $y = rand(1, $this->getOptions('y'));
            $v = rand(0,1);
            $ship = array();

            while (count($ship) != $ships[$iterration]) {
                if ($ships[$iterration] != 1)
                    if (!$this->isCell(($v ? array($x+1,$y) : array($x,$y+1))))
                        break;

                if (!$this->isAlone(array($x,$y), $field))
                    break;

                $ship[] = array($x,$y);
                $v ? $x++ : $y++;
            }

            if(count($ship) == $ships[$iterration]){
                foreach($ship as $cell)
                    $field[$cell[0]][$cell[1]]=$ships[$iterration];
                $playerShips[]=$ship;
                //$player_ships[$iterration]=array('l'=>count($ship),'d'=>0);
                // echo "\nКОРАБЛЬ №$iterration ".$ships[$iterration].'-палубный';
                
                $iterration++;
            } else
                $tries++;

            if($tries>100){
                echo "\n!!!ПРЕВЫШЕНИЕ ИТТЕРАЦИЙ!!! КОРАБЛЬ №$iterration ".$ships[$iterration].'-палубный';
                $field = $this->generateField($playerId);
                break;
            }

        }

        return $field;
    }

}
