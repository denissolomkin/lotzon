<?php
Application::import(PATH_APPLICATION . 'model/Entity.php');

class Game extends Entity
{
    protected $_id        = 0;
    protected $_key       = '';
    protected $_title         = array();
    protected $_options       = array();
    protected $_gameVariation = array();
    protected $_gameModes     = array();

    protected $_uid           = '';
    protected $_currency      = '';
    protected $_price         = null;
    protected $_numberPlayers = null;
    protected $_time          = null;
    protected $_ping          = null;

    public $_bot       = array();
    public $_botTimer  = array();
    public $_botReplay = array();

    protected $_over  = 0;
    protected $_run   = 0;
    protected $_saved = 0;

    protected $_players  = array();
    protected $_clients  = array();
    protected $_client   = '';
    protected $_callback = array();
    protected $_response = '';
    protected $_winner   = null;
    protected $_loser    = null;
    private   $_current  = array();

    protected $_field       = array();
    protected $_fieldPlayed = array();

    public function __construct($game, $variation)
    {

        $this->setId($game->getId())
            ->setKey($game->getKey())
            ->setOptions($game->getOptions())
            ->setTitle($game->getTitle())
            ->setModes($game->getModes())
            ->setVariation($variation)
            ->setUid(uniqid())
            ->setTime(time())
            ->init();

        $this->setModelClass('GameAppsModel');
    }

    public function init()
    {
        $this->setField($this->generateField());
    }

    public function validate()
    {
        return true;
    }

    public function fetch()
    {
        try {
            $model = $this->getModelClass();
            $model::instance()->fetch($this);
        } catch (ModelException $e) {
            if ($e->getCode() == 404) {
                return false;
            } else
                echo '[ERROR] Game->fetch(): ' . $e->getCode() . $e->getMessage();
        }

        return $this;
    }

    public function create()
    {

        $this->setPing(time());

        try {
            $model = $this->getModelClass();
            $model::instance()->create($this);
        } catch (ModelException $e) {
            echo '[ERROR] Game->create(): ' . $e->getCode() . $e->getMessage();
        }

        return $this;
    }

    public function update()
    {

        $this->setPing(time());

        try {
            $model = $this->getModelClass();
            $model::instance()->update($this);
        } catch (ModelException $e) {
            echo '[ERROR] Game->update(): ' . $e->getMessage(). $e->getCode();
        }

        return $this;
    }

    public function saveResults()
    {

        $this->setPing(time());

        $model = $this->getModelClass();

        echo $this->time(1) . " " . $this->getKey() . ' ' . $this->getUid() . " - Сохраняем игру: \n";
        print_r($this->getPlayers());

        try {
            $this->setSaved(1);
            return $model::instance()->saveResults($this);
        } catch (ModelException $e) {
            echo '[ERROR] Game->saveResults(): ' . $e->getMessage();
            return false;
        }

    }

    public function quitAction($data = null)
    {
        #echo $this->time().' '. "Выход из игры\n";

        if (!$this->isOver()) {
            $this->startAction($data);
        } else {
            $playerId = $this->getClient()->id;
            $this->unsetCallback();
            $this->setCallback(array(
                'quit'   => $playerId,
                'action' => 'quit'
            ));


            $this->setResponse($this->getClients());
            #echo $this->time().' '. "Удаляем из клиентов игры {$playerId}\n";
            $this->unsetClients($playerId)
                ->setPlayers($this->getClients());
        }

        #echo $this->time().' '. "Конец выход из игры\n";
    }

    public function surrenderAction($data = null)
    {
        #echo $this->time().' '. "Сдаться\n";

        $playerId = $this->getClient()->id;

        $this->updatePlayer(array('result' => 1))
            ->updatePlayer(array('result' => -2), $playerId)
            ->unsetCallback();

        $this->setRun(0)
            ->setOver(1)
            ->setSaved(0);

        if (count($this->getPlayers()) > 1)
            while ($this->currentPlayer()['pid'] == $playerId)
                $this->nextPlayer();

        $this->setWinner($this->currentPlayer()['pid']);

        $this->setCallback(array(
            'winner'   => $this->currentPlayer()['pid'],
            'players'  => $this->getPlayers(),
            'currency' => $this->getCurrency(),
            'price'    => $this->getPrice(),
            'action'   => 'move'
        ));

        $this->setResponse($this->getClients());
        #echo $this->time().' '. "Конец сдаться\n";
    }


