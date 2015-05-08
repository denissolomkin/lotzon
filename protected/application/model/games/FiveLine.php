<?php

Application::import(PATH_APPLICATION . 'model/Game.php');

class FiveLine extends Game
{
    protected $_matrix = array(
        array( array(-1,-1), array(1,1) ), # \
        array( array(0,-1),  array(0,1) ), # |
        array( array(1,-1), array(-1,1) ), # /
        array( array(-1,0),  array(1,0) ), # -

    );

    public function doMove($cell)
    {
        list($x,$y)=$cell;
        $current = $this->currentPlayer();
        $playerId = $current['pid'];
        $points=$current['points'];

        $this->_field[$x][$y]['player'] = $playerId;
        $this->_fieldPlayed[$x][$y] = $this->_field[$x][$y];

        if(($diff=$this->maxLine($cell,$playerId)-$points)>0)
            $this->updatePlayer(array('points' => $diff), $playerId);

        //echo $this->time().' '. "следующий игрок";
        $this->nextPlayer();
        return $this;
    }

    public function maxLine($cell, $playerId)
    {
        list($x, $y) = $cell; $max=0; $field=$this->getFieldPlayed();

        #echo $this->time().' '. "Проверка $x x $y \n";
        foreach ($this->_matrix as $mx) {
            $count=1;
            $line=array($x=>array($y=>'w'));
            foreach ($mx as $dir) {
                $x1=$x; $y1=$y;
                while($x1+$dir[0]>0 && $x1+$dir[0]<=$this->getOption('x') && $y1+$dir[1]>0 && $y1+$dir[1]<=$this->getOption('y') && $field[$x1+$dir[0]][$y1+$dir[1]]['player']==$playerId){
                    $x1+=$dir[0];
                    $y1+=$dir[1];
                    $line[$x1][$y1]='w';
                    $count++;
                }
            }
            $max=max($max,$count);
            if($max>=$this->getOption('w')){
                $this->setCallback(array(
                    'line' => $line,
                ));
                return $max;
            }
            #else echo $this->time().' '. "Игнор $x1 x $y1 \n";
        }

        return $max;
    }

    public function checkWinner()
    {
        echo $this->time().' '. "Проверка победителя \n";
        $current = $this->getPlayers()[$this->getClient()->id];

        if ($current['points'] >= $this->getOption('w') OR $current['moves'] <= 0) {
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
                $this->updatePlayer(array('result' => -1));
                $this->updatePlayer(array('result' => 2), current($winner)['player']['pid']);
                $this->setTime(time());
                $this->_isOver      = 1;
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

    public function generateField()
    {
        $gameField=array();
        for ($i = 1; $i <= $this->getOption('x'); ++$i) {
            for ($j = 1; $j <= $this->getOption('y'); ++$j) {
                $gameField[$i][$j]['player'] = null;
                $gameField[$i][$j]['coord'] = $i.'x'.$j;
            }
        }
        return $gameField;
    }

    public function generateMove()
    {
        #echo $this->time().' '. "Генерация поля для бота\n";
        do {
            $x = rand(1, $this->getOption('x'));
            $y = rand(1, $this->getOption('y'));
        } while($this->_field[$x][$y]['player']);
        return array($x, $y);
    }
}