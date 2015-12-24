<?php
use \ShopItem;

class GameConstructor extends Entity
{
    protected $_id           = '';
    protected $_uid          = '';
    protected $_key          = '';
    protected $_lang         = '';
    protected $_type         = '';
    protected $_title        = array();
    protected $_description  = array();
    protected $_enabled      = true;
    protected $_field        = array();
    protected $_modes        = array();
    protected $_options      = array();
    protected $_prizes       = array();
    protected $_loadedPrizes = array();
    protected $_audio        = array();

    protected $_gameField  = array();
    protected $_gamePrizes = array();

    protected $_over   = false;
    protected $_userId = 0;

    public function init()
    {
        $this->setModelClass('GameConstructorModel');
    }

    public function validate()
    {
        return true;
    }

    function exportField()
    {
        $field = $this->getField();
        $field['c'] -= count($this->getGameField());
        unset($field['combination']);

        return $field;
    }

    function exportModes()
    {
        $modes = $this->getModes();

        return (is_array($modes) && array_walk($modes, function (&$value, $index) {
            $value = array_keys($value);
        }) ? $modes : null);
    }

    public function getVariations($lang)
    {
        $variations = $this->getOptions('Variations') ?: array();

        foreach ($variations as &$variation) {
            $variation['t'] = isset($variation['t'][$lang]) && $variation['t'][$lang] != ''
                ? $variation['t'][$lang]
                : reset($variation['t']);

            if (is_array($variation['v']))
                foreach ($variation['v'] as &$value) {

                    $value = isset($value['t'][$lang]) && $value['t'][$lang] != ''
                        ? $value['t'][$lang]
                        : reset($value['t']);

                }
        }

        return $variations;
    }

    function exportPrizes()
    {
        if (is_array($this->getPrizes()))
            foreach ($this->getPrizes() as $prize) {
                if ($prize['v']) {
                    unset($prize['p']);
                    $prizes[] = $prize;
                }
            }

        if (is_array($this->getGamePrizes()))
            foreach ($this->getGamePrizes() as $gamePrizes)
                if (is_array($gamePrizes))
                    foreach ($gamePrizes as $prize) {
                        if ($prize['v'])
                            $prizes[] = $prize;
                    }

        return $prizes;
    }

    public function loadPrizes()
    {
        if (($prizes = $this->getPrizes())) {
            foreach ($prizes as &$prize) {
                if ($prize['t'] == 'item') {
                    $item = new ShopItem();
                    try {
                        $item->setId($prize['v'])->fetch();
                        $prize['s'] = $item->getImage();
                        $prize['n'] = $item->getTitle();
                    } catch (EntityException $e) {
                        throw new Exception("Internal error", 500);
                    }
                }
            }

            $this->setPrizes($prizes);
            $this->setLoadedPrizes($prizes);
        }

        return $this;
    }

    public function filterAudio()
    {
        if (is_array($this->getAudio()))
            $this->setAudio(array_filter($this->getAudio()));

        return $this;
    }

    public function export($to)
    {

        switch ($to) {

            case 'list':
                $ret = array(
                    'id'    => $this->getId(),
                    'title' => $this->getTitle($this->getLang()),
                    'key'   => $this->getKey()
                );

                if ($this->getType() == 'chance') {
                    $ret += array(
                        'prizes' => $this->loadPrizes()->exportPrizes()
                    );
                }

                break;

            case 'item':
                $ret = array(
                    'id'          => $this->getId(),
                    'title'       => $this->getTitle($this->getLang()),
                    'description' => $this->getDescription($this->getLang()),
                    'key'         => $this->getKey(),
                    'audio'       => $this->getAudio()
                );

                if ($this->getType() == 'online') {

                    $ret += array(
                        'variations' => $this->getVariations($this->getLang()),
                        'modes'      => $this->exportModes(),
                        'maxPlayers' => $this->getOptions('p'),
                        'create'     => $this->getOptions('f'));

                } else if ($this->getType() == 'chance') {

                    $ret += array(
                        'prizes' => $this->loadPrizes()->exportPrizes(),
                        'field'  => $this->exportField()
                    );

                }

                break;

            case 'stat':
                $ret = array(
                    'Title'       => $this->getTitle($this->getLang()),
                    'Description' => $this->getDescription($this->getLang()),
                    'Prizes'      => $this->loadPrizes()->exportPrizes(),
                    'Audio'       => $this->getAudio(),
                    'Uid'         => $this->getUid(),
                    'Id'          => $this->getId(),
                    'Key'         => $this->getKey(),
                    'Timeout'     => $this->getTimeout() ? ($this->getTimeout() * 60 + $this->getTime()) - time() : false,
                    'Field'       => $this->exportField(),
                    'GameField'   => $this->getGameField()
                );
                break;

            case 'DB':
                $ret = array(
                    'Id'          => $this->getId(),
                    'Key'         => $this->getKey(),
                    'Type'        => $this->getType(),
                    'Title'       => @serialize($this->getTitle()),
                    'Description' => @serialize($this->getDescription()),
                    'Audio'       => @serialize($this->getAudio()),
                    'Field'       => @serialize($this->getField()),
                    'Prizes'      => @serialize($this->getPrizes()),
                    'Modes'       => @serialize($this->getModes()),
                    'Options'     => @serialize($this->getOptions()),
                    'Enabled'     => $this->getEnabled()
                );
                break;
        }

        return $ret;
    }

    public function formatFrom($from, $data)
    {
        switch ($from) {
            case 'DB':
                $this->setId($data['Id'])
                    ->setKey($data['Key'])
                    ->setType($this->getType() ?: $data['Type'])
                    ->setTitle(@unserialize($data['Title']))
                    ->setDescription(@unserialize($data['Description']))
                    ->setAudio(@unserialize($data['Audio']))
                    ->filterAudio()
                    ->setField(@unserialize($data['Field']))
                    ->setPrizes(@unserialize($data['Prizes']))
                    ->setOptions(@unserialize($data['Options']))
                    ->setModes(@unserialize($data['Modes']))
                    ->setEnabled($data['Enabled']);
                break;
        }

        return $this;
    }
}