    public function readyAction($data = null)
    {
        #echo $this->time().' '. "Повтор игры {$this->getUid()} ".(isset($this->getClient()->bot) ?'бот':'игрок')." №{$this->getClient()->id} \n";
        #echo " REPLAY  \n";

        if ($this->isRun()) {
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

                $this->setRun(1)
                    ->setSaved(0);

                $this->unsetPlayers()
                    ->startAction();

            } else {

                $this->setOver(0);

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

    public function replayAction($data = null)
    {
        #echo $this->time().' '. "Повтор игры {$this->getUid()} ".(isset($this->getClient()->bot) ?'бот':'игрок')." №{$this->getClient()->id} \n";
        #echo " REPLAY  \n";

        $clientId = $this->getClient()->id;
        $this->updatePlayer(array('reply' => 1), $clientId);
        $players = $this->getPlayers();

        if (isset($this->getClient()->bot) AND !in_array($clientId, $this->_botReplay)) {
            $this->_botReplay[] = $clientId;
        }

        $reply = 0;
        foreach ($players as $player) {
            if (isset($player['reply']))// || isset($this->getClients()[$player['pid']]->bot))
                $reply += 1;
        }

        if ($reply == count($players)) {

            $this->setRun(1)
                ->setOver(0)
                ->setSaved(0);

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
                    'reply'  => $reply
                ));
        }

        #echo $this->time().' '. "Конец повтора игры\n";
    }

    public function startAction($data = null)
    {
        #echo $this->time(0).' '. "Старт\n";
        $this->unsetCallback();

        if (!$this->getPlayers()) {

            #echo $this->time().' '. "Первичная установка игроков\n";
            $this->setPlayers($this->getClients())
                ->nextPlayer()
                ->setWinner(null);

            $this->setRun(1)
                ->setOver(0)
                ->setSaved(0);
        }

        if ($this->getWinner())
            $this->setCallback(array(
                'winner'   => $this->getWinner(),
                'price'    => $this->getPrice(),
                'currency' => $this->getCurrency()
            ));

        $this->setCallback(array(
            'current'   => $this->currentPlayer()['pid'],
            'timeout'   => $this->currentPlayer()['timeout'] - time(),
            'app'       => array(
                'id'   => $this->getId(),
                'uid'  => $this->getUid(),
                'key'  => $this->getKey(),
                'mode' => $this->getCurrency() . '-' . $this->getPrice()
            ),
            'appId'     => $this->getUid(),
            'appMode'   => $this->getCurrency() . '-' . $this->getPrice(),
            'appName'   => $this->getKey(),
            'players'   => $this->getPlayers(),
            'field'     => $this->getFieldPlayed(),
            'variation' => $this->getVariation(),
            'action'    => 'start'
        ));

        $this->setResponse($this->getClients());
    }

