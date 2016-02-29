<?php

class GameConstructorChance extends GameConstructor
{

    protected $_time         = '';
    protected $_timeout      = '';
    protected $_combinations = array();
    protected $_matrix = array(
        'line'  => array(
            array(array(-1, -1), array(1, 1)), # \
            array(array(0, -1), array(0, 1)), # |
            array(array(1, -1), array(-1, 1)), # /
            array(array(1, 0), array(-1, 0)), # -
        ),

        'snake' => array(
            array(0, -1), array(0, 1), array(1, 0), array(-1, 0) # +
        ),
    );

    /* todo replace _field into _options */
    public function getOptions($key)
    {
        return $this->_field[$key];
    }

    /* todo replace to getOptions & addOptions in Chances */
    public function option($key, $value = null)
    {
        if ($value)
            $this->_field[$key] += $value;

        return $this->_field[$key];
    }

    public function doMove($cell)
    {

        $res        = array('Uid' => $this->getUid());
        $gameField  = $this->getGameField();
        $gamePrizes = $this->getGamePrizes();
        $format     = $this->option('f');

        if (isset($gameField[$cell]))
            return $res + array('error' => 'CELL_IS_PLAYED');

        if (((!$format || $format == 'cell') && count($gameField) >= $this->option('c'))
            || ($format && $format != 'cell' && $gamePrizes['hit'] >= $this->option('c'))
            || $this->option('m') === 0
        ) {
            return $res + array('error' => 'GAME_IS_OVER');
        }

        $res['Prize'] = $gameField[$cell] = false;
        $res['Cell']  = $cell;
        $res['Moves'] = $this->option('f') && $this->option('f') != 'cell' ? $this->option('m') : ($this->option('c') - count($gameField));


        if (is_array($prizes = $this->getPrizes()) && !empty($prizes)) {
            if ($format && $format != 'cell') {

                $prizeId = $format == 'miss' ? $gamePrizes['hit'] + 1 : $gamePrizes['hit'] + 1;
                if ($this->validCombination(
                    $cell,
                    $prizeId,
                    (isset($prizes[$prizeId]) ? ($prizes[$prizeId]['p'] ? !rand(0, $prizes[$prizeId]['p'] - 1) : false) : false),
                    true)
                ) {
                    // hit
                    $gamePrizes['hit'] = $gamePrizes['hit'] + 1;
                    $this->setGamePrizes($gamePrizes);
                    $res['Prize'] = $gameField[$cell] = array('t' => 'hit');
                } else {
                    // miss
                    $res['Moves'] = $this->option('m', -1);
                }

            } else {

                shuffle($prizes);
                $miss = true;


                foreach ($prizes as $prizeId => $prize) {
                    if (($this->validCombination(
                            $cell,
                            $prizeId,
                            (isset($prizes[$prizeId]) ? ($prizes[$prizeId]['p'] ? !rand(0, $prizes[$prizeId]['p'] - 1) : false) : false),
                            ($prizeId + 1 == count($prizes))) OR
                        count($prizes) - 1 == ($this->getField()['x'] * $this->getField()['y'] - count($gameField)))
                    ) {

                        unset($prize['p']);
                        $miss = false;

                        $gamePrizes[$prize['t']][] = $prize;
                        $this->setGamePrizes($gamePrizes);
                        unset($prizes[$prizeId]);
                        $res['Prize'] = $gameField[$cell] = $prize;
                        $this->setPrizes($prizes);

                        break;
                    }
                }

                // miss
                if ($miss === true) {
                    $this->option('m', -1);
                }

            }
        }

        $this->setGameField($gameField);


        /* end game */
        if (((!$format || $format == 'cell') && count($gameField) >= $this->option('c'))
            || ($format && $format != 'cell' && $gamePrizes['hit'] >= $this->option('c'))
            || ($this->option('m') === 0)
        ) {

            $gameField = $this->genCombination();
            $prizes    = array();

            if (is_array($gamePrizes)) {

                if (isset($gamePrizes['points'])) {
                    $prizes['POINT'] = 0;
                    foreach ($gamePrizes['points'] as $prize)
                        $prizes['POINT'] += (isset($prize['v']) ? $prize['v'] : 0);
                }

                if (isset($gamePrizes['money'])) {
                    $prizes['MONEY'] = 0;
                    foreach ($gamePrizes['money'] as $prize)
                        $prizes['MONEY'] += (isset($prize['v']) ? $prize['v'] : 0);
                }

                if (isset($gamePrizes['item'])) {
                    // item prize
                    $prizes['ITEM'] = '';
                    foreach ($gamePrizes['item'] as $prize)
                        $prizes['ITEM'] .= ' ' . $prize['n'];
                }

                if (isset($gamePrizes['math'])) {
                    // math function
                    foreach ($gamePrizes['math'] as $prize) {
                        if (isset($prizes['MONEY']) && $prizes['MONEY'] != 0)
                            eval("\$prizes['MONEY'] = " . $prizes['MONEY'] . $prize['v'] . ";");
                        if (isset($prizes['POINT']) && $prizes['POINT'] != 0)
                            eval("\$prizes['POINT'] = " . $prizes['POINT'] . $prize['v'] . ";");
                    }
                }

                if (isset($gamePrizes['hit'])) {
                    // combination
                    if (($format == 'hit' && isset($this->getPrizes()[$gamePrizes['hit']]) && $prize = $this->getPrizes()[$gamePrizes['hit']])
                        || ($format == 'miss' && $gamePrizes['hit'] == $this->getOptions('c') && isset($this->getPrizes()[count($gameField) - $gamePrizes['hit']]) && $prize = $this->getPrizes()[count($gameField) - $gamePrizes['hit']])
                    ) {
                        switch ($prize['t']) {
                            case 'item':
                                $prizes['ITEM'] = $prize['n'];
                                break;

                            case 'money':
                                $prizes['MONEY'] = (isset($prize['v']) && $prize['v'] ? $prize['v'] : null);
                                break;

                            case 'points':
                                $prizes['POINT'] = (isset($prize['v']) && $prize['v'] ? $prize['v'] : null);
                                break;
                        }
                        array_filter($prizes);
                    }
                }
            }

            $this->setGamePrizes($prizes);

            $res['GameField']  = $gameField;
            $res['GamePrizes'] = $prizes;
            $res['Moves']      = 0;
            if ($this->option('p'))
                $res['Price'] = $this->option('p');
            $this->setOver(1);
        }

        $res['comb'] = $this->getCombinations();

        return $res;
    }

