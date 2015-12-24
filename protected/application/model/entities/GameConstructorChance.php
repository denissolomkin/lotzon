<?php
use \ShopItem;

class GameConstructorChance extends GameConstructor
{

    protected $_time         = '';
    protected $_timeout      = '';
    protected $_combinations = array();

    /* todo replace to getOptions & addOptions in Chances */
    public function option($key, $value = null)
    {
        if ($value)
            $this->_field[$key] += $value;

        return $this->_field[$key];
    }


}
