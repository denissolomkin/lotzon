<?php
use \ShopItem;

class QuickGame extends Entity
{
    private $_id = '';
    private $_uid = '';
    private $_key = '';
    private $_title = '';
    private $_time = '';
    private $_timeout = '';
    private $_over = 0;
    private $_lang = '';
    private $_description = '';
    private $_enabled = true;
    private $_prizes = array();
    private $_field= array();
    private $_gameField= array();
    private $_gamePrizes= array();

    private $_matrix =array(
        'line' => array(
            array(array(-1, -1), array(1, 1)), # \
            array(array(0, -1), array(0, 1)), # |
            array(array(1, -1), array(-1, 1)), # /
            array(array(1, 0), array(-1, 0)), # -
        ),

        'snake' => array(
            array(0, -1), array(0, 1), array(1, 0), array(-1, 0) # +
        ),
    );

    public function init()
    {
        $this->setModelClass('QuickGamesModel');
    }

    public function setId($id)
    {
        $this->_id = $id;

        return $this;
    }

    public function getId()
    {
        return $this->_id;
    }

    public function setKey($key)
    {
        $this->_key = $key;

        return $this;
    }

    public function getKey()
    {
        return $this->_key;
    }

    public function setLang($lang)
    {
        $this->_lang = $lang;

        return $this;
    }

    public function getLang()
    {
        return $this->_lang;
    }
    public function setUid($uid)
    {
        $this->_uid = $uid;

        return $this;
    }

    public function getUid()
    {
        return $this->_uid;
    }

    public function setEnabled($enabled)
    {
        $this->_enabled = $enabled;
        return $this;
    }

    public function isEnabled()
    {
        return $this->_enabled;
    }

    public function setField($field)
    {
        $this->_field = $field;

        return $this;
    }

    public function getField()
    {
        return $this->_field;
    }

    public function getOption($key)
    {
        return $this->_field[$key];
    }

    public function option($key, $value=null)
    {
        if($value)
            $this->_field[$key] += $value;

        return $this->_field[$key];
    }

    public function setGameField($field)
    {
        $this->_gameField = $field;

        return $this;
    }

    public function getGameField()
    {
        return $this->_gameField;
    }

    public function setGamePrizes($prizes)
    {
        $this->_gamePrizes = $prizes;

        return $this;
    }

