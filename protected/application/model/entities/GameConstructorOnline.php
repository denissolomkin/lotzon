<?php

class GameConstructorOnline extends GameConstructor
{
    public function initVariation($inputVariation = array())
    {
        $variations = $this->getOptions('Variations') ?: array();
        foreach ($variations as $key => &$variation) {
            if (isset($inputVariation[$key]) && isset($variation['v'][$inputVariation[$key]]))
                $variation = $inputVariation[$key];
            elseif (isset($variation['d']))
                $variation = $variation['d'];
            else
                $variation = key($variation['v']);
        }

        return $variations;
    }

    public function isMode($mode)
    {
        $mode = explode('-', $mode);

        return (isset($this->getModes()[$mode[0]][$mode[1]]) && (!isset($mode[2]) || ($mode[2] >= 2 && $mode[2] <= $this->getOptions('p'))));
    }

    public function checkMode($mode)
    {
        $mode = explode('-', $mode);

        return (isset($this->getModes()[$mode[0]][$mode[1]]) && (!isset($mode[2]) || ($mode[2] >= 2 && $mode[2] <= $this->getOptions('p'))));
    }

    function exportModes()
    {
        $modes = $this->getModes();

        return (is_array($modes) && array_walk($modes, function (&$value, $index) {
            $value = array_keys($value);
        }) ? $modes : null);
    }

}