    public function moveAction($data = null)
    {
        $this->unsetCallback();
        if (!isset($data->cell) OR isset($this->getClients()[$this->currentPlayer()['pid']]->bot)) {
            #echo ''.time().' '. "ход бота\n";
            $cell = $this->generateMove();
        } else {
            #echo ''.time().' '. "ход игрока\n";
            $cell = explode('x', $data->cell);
        }

        if ($error = $this->checkError($cell)) {
            $this->setCallback(array('error' => $error));
        } else {
            #echo $this->time().' '. "делаем ход\n";
            $this->doMove($cell);

            if ($winner = $this->checkWinner()) {
                $this->setCallback(array(
                    'winner'   => $winner['pid'],
                    'currency' => $this->getCurrency(),
                    'price'    => $this->getPrice()
                ));
            }

            $this->setCallback(array(
                'current' => $this->currentPlayer()['pid'],
                'timeout' => $this->currentPlayer()['timeout'] - time(),
                'cell'    => $this->getCell($cell),
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

    public function timeoutAction($data = null)
    {

        #echo $this->time().' '. "Тайм-аут \n";
        $this->unsetCallback();
        if (!$this->isOver() AND isset($this->currentPlayer()['timeout']) AND $this->currentPlayer()['timeout'] <= time()) {

            #echo $this->time().' '. "Переход хода \n";
            $this->passMove();
            #echo $this->time().' '. 'разница времени после пасса '.$this->currentPlayer()['pid'].' - '.time().' - '.$this->currentPlayer()['timeout']."\n";
            $this->nextPlayer();
            #echo $this->time().' '. 'разница времени после перехода '.$this->currentPlayer()['pid'].' - '.time().' - '.$this->currentPlayer()['timeout']."\n";
        }

        if ($winner = $this->checkWinner())
            $this->setCallback(array(
                'winner'   => $winner['pid'],
                'price'    => $this->getPrice(),
                'currency' => $this->getCurrency()));

        $currentPlayer = $this->currentPlayer();

        $this->setCallback(array(
            'current' => $this->currentPlayer()['pid'],
            'timeout' => (isset($currentPlayer['timeout']) ? $currentPlayer['timeout'] : time() + 1) - time(),//($this->currentPlayer()['timeout']-time()>0?$this->currentPlayer()['timeout']-time():1),
            'players' => $this->getPlayers(),
            'action'  => 'move'
        ));

        $this->setResponse($this->getClients());
        #echo $this->time().' '. "Конец тайм-аута \n";
    }

    public function checkError($cell)
    {
        list($x, $y) = $cell;
        #echo $this->time().' '. "Проверка ошибок \n";
        if (!$this->isMove()) {
            #echo " NOT_YOUR_MOVE\n";
            return 'NOT_YOUR_MOVE';
        } elseif (!$this->isCell($cell)) {
            #echo " WRONG_CELL\n";
            return 'WRONG_CELL ' . $x . 'x' . $y;
        } elseif ($this->isClicked($cell)) {
            #echo " CELL_IS_PLAYED\n";
            return 'CELL_IS_PLAYED';
        } elseif ($this->isOver()) {
            #echo " GAME_IS_OVER\n";
            return 'GAME_IS_OVER';
        }
    }

    public function isCell($cell)
    {
        list($x, $y) = $cell;

        return ($x > 0 && $y > 0 && $x <= $this->getOptions('x') && $y <= $this->getOptions('y'));
    }

    public function isClicked($cell)
    {
        list($x, $y) = $cell;

        return $this->_field[$x][$y]['player'];
    }

    public function isMove()
    {
        $current = $this->currentPlayer();

        return ($current['pid'] == $this->getClient()->id);

    }

    public function getCell($cell)
    {
        list($x, $y) = $cell;

        return $this->_field[$x][$y];
    }

    public function passMove()
    {
        #echo $this->time().' '. "Пас хода \n";
        $current = $this->currentPlayer();
        $this->updatePlayer(array(
            'moves' => -1), $current['pid']);

        return $this;
    }

    public function doMove($cell)
    {
        list($x, $y) = $cell;
        $playerId                       = $this->getClient()->id;
        $points                         = $this->_field[$x][$y]['points'];
        $this->_field[$x][$y]['player'] = $playerId;

        $this->updatePlayer(array(
            'points' => $points,
            'moves'  => -1), $playerId);

        $this->_fieldPlayed[$x][$y] = $this->_field[$x][$y];

        $this->nextPlayer();

        return $this;
    }

    public function generateMove()
    {

        //$minimum=( rand(1,2) < 2 ? ($this->FIELD_SIZE_X*$this->FIELD_SIZE_Y)-($this->GAME_MOVES*($this->GAME_PLAYERS+1))):0);
        //$minimum=( rand(1,5) < 2 ? ($this->FIELD_SIZE_X*$this->FIELD_SIZE_Y/2):0 );
        //$minimum=0;

        if ($this->isSuccessMove() > 0)
            $minimum = (!rand(0, $this->isSuccessMove() - 1) ? ($this->getOptions('x') * $this->getOptions('y')) - ($this->getOptions('m') * ($this->getOptions('p') + 1)) : 0);
        else
            $minimum = 0;

        #echo $this->time().' '. "Генерация поля для бота\n";
        do {
            do {
                #echo "Ход бота\n";
                $x = rand(1, $this->getOptions('x'));
                $y = rand(1, $this->getOptions('y'));
            } while ($this->_field[$x][$y]['player']);
        } while ($this->_field[$x][$y]['points'] < $minimum);

        #echo $this->time().' '. "Ход бота $x, $y = $minimum ".$this->_field[$x][$y]['points']."\n";
        return array($x, $y);
    }

    public function nextPlayer($skip = false)
    {

        if ($skip !== true) {

            while ($this->currentPlayer() && current($this->_players)['pid'] != $this->currentPlayer()['pid'])
                if (next($this->_players) === false)
                    reset($this->_players);

            #echo $this->time().' '. "Следующий игрок \n";
            if (next($this->_players) === false)
                reset($this->_players);

            if ($skip == 'init') {
                $this->currentPlayers(array(current($this->_players)['pid']));

                return;
            } elseif ($skip === false) {
                $this->currentPlayers(array(current($this->_players)['pid']));
            } else {
                return current($this->_players)['pid'];
            }
        }

        $this->_botTimer = array();

        foreach ($this->currentPlayers() as $playerId) {
            $this->_players[$playerId]['timeout'] = time() + $this->getOptions('t');
            if (isset($this->_clients[$playerId]->bot))
                $this->_botTimer[$playerId] = rand(8, 30) / 10; // 0.1;
        }

        return $this;
    }

    public function currentPlayer()
    {
        return count($this->_current) ? $this->_players[reset($this->_current)] : false;
        /* return current($this->_players); */
    }

    public function currentPlayers($playerIds = false)
    {
        if ($playerIds !== false) {
            $this->_current = $playerIds;

            return $this;
        } else
            return $this->_current;
    }

    public function checkWinner()
    {
        #echo $this->time().' '. "Проверка победителя \n";
        $current = $this->currentPlayer();

        if ($current['moves'] < 1) {
            $players = $this->getPlayers();
            $winner  = array();
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
                    'win'    => $this->getPrice() * -1));
                $this->updatePlayer(array(
                    'result' => 2,
                    'win'    => $this->getPrice() + $this->getWinCoefficient()), current($winner)['player']['pid']);
                $this->setTime(time());

                $this->setRun(0)
                    ->setOver(1);

                $this->_botReplay = array();
                $this->_botTimer  = array();

                return current($winner)['player'];
            } else {
                #echo $this->time().' '. "Экстра время \n";
                $this->setCallback(array('extra' => 1));
                $this->updatePlayer(array('moves' => 1));
            }

        }
        #echo $this->time().' '. "Победителя нет \n";

    }

    public function updatePlayer($data, $id = null)
    {
        $currentPlayer = $this->currentPlayer();
        #echo $this->time().' '. "Обновление данных\n";
        if ($id)
            $players[] = $this->getPlayers()[$id];
        else
            $players = $this->getPlayers();


        foreach ($players as $player) {
            foreach ($data as $key => $value) {
                if (!is_numeric($key)) {
                    if (array_key_exists($key, $this->_players[$player['pid']]))
                        $this->_players[$player['pid']][$key] += $value;
                    else
                        $this->_players[$player['pid']][$key] = $value;
                } else {
                    if (array_key_exists($value, $this->_players[$player['pid']])) {
                        unset($this->_players[$player['pid']][$value]);
                        #echo $this->time().' '. "Удаление из игроков {$value}\n";
                    }
                }
            }
        }

        if (!$id) {
            reset($players);
            while (each($players) == $currentPlayer) {
            }
            #echo $this->time().' '. "Возврат указателя в массиве игроков \n";//print_r($this->currentPlayer());
        }


        return $this;
    }

    public function unsetPlayers()
    {
        $this->_players = array();

        return $this;
    }

    public function setPlayers($clients, $rand = true)
    {
        if ($rand)
            rand(0, 1) ? arsort($clients) : asort($clients);

        $order = 0;

        $this->unsetPlayers();

        if (!empty($clients))
            foreach ($clients as $id => $client) {

                if ($this->getNumberPlayers() > 2) ;
                $order++;

                if (isset($client->bot))
                    $this->_bot[$id] = $id;

                $this->_players[$id] = array(
                    'pid'     => $id,
                    'moves'   => $this->getOptions('m'),
                    'points'  => 0,
                    'avatar'  => $client->avatar,
                    'lang'    => isset($client->lang) ? $client->lang : 'RU',
                    'country' => isset($client->country) ? $client->country : 'RU',
                    'name'    => $client->name,
                    'timeout' => time() + $this->getOptions('t')
                );

                if ($order)
                    $this->_players[$id]['order'] = $order;
            }

        #echo $this->time().' '. "Инициализация игроков\n";
        return $this;
    }

    public function setClient($client)
    {
        $this->_client = $this->getClients($client);

        return $this;
    }

    public function unsetClients($id = null)
    {
        if ($id)
            unset($this->_clients[$id]);
        else
            unset($this->_clients);

        return $this;
    }

    public function setModes($array)
    {
        $this->_gameModes = $array;

        return $this;
    }

    public function getMode()
    {
        return implode('-', array(
            $this->getCurrency(),
            $this->getPrice(),
            $this->getNumberPlayers(),
            http_build_query($this->getVariation())
        ));
    }

    public function isSuccessMove()
    {
        return isset($this->_gameModes[$this->getCurrency()]) && isset($this->_gameModes[$this->getCurrency()][$this->getPrice()])
            ? $this->_gameModes[$this->getCurrency()][$this->getPrice()] : false;
    }

    public function setVariation($variation)
    {
        if ($variation)
            $this->_gameVariation = $variation;

        if (isset($this->_gameVariation['field'])) {
            $field = explode('x', $this->_gameVariation['field']);
            $this->setOptions(array('x' => $field[0], 'y' => $field[1]) + $this->getOptions());
        }

        return $this;
    }

    public function getVariation($key = null)
    {
        return isset($key) ? (isset($this->_gameVariation[$key]) ? $this->_gameVariation[$key] : false) : $this->_gameVariation;
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


        $coef *= ($this->getOptions('h') ? (100 - $this->getOptions('h')) / 100 : 1) * $this->getPrice();

        if ($coef > 0)
            $coef = floor($coef * 100) / 100;

        return $coef;
    }

    public function unsetCallback()
    {
        unset($this->_callback);

        return $this;
    }

    public function setCallback($callback, $playerId = null)
    {
        foreach ($callback as $key => $value)
            if (!$playerId)
                $this->_callback[$key] = $value;
            else
                $this->_callback[$playerId][$key] = $value;

        return $this;
    }

    public function setResponse($clients)
    {
        $this->_response = is_array($clients) ? $clients : array($clients);

        return $this;
    }

    public function unsetFieldPlayed()
    {
        $this->_fieldPlayed = array();

        return $this;
    }

    public function generateField()
    {
        $gameField = array();
        $numbers = range(1, $this->getOptions('x') * $this->getOptions('y'));
        shuffle($numbers);

        for ($i = 1; $i <= $this->getOptions('x'); ++$i) {
            for ($j = 1; $j <= $this->getOptions('y'); ++$j) {
                $gameField[$i][$j]['points'] = $numbers[(($i - 1) * $this->getOptions('y') + $j) - 1];
                $gameField[$i][$j]['player'] = null;
                $gameField[$i][$j]['coord']  = $i . 'x' . $j;
            }
        }

        return $gameField;
    }

    public function time($space = true)
    {
        return ($space ? ' ' : '') . date('H:i:s', time());
    }

}