    public function getGamePrizes()
    {
        return $this->_gamePrizes;
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

    public function setDescription($description)
    {
        $this->_description = $description;
        return $this;
    }

    public function getDescription($lang=false)
    {

        if($lang) {
            if(isset($this->_description[$lang]) && $this->_description[$lang] && $this->_description[$lang]!='')
                $description = nl2br($this->_description[$lang]);
            else
                $description = nl2br(reset($this->_description));;
        } else
            $description = $this->_description;

        return $description;
    }

    public function setOver($over)
    {
        $this->_over = $over;

        return $this;
    }

    public function isOver()
    {
        return $this->_over;
    }

    public function setTitle($title)
    {
        $this->_title = $title;

        return $this;
    }


    public function getTitle($lang=false)
    {
        if($lang) {
            if(isset($this->_title[$lang]) && $this->_title[$lang] && $this->_title[$lang]!='')
                $title = $this->_title[$lang];
            else
                $title = reset($this->_title);
        } else
            $title = $this->_title;

        return $title;
    }

    public function setTime($time)
    {
        $this->_time=$time?:time();
        return $this;
    }

    public function getTime()
    {
        return $this->_time;
    }

    public function setTimeout($time)
    {
        $this->_timeout=$time;
        return $this;
    }

    public function getTimeout()
    {
        return $this->_timeout;
    }

    public function setUserId($id)
    {
        $this->_userId=$id;
        return $this;
    }

    public function getUserId()
    {
        return $this->_userId;
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

    public function saveGame() {
    }

    function getStat() {
        $field = $this->getField();
        $field['c']-=count($this->getGameField());
        unset($field['combination']);

        return array(
            'Title'=>$this->getTitle($this->getLang()),
            'Description'=>$this->getDescription($this->getLang()),
            'Uid'=>$this->getUid(),
            'Id'=>$this->getId(),
            'Key'=>$this->getKey(),
            'Timeout'=> $this->getTimeout() ? ($this->getTimeout() * 60 + $this->getTime()) - time() : false,
            'Field' => $field,
            'GameField' => $this->getGameField());
    }

    public function doMove($cell) {

        $res = array('Uid'=>$this->getUid());
        $gameField  = $this->getGameField();
        $gamePrizes = $this->getGamePrizes();

        if(isset($gameField[$cell]))
            return $res+array('error'=>'CELL_IS_PLAYED');

        if( (!$this->option('combination') && count($gameField)==$this->option('c'))
            || ($this->option('combination') && $gamePrizes['hit']==$this->option('c'))
            || $this->option('m')===0)
            return $res+array('error'=>'GAME_IS_OVER');

        $res['Prize'] = $gameField[$cell] = false;
        $res['Cell']  = $cell;
        $res['Moves'] = $this->option('combination') ? $this->option('m') : ($this->option('c')-count($gameField));


        if (is_array($prizes = $this->getPrizes()) && !empty($prizes)) {
            if($this->option('combination')) {

                if ($this->validCombination($cell, $gamePrizes['hit']+1) ){
                    // hit
                    $gamePrizes['hit']=$gamePrizes['hit']+1;
                    $this->setGamePrizes($gamePrizes);
                    $res['Prize'] = $gameField[$cell] = array('t'=>'hit');
                } else {
                    // miss
                    $res['Moves'] = $this->option('m',-1);
                }

            } else {

                shuffle($prizes);
                $miss=true;
                foreach ($prizes as $index => $prize) {
                    if ($this->validCombination($cell, $index) OR
                        count($prizes) - 1 == ($this->getField()['x'] * $this->getField()['y'] - count($gameField))) {

                        unset($prize['p']);
                        $miss=false;

                        $gamePrizes[$prize['t']][] = $prize;
                        $this->setGamePrizes($gamePrizes);
                        unset($prizes[$index]);
                        $res['Prize'] = $gameField[$cell] = $prize;
                        $this->setPrizes($prizes);

                        break;
                    }
                }

                // miss
                if ($miss==true)
                    $this->option('m',-1);
            }
        }

        $this->setGameField($gameField);
        
        /* end game */
        if( (!$this->option('combination') && count($gameField)>=$this->option('c'))
            || ($this->option('combination') && $gamePrizes['hit']>=$this->option('c'))
            || $this->option('m')===0){

            /*
            if(!empty($prizes) && !$this->option('combination')){

                $xs = range(1, $this->option('x'));
                $ys = range(1, $this->option('y'));
                shuffle($xs);
                shuffle($ys);

                foreach($prizes as $prize) {
                    unset($prize['p']);
                    foreach ($xs as $x) {
                        foreach ($ys as $y) {
                            if (!(isset($gameField[$x . 'x' . $y]))) {
                                $gameField[$x . 'x' . $y] = $prize;
                                break 2;
                            }
                        }
                    }
                }
            } elseif ($this->option('combination') && $gamePrizes['hit']!=$this->option('c')){
                $gameField = $this->genCombination();
            }
            */

            $gameField = $this->genCombination();
            $prizes=array();

            if(is_array($gamePrizes)){

                if(isset($gamePrizes['points'])) {
                    $prizes['POINT']=0;
                    foreach ($gamePrizes['points'] as $prize)
                        $prizes['POINT'] += (isset($prize['v']) ? $prize['v'] : 0);
                }

                if(isset($gamePrizes['money'])) {
                    $prizes['MONEY']=0;
                    foreach ($gamePrizes['money'] as $prize)
                        $prizes['MONEY'] += (isset($prize['v']) ? $prize['v'] : 0);
                }

                if(isset($gamePrizes['item'])) {
                    // item prize
                    $prizes['ITEM']='';
                    foreach ($gamePrizes['item'] as $prize)
                        $prizes['ITEM'] .= ' '.$prize['n'];
                }

                if(isset($gamePrizes['math'])){
                    // math function
                    foreach($gamePrizes['math'] as $prize) {
                        if(isset($prizes['MONEY']) && $prizes['MONEY']!=0)
                            eval("\$prizes['MONEY'] = ".$prizes['MONEY'].$prize['v'].";");
                        if(isset($prizes['POINT']) && $prizes['POINT']!=0)
                            eval("\$prizes['POINT'] = ".$prizes['POINT'].$prize['v'].";");
                    }
                }

                if(isset($gamePrizes['hit'])) {
                    // combination
                    if(isset($this->getPrizes()[$gamePrizes['hit']]) && $prize=$this->getPrizes()[$gamePrizes['hit']]){
                        switch($prize['t']) {

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

            $res['GameField']=$gameField;
            $res['GamePrizes']=$prizes;
            $res['Moves'] = 0;
            if($this->option('p'))
                $res['Price']=$this->option('p');
            $this->setOver(1);
        }
        $res['comb']=$this->getCombinations();
        return $res;
    }
    
    private function validCombination($cell,$prizeId)
    {

        $cell=array_map('intval', explode('x', $cell ));
        $options = $this->option('combination');
        $prizes=$this->getPrizes();
        $chance = isset($prizes[$prizeId]) ? ($prizes[$prizeId]['p'] ? !rand(0, $prizes[$prizeId]['p'] - 1) : false ) : false;

        if($options) {
            if (!$this->getCombinations()) {

                $size = (int) ($options ? $this->option('c') : count($this->getPrizes()));
                foreach ($options as $option)
                    switch ($option) {
                        case 'random' :
                            break;
                        case 'square' :
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

                $chance = true;

            } else {

                if (is_array($this->getCombinations())){

                    $combinations = $this->filterCombinations($cell);

                    if(count($this->getCombinations())==1){
                        $chance = count($combinations);
                    } else {
                        if (count($combinations) > 1) {
                            $this->setCombinations($combinations, !$chance);
                        } elseif (count($combinations) == 1) {

                            if($chance)
                                foreach($prizes as $index=>$prize){
                                    if($index>$prizeId){
                                        if(!($chance = $prize['p'] ? !rand(0, $prize['p'] - 1) : false))
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

    private function addCombination($combination){
        $this->_combinations[]=$combination;
        return $this;
    }

    private function setCombinations($combinations){
        $this->_combinations=$combinations;
        return $this;
    }

    private function getCombinations(){
        return $this->_combinations;
    }

    private function filterCombinations($cell, $reverse=false){

        $combinations=$this->_combinations;
        foreach($combinations as $index=>$combination) {
            if ((!in_array($cell, $combination) && !$reverse) || (in_array($cell, $combination) && $reverse))
                unset($combinations[$index]);
        }
        return $combinations;
    }

    private function uniqueCombinations(){
        $combinations=array();
        if(is_array($this->_combinations))
            foreach($this->_combinations as $combination)
            if(!in_array($combination, $combinations))
                $combinations[]=$combination;
        return $this->_combinations=$combinations;
    }
    
    function genCombinationLine($cell, $size)
    {
        $changes = range(1, $size);
        $matrix = $this->_matrix['line'];

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

                        if ($coor[0]  > $this->option('x') || $coor[0]  < 1 || $coor[1] > $this->option('y') || $coor[1] < 1)
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

    function genCombinationSnake($init=null, $size = null, $cell = null, $snake=array(), $reverse=null)
    {
        if(!isset($init)) return false;
        if(!isset($cell)) $cell = $init;
        if(!in_array($cell, $snake)) $snake[]=$cell;

        $matrix = $this->_matrix['snake'];

        foreach ($matrix as $path) {

            $temp = array($cell[0] + $path[0], $cell[1] + $path[1]);

            if ($temp[0] > $this->option('x') || $temp[0] < 1
                || $temp[1] > $this->option('y') || $temp[1] < 1
                || in_array(array($temp[0], $temp[1]), $snake)) {
                continue;
            } elseif (count($snake) == $reverse) {
                $this->genCombinationSnake($init, $size, $init, $snake);
                return false;
            } elseif (count($snake) === $size) {
                sort($snake);
                $this->addCombination($snake);
                return false;
            } else
                $this->genCombinationSnake($init, $size, $temp, $snake, $reverse);
        }
    }

    private function genCombination()
    {

        if($this->option('combination')) {
            $must = $this->option('c') - $this->getGamePrizes()['hit'];
            $prizes = array_pad(array(), $must, array('t'=>'hit'));
        } else
            $must = count($prizes = $this->getPrizes());

        $gameField = $this->getGameField();

        if($must) {
            $options = $this->option('combination') ? : array('random');
            shuffle($options);

                foreach ($options as $option) {
                    switch ($option) {

                        case 'square' :
                        case 'snake' :
                        case 'line' :
                            if(count($combinations=$this->getCombinations())){
                                shuffle($combinations);
                                $combination = end($combinations);
                                while ($must)
                                    foreach ($prizes as $prize) {
                                    foreach ($combination as $index=>$cell) {
                                        list($x,$y)=$cell;
                                        unset($combination[$index]);
                                        if (!(isset($gameField[$x . 'x' . $y]))) {
                                            $gameField[$x . 'x' . $y] = $prize;
                                            $must--;
                                            break;
                                        }}}}
                        break;

                        case 'random' :
                        default:
                            $xs = range(1, $this->option('x'));
                            $ys = range(1, $this->option('y'));
                            shuffle($xs);
                            shuffle($ys);

                            while ($must)
                                foreach ($prizes as $prize) {
                                unset($prize['p']);
                                foreach ($xs as $x)
                                    foreach ($ys as $y)
                                        if (!(isset($gameField[$x . 'x' . $y]))) {
                                            $gameField[$x . 'x' . $y] = $prize;
                                            $must--;
                                            break 2;
                                        }
                            }
                            break;
                    }
                }

        }

        return $gameField;
    }

    public function loadPrizes()
    {
        if ($prizes = $this->getPrizes()) {
            foreach ($prizes as &$prize) {
                if($prize['t']=='item') {
                    $item = new ShopItem();
                    try {
                        $item->setId($prize['v'])->fetch();
                        $prize['s'] = $item->getImage();
                        $prize['n'] = $item->getTitle();
                    } catch (EntityException $e) {
                    }
                }
            }

            $this->setPrizes($prizes);
        }

        return $this;
    }

    public function formatFrom($from, $data)
    {
        if ($from == 'DB') {
            $this->setId($data['Id'])
                ->setTitle(@unserialize($data['Title']))
                ->setDescription(@unserialize($data['Description']))
                ->setPrizes(@unserialize($data['Prizes']))
                ->setField(@unserialize($data['Field']))
                ->setEnabled($data['Enabled']);
        }

        return $this;
    }
}
