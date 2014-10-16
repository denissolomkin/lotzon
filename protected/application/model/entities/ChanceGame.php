<?php 

class ChanceGame extends Entity 
{
    private $_identifier = '';
    private $_minFrom = 0;
    private $_minTo = 0;
    private $_prizes = array();
    private $_gameTitle = '';
    private $_gamePrice = 0;
    private $_pointsWin = 0;
    private $_triesCount = 0;

    public function init()
    {
        $this->setModelClass('ChanceGamesModel');
    }

    public function setIdentifier($identity)
    {
        $this->_identifier = $identity;

        return $this;
    }

    public function getIdentifier()
    {
        return $this->_identifier;
    }

    public function setMinFrom($minFrom)
    {
        $this->_minFrom = $minFrom;

        return $this;
    }

    public function getMinFrom()
    {
        return $this->_minFrom;
    }

    public function setMinTo($minTo)
    {
        $this->_minTo = $minTo;

        return $this;
    }

    public function getMinTo()
    {
        return $this->_minTo;
    }

    public function setPrizes($prizes)
    {
        $this->_prizes = $prizes;

        return $this;
    }

    public function getPrizes()
    {
        return $this->_prizes;
    }

    public function setGameTitle($gameTitle)
    {
        $this->_gameTitle = $gameTitle;

        return $this;
    }

    public function getGameTitle()
    {
        return $this->_gameTitle;
    }

    public function setGamePrice($gamePrice)
    {
        $this->_gamePrice = $gamePrice;

        return $this;
    }

    public function getGamePrice()
    {
        return $this->_gamePrice;
    }

    public function setPointsWin($pointsWin)
    {
        $this->_pointsWin = $pointsWin;

        return $this;
    }

    public function getPointsWin()
    {
        return $this->_pointsWin;
    }

    public function setTriesCount($triesCount)
    {
        $this->_triesCount = $triesCount;

        return $this;
    }

    public function getTriesCount()
    {
        return $this->_triesCount;
    }

    public function save()
    {
        try {
            $model = $this->getModelClass();
            $model::instance()->save($this);
        }  catch (ModelException $e) {
            throw new EntityException($e->getMessage(), $e->getCode());
        }

        return $this;
    }

    public function loadPrizes()
    {
        $prizes = array();
        if ($this->getPrizes()) {
            foreach ($this->getPrizes() as $prizeId) {
                $prize = new ShopItem();
                $prize->setId($prizeId)->fetch();

                $prizes[$prize->getId()] = $prize;
            }
        }

        return $prizes;
    }

    public function generateGame() {
        switch ($this->getIdentifier()) {
            case 'moment':
                return $this->generateMoment();    
            break;
            case '33':
                return $this->generate3x3();    
            break;
            case '44':
                return $this->generate4x4();    
            break;
            case '55':
                return $this->generate5x5();    
            break;
        }
    }

    protected function generateMoment() {
        $game = array();
        
        $rand = mt_rand(1,3);
        for ($i = 1; $i<3; ++$i) {
            if ($i == $rand) {
                $game[] = 1;
            } else {
                $game[] = 0;
            }
        }

        return $game;
    }

    protected function generate3x3() {
        $gameField = $path  = array();

        for ($i = 1; $i <= 3 ; ++$i) { 
            for ($j = 1; $j <= 3; ++$j) {
                $gameField[$i][$j] = 0;                
            }
        }
        // get random cell
        $cell = array(mt_rand(1,3), mt_rand(1,3));
        // add cell to path
        $path[] = $cell;
        
        $turnRand = mt_rand(0,100) < 70;
        while (count($path) != 3) {
            if ($pathCell = $this->followPath($path[count($path) - 1], $gameField, $path, $turnRand)) {
                $path[] = $pathCell;                
            } else {
                $pathCell = $this->followPath($path[count($path) - 1], $gameField, $path, $turnRand);
                if (!$pathCell) {
                    $path = array($cell);    
                } else {
                    $path[] = $pathCell;    
                }
            }
        }

        // add path to gameField
        $fst = true;
        foreach ($path as $step => $pathCell) {
            $gameField[$pathCell[0]][$pathCell[1]] = 1;
        }

        return $gameField;
    }

