<?php
use \GameConstructor;

class GamePublished extends Entity
{
    protected $_key         = '';
    protected $_title       = array();
    protected $_options     = array();
    protected $_games       = array();
    protected $_loadedGames = array();


    public function init()
    {
        $this->setModelClass('GamesPublishedModel');
    }

    public function loadGames()
    {
        $games = array();
        try {

            foreach ($this->getGames() as $gameId) {

                $game = new GameConstructor();
                $game->setId($gameId)
                    /* todo merge tables OnlineGames & QuickGames after merge LOT-22 */
                    ->setType($this->getKey() === 'OnlineGame' ? 'online' : 'chance')
                    ->fetch();
                $games[] = $game;

            }

        } catch (EntityException $e) {
            echo $e->getMessage();
        }

        return $games;
    }

    public function validate()
    {
        return true;
    }

    public function formatFrom($from, $data)
    {
        if ($from == 'DB') {
            $this->setKey($data['Key'])
                ->setTitle($data['Title'])
                ->setOptions(@unserialize($data['Options']))
                ->setGames(@unserialize($data['Games']))
                ->setLoadedGames($this->loadGames());
        }

        return $this;
    }
}
