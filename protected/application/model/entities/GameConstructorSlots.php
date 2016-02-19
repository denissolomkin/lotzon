<?php

class GameConstructorSlots extends GameConstructor
{

    protected $_time = null;
    protected $_timeout = null;
    protected $_bet = null;
    protected $_currency = null;
    protected $_combinations = array(

        /* slots
         * 1 - banana
         * 2 - seven
         * 3 - watermelon
         * 4 - bar
         * 5 - cherry
         * 6 - jackpot
         * */

        '*7' => array(
            array(1, 1, 3),
            array(1, 3, 3),
            array(1, 1, 5),
            array(1, 5, 5),
            array(1, 3, 5),
            array(3, 3, 5),
            array(3, 5, 5)
        ),
        '*12' => array(3, 3, 3),
        '*14' => array(1, 1, 1),
        '*15' => array(5, 5, 5),
        '*16' => array(
            array(1, 2, 4),
            array(2, 3, 4),
            array(1, 4, 5),
            array(3, 4, 5),
            array(1, 2, 6),
            array(2, 3, 6),
            array(1, 5, 6),
            array(3, 5, 6)
        ),
        '*20' => array(2, 2, 2),
        '*50' => array(4, 4, 4),
        '*200' => array(6, 6, 6)

    );

    public function doMove()
    {

        $gamePrizes = $prizes = array();

        if (is_array($prizes = $this->getPrizes()) && !empty($prizes)) {

            shuffle($prizes);
            foreach ($prizes as $prizeId => $prize) {
                if ($this->getCombinations($prize['v']) && (isset($prizes[$prizeId]) ? ($prizes[$prizeId]['p'] ? !rand(0, $prizes[$prizeId]['p'] - 1) : false) : false)) {

                    unset($prize['p']);

                    switch ($prize['v'][0]) {
                        case '*':
                            $win = ($this->getBet() * substr($prize['v'], 1));
                            break;
                        case '/':
                            $win = ($this->getBet() / substr($prize['v'], 1));
                            break;
                    }

                    $gamePrizes[$prize['t']] = $prize;
                    $gamePrizes[$this->getCurrency()] = $win;

                    $this->setGamePrizes($gamePrizes);

                    $combination = $this->getCombinations($prize['v']);
                    $combination = is_array(reset($combination))
                        ? (shuffle($combination) ? array_shift($combination) : false)
                        : $combination;
                    shuffle($combination);
                    $this->setGameField($combination);

                    break;
                }
            }
        }

        /* end game */

        if (empty($this->getGameField())) {
            $this->setGameField($this->genCombination());
        }

        $res = array(
            'Uid' => $this->getUid(),
            'GameField' => $this->getGameField(),
            'GamePrizes' => $this->getGamePrizes(),
            'Win' => $win
        );

        $this->setOver(1);

        return $res;
    }

    protected function genCombination()
    {
        do {
            $comb = array();
            while (count($comb) != 3)
                $comb[] = rand(1, 6);
            sort($comb);
        } while ($this->checkCombination($this->getCombinations(), $comb));

        shuffle($comb);

        return $comb;
    }

    protected function checkCombination($haystack, $needle)
    {
        foreach ($haystack as $value) {
            if ((is_array($value) && $this->checkCombination($value, $needle) !== false) || $needle === $value) {
                return true;
            }
        }

        return false;
    }


}