    protected function generate4x4() {
        $gameField = $path = array();

        for ($i = 1; $i <= 4 ; ++$i) { 
            for ($j = 1; $j <= 4; ++$j) {
                $gameField[$i][$j] = 0;                
            }
        }
        // get random cell
        $cell = array(mt_rand(1,4), mt_rand(1,4));
        // add cell to path
        $path[] = $cell;
        
        while (count($path) != 3) {
            if ($pathCell = $this->followPath($path[count($path) - 1], $gameField, $path, false)) {
                $path[] = $pathCell;                
            } else {
                $pathCell = $this->followPath($path[count($path) - 1], $gameField, $path, false);
                if (!$pathCell) {
                    $path = array($cell);    
                } else {
                    $path[] = $pathCell;    
                }
            }
        }
        
        // add path to gameField
        $fst = true;
        foreach ($path as $step => $pathCell) {
            $gameField[$pathCell[0]][$pathCell[1]] = 1;
        }

        return $gameField;
    }

    public function generate5x5() 
    {
        $gameField = array();
        // generate field
        for ($i = 1; $i <= 5 ; ++$i) { 
            for ($j = 1; $j <= 5; ++$j) {
                $gameField[$i][$j] = 0;                
            }
        }
        // paste unique variants
        $cells = array();
        while (count($cells) != 5) {
            $cell = array(mt_rand(1,5), mt_rand(1,5));
            if (!isset($cells[$cell[0].$cell[1]])) {
                $cells[$cell[0].$cell[1]] = $cell;
            }
        }
        foreach ($cells as $cell) {
            $gameField[$cell[0]][$cell[1]] = 1;
        }

        return $gameField;
    }

    protected function followPath($cell, $field, &$path, $turnsAllowed = true)
    {
        $neibs = array();
        $neibsVariants = array(
            //x-1, y -1
            array($cell[0] -1, $cell[1] -1),
            //x, y -1
            array($cell[0], $cell[1] -1),
            //x+1, y-1
            array($cell[0] + 1, $cell[1] - 1),
            // x-1, y
            array($cell[0] - 1, $cell[1]),
            // x+1 ,y
            array($cell[0] + 1, $cell[1]),
            // x-1 ,y+1
            array($cell[0] - 1, $cell[1] + 1),
            // x ,y+1
            array($cell[0], $cell[1] + 1),
            // x ,y+1
            array($cell[0] + 1, $cell[1] + 1),
        );

        foreach ($neibsVariants as $id => $neibCell) {
            // if out of bonds
            if (!isset($field[$neibCell[0]][$neibCell[1]])) {
                continue;
            }
            // if already in path
            $in_path = false;
            foreach ($path as $pathCell) {
                if ($neibCell[0] == $pathCell[0] && $neibCell[1] == $pathCell[1]) {
                    $in_path = true;
                }   
            }
            if (!$in_path) {
                $neibs[] = $neibCell;    
            }
        }

        $found = false;
        $prevCell = @$path[count($path) - 2];
        
        while (!$found) {
            $random = array_rand($neibs);
            $candidat = $neibs[$random];
            
            if ($turnsAllowed) {
                if ($prevCell) {
                    if ((abs($candidat[0] - $prevCell[0]) == 2 && abs($candidat[1] - $prevCell[1]) != 2) ||
                        (abs($candidat[1] - $prevCell[1]) == 2 && abs($candidat[0] - $prevCell[0]) != 2)) {
                        // delete this cell and continue                        
                        unset($neibs[$random]);                        
                    }  else {
                        $found = true;
                    }
                } else {
                    $found = true;
                }    
            } else {
                if ($prevCell) {
                    $xdirection = $cell[0] - $prevCell[0];
                    $ydirection = $cell[1] - $prevCell[1];

                    // follow previous direction
                    if (($candidat[0] - $cell[0]) != $xdirection || ($candidat[1] - $cell[1]) != $ydirection) {
                        unset($neibs[$random]);

                        // if no more variant reverse path
                        if (!count($neibs)) {
                            $path = array_reverse($path);
                            return false;
                        }
                    } else {
                        $found = true;
                    }
                } else {
                    $found = true;
                }
            }
        }

        return $candidat;
    }
}