    private function validCombination($cell, $prizeId, $chance, $force = false)
    {

        if ($options = $this->option('combination')) {

            $cell   = array_map('intval', explode('x', $cell));
            $format = $this->option('f');

            if (!$this->getCombinations()) {

                if ($chance) {

                    $size = (int)($format && $format != 'cell' ? $this->option('c') : count($this->getPrizes()));

                    foreach ($options as $option)
                        switch ($option) {
                            case 'random' :
                            case 'star' :
                            case 'square' :
                                break;
                            case 'snake' :
                                $this->genCombinationSnake($cell, $size);
                                $this->genCombinationSnake($cell, $size, null, array(), 2);
                                break;
                            case 'line' :
                                $this->genCombinationLine($cell, $size);
                                break;
                        }

                    $this->uniqueCombinations();
                    if (!$this->getCombinations())
                        $this->setCombinations(true);

                }

            } else {
                if (is_array($this->getCombinations())) {

                    $combinations = $this->filterCombinations($cell);

                    if (count($this->getCombinations()) == 1) {
                        $chance = count($combinations);
                    } else {
                        if (count($combinations) > 1) {
                            if ($chance || $force)
                                $this->setCombinations($this->filterCombinations($cell, !$chance));
                        } elseif (count($combinations) == 1) {

                            if ($chance)
                                foreach ($this->getPrizes() as $index => $prize) {
                                    if (!$format || $format == 'cell' || $index > $prizeId) {
                                        if (!($chance = ($prize['p'] ? !rand(0, $prize['p'] - 1) : false)))
                                            break;
                                    }
                                }
                            $this->setCombinations($this->filterCombinations($cell, !$chance));
                        } else {
                            $chance = false;
                        }
                    }
                }

                // if(count($this->getCombinations)==1)

                $chance = (in_array('random', $options) || count($this->getCombinations())) && $chance ? true : false;
            }
        }

        return $chance;
    }

    private function addCombination($combination)
    {
        $this->_combinations[] = $combination;

        return $this;
    }

    private function filterCombinations($cell, $reverse = false)
    {

        $combinations = $this->_combinations;
        foreach ($combinations as $index => $combination) {
            if ((!in_array($cell, $combination) && !$reverse) || (in_array($cell, $combination) && $reverse))
                unset($combinations[$index]);
        }

        return $combinations;
    }

    private function uniqueCombinations()
    {
        $combinations = array();
        if (is_array($this->_combinations))
            foreach ($this->_combinations as $combination)
                if (!in_array($combination, $combinations))
                    $combinations[] = $combination;

        return $this->_combinations = $combinations;
    }

