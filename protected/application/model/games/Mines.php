<?php

Application::import(PATH_APPLICATION . 'model/Game.php');

class Mines extends Game
{
    protected $_matrix = array(
        array(-1, -1), array(-1, 0), array(-1, 1),
        array(0, -1),                array(0, 1),
        array(1, -1),  array(1, 0),  array(1, 1),
    );

    protected $_mines = 0;
    protected $_size = 0;
    protected $_cells = 0;
    protected $_gameVariation = array('mines'=>1);

    public function doMove($cell)
    {
        list($x,$y)=$cell;
        $playerId = $this->currentPlayer()['pid'];

        $this->_field[$x][$y]['player'] = $playerId;
        $this->_fieldPlayed[$x][$y] = $this->_field[$x][$y];


        if($this->_field[$x][$y]['mine']=='m') {
            $this->updatePlayer(array('moves' => -1), $playerId);
            //$this->_mines--;
            echo "Мина\n";
        } else {

            $this->_cells++;
            echo $x.'x'. $y."\n";

            $this->updatePlayer(array('points' => 1), $playerId);
            if (!isset($this->_field[$x][$y]['mine']) && $empty=$this->getEmpty($cell, $playerId))
                $this->setCallback(array('field' => $empty));
        }

        //echo $this->time().' '. "следующий игрок";
        $this->nextPlayer();

        return $this;
    }

    public function getEmpty($cell, $playerId, &$empty=array(),&$ignore=array())
    {
        list($x, $y) = $cell;
        $ignore[]=$cell;

        foreach ($this->_matrix as $dir) {
                while(
                    $x+$dir[0]>0 && $x+$dir[0]<=$this->_size
                    && $y+$dir[1]>0 && $y+$dir[1]<=$this->_size
                    && !in_array(array($x+$dir[0],$y+$dir[1]),$ignore)
                ){
                    $x1=$x+$dir[0];
                    $y1=$y+$dir[1];

                    #echo $this->time().' '. "Проверка $x1 x $y1 \n";

                    echo $x1.'x'. $y1."\n";
                    $ignore[]=array($x1,$y1);

                    if(!$this->_field[$x1][$y1]['player']) {
                        $this->_cells++;
                        $this->_field[$x1][$y1]['player'] = $playerId;
                        $empty[$x1][$y1] = $this->_fieldPlayed[$x1][$y1] = $this->_field[$x1][$y1];
                    }

                    if(!$this->_field[$x1][$y1]['mine'])
                        $this->getEmpty(array($x1,$y1), $playerId, $empty, $ignore);
                }

            #else echo $this->time().' '. "Игнор $x1 x $y1 \n";
        }
        return $empty;
    }


    public function checkWinner()
    {
        echo $this->time().' '. "Проверка победителя \n";
        $current = $this->getPlayers()[$this->getClient()->id];

        echo ($this->_size * $this->_size) - $this->_cells - $this->_mines.' = '.$this->_size * $this->_size.'-'.$this->_cells .' + '. $this->_mines."\n";

        if (($this->getOptions('w') && $current['points'] >= $this->getOptions('w')) OR $current['moves'] <= 0 OR (($this->_size * $this->_size) - $this->_mines == $this->_cells)) {
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

                echo $this->time().' '. "Экстра время \n";

                $this->unsetPlayers()
                    ->startAction()
                    ->setCallback(array('extra' => 1));
                // $this->updatePlayer(array('moves' => 1));
            }

        }
        #echo $this->time().' '. "Победителя нет \n";
    }

    public function isCell($cell)
    {
        list($x,$y)=$cell;
        return ($x>0 && $y>0 && $x<= $this->_size && $y<= $this->_size);
    }

    public function generateField()
    {
        $gameField=array();

        $this->_mines = $mines = $this->getVariation('mines');//(int)($this->getOptions('x') * $this->getOptions('y') / 5);
        $this->_size = (int) sqrt($this->_mines*5);

        $this->_cells = 0;

        for ($x = 1; $x <= $this->_size ; ++$x) {
            for ($y = 1; $y <= $this->_size; ++$y) {
                $gameField[$x][$y]['player'] = null;
                $gameField[$x][$y]['mine'] = null;
                $gameField[$x][$y]['coord'] = $x.'x'.$y;
            }
        }


        do {
            do {
                $x = rand(1, $this->_size);
                $y = rand(1, $this->_size);
            } while ($gameField[$x][$y]['mine']=='m');

            $mines--;
            $gameField[$x][$y]['mine'] = 'm';

            foreach ($this->_matrix as $mx) {
                if($gameField[$x+$mx[0]][$y+$mx[1]]['mine']!='m')
                    $gameField[$x+$mx[0]][$y+$mx[1]]['mine']+=1;
            }



        } while ($mines);

        return $gameField;
    }

    public function generateMove()
    {
        #echo $this->time().' '. "Генерация поля для бота\n";
        do {
            $x = rand(1, $this->_size);
            $y = rand(1, $this->_size);
        } while($this->_field[$x][$y]['player']);
        return array($x, $y);
    }
}