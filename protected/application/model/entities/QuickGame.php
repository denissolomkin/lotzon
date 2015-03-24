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
        return array(
            'Title'=>$this->getTitle($this->getLang()),
            'Description'=>$this->getDescription($this->getLang()),
            'Uid'=>$this->getUid(),
            'Id'=>$this->getId(),
            'Key'=>$this->getKey(),
            'Timeout'=> $this->getTimeout() ? ($this->getTimeout() * 60 + $this->getTime()) - time(): false,
            'Field' => $field,
            'GameField' => $this->getGameField());
    }

    public function doMove($cell) {

        $res=array('Uid'=>$this->getUid());
        $gameField  = $this->getGameField();

        if(isset($gameField[$cell]))
            return $res+array('error'=>'CELL_IS_PLAYED');

        if(count($gameField)==$this->getField()['c'])
            return $res+array('error'=>'GAME_IS_OVER');

        $res['Prize']=$gameField[$cell]=false;
        $res['Cell']=$cell;

        $gamePrizes=$this->getGamePrizes();

        if(is_array($prizes=$this->getPrizes()) && !empty($prizes)) {
            shuffle($prizes);

            foreach ($prizes as $index => $prize) {
                if ((!rand(0, $prize['p'] - 1) AND $prize['p']) OR
                    count($prizes) - 1 == ($this->getField()['x'] * $this->getField()['y'] - count($gameField))
                ) {

                    unset($prize['p']);

                    $gamePrizes[$prize['t']][] = $prize;
                    $this->setGamePrizes($gamePrizes);
                    unset($prizes[$index]);
                    $res['Prize'] = $gameField[$cell] = $prize;
                    $this->setPrizes($prizes);

                    break;
                }
            }
        }

        $this->setGameField($gameField);

        /* end game */
        if(count($gameField)==$this->getField()['c']){

            $xs = range(1, $this->getField()['x']);
            $ys = range(1, $this->getField()['y']);
            shuffle($xs);
            shuffle($ys);

            if(!empty($prizes))
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
                    $prizes['ITEM']='';
                    foreach ($gamePrizes['item'] as $prize)
                        $prizes['ITEM'] .= ' '.$prize['n'];
                }

                if(isset($gamePrizes['math']))
                    foreach($gamePrizes['math'] as $prize) {
                        //print_r($prize);
                        if(isset($prizes['MONEY']) && $prizes['MONEY']!=0)
                           eval("\$prizes['MONEY'] = ".$prizes['MONEY'].$prize['v'].";");
                        if(isset($prizes['POINT']) && $prizes['POINT']!=0)
                            eval("\$prizes['POINT'] = ".$prizes['POINT'].$prize['v'].";");
                            //$prizes['points'] = eval($prizes['points'].$prize['v']);
                    }
            }

            $this->setGamePrizes($prizes);
            $res['GameField']=$gameField;
            $res['GamePrizes']=$prizes;
            if($this->getOption('p'))
                $res['Price']=$this->getOption('p');
            $this->setOver(1);
        }
        return $res;
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