    function genCombinationLine($cell, $size)
    {
        $changes = range(1, $size);
        $matrix  = $this->getMatrix('line');

        #4
        foreach ($matrix as $direction) {
            #3
            foreach ($changes as $change) {
                $coor = $cell;
                #2
                foreach ($direction as $path) {
                    $line = array($coor);
                    #1
                    for ($count = 1; $count <= $size; $count++) {

                        if ($change == $count) {
                            unset($change);
                            break;
                        } else
                            $coor = array($coor[0] + $path[0], $coor[1] + $path[1]);

                        if ($coor[0] > $this->option('x') || $coor[0] < 1
                            || $coor[1] > $this->option('y') || $coor[1] < 1
                            || isset($this->getGameField()[$coor[0] . 'x' . $coor[1]])
                        )
                            break 2;
                        else
                            $line[] = $coor;

                        if (count($line) === $size) {
                            sort($line);
                            $this->addCombination($line);
                            break 2;
                        }
                    }
                }
            }
        }
    }

    function genCombinationSnake($init = null, $size = null, $cell = null, $snake = array(), $reverse = null)
    {
        if (!isset($init)) return false;
        if (!isset($cell)) $cell = $init;
        if (!in_array($cell, $snake)) $snake[] = $cell;

        if (count($snake) === $reverse) {
            $this->genCombinationSnake($init, $size, $init, $snake);

            return false;
        } elseif (count($snake) === $size) {
            sort($snake);
            $this->addCombination($snake);

            return false;
        }

        $matrix = $this->getMatrix('snake');

        foreach ($matrix as $path) {

            $temp = array($cell[0] + $path[0], $cell[1] + $path[1]);

            if ($temp[0] > $this->option('x') || $temp[0] < 1
                || $temp[1] > $this->option('y') || $temp[1] < 1
                || isset($this->getGameField()[$temp[0] . 'x' . $temp[1]])
                || in_array(array($temp[0], $temp[1]), $snake)
            ) {
                continue;
            } else
                $this->genCombinationSnake($init, $size, $temp, $snake, $reverse);
        }
    }

    private function genCombination()
    {

        if ($this->option('f') && $this->option('f') != 'cell') {
            $must   = $this->option('c') - $this->getGamePrizes()['hit'];
            $prizes = array_pad(array(), $must, array('t' => 'hit'));
        } else
            $must = count($prizes = $this->getPrizes());

        $gameField = $this->getGameField();

        if ($must) {
            $options = $this->option('combination') ?: array('random');
            shuffle($options);
            foreach ($options as $option) {
                switch ($option) {

                    case 'square' :
                    case 'snake' :
                    case 'line' :

                        if (!$this->getCombinations()) {

                            $xs = range(1, $this->option('x'));
                            $ys = range(1, $this->option('y'));
                            shuffle($xs);
                            shuffle($ys);

                            foreach ($xs as $x)
                                foreach ($ys as $y)
                                    if (!(isset($gameField[$x . 'x' . $y]))) {

                                        if (in_array('snake', $options)) {
                                            $this->genCombinationSnake(array($x, $y), $must);
                                        }

                                        if (in_array('line', $options))
                                            $this->genCombinationLine(array($x, $y), $must);

                                        if ($this->getCombinations())
                                            break 2;
                                    }
                        }

                        if (count($combinations = $this->getCombinations()) && is_array($combinations)) {
                            shuffle($combinations);
                            $combination = end($combinations);
                            $i           = 100;
                            while ($i && $must) {
                                $i--;
                                foreach ($prizes as $prize) {
                                    foreach ($combination as $index => $cell) {
                                        list($x, $y) = $cell;
                                        unset($combination[$index]);
                                        if (!(isset($gameField[$x . 'x' . $y]))) {
                                            $gameField[$x . 'x' . $y] = $prize;
                                            $must--;
                                            break;
                                        }
                                    }
                                }
                            }
                        }
                        break;

                    case 'random' :
                    default:
                        $xs = range(1, $this->option('x'));
                        $ys = range(1, $this->option('y'));

                        while ($must)
                            foreach ($prizes as $prize) {

                                shuffle($xs);
                                shuffle($ys);

                                unset($prize['p']);
                                foreach ($xs as $x)
                                    foreach ($ys as $y) {
                                        if (!(isset($gameField[$x . 'x' . $y]))) {
                                            $gameField[$x . 'x' . $y] = $prize;
                                            $must--;
                                            break 2;
                                        }
                                    }
                            }
                        break;
                }
            }
        }

        return $gameField;
    }


}
