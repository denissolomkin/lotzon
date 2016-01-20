<?php

use \GameConstructor;

class GameConstructorDBProcessor
{

    public function getList()
    {
        $games = array();

        /* todo merge tables */
        foreach (array('online' => 'OnlineGames',
                       'chance' => 'QuickGames') as $key => $table) {

            $sql = "SELECT * FROM `" . $table . "` WHERE 1";

            try {
                $sth = DB::Connect()->prepare($sql);
                $sth->execute();
            } catch (PDOException $e) {
                throw new ModelException("Error processing storage query" . $e->getMessage(), 500);
            }

            $games[$key] = array();
            $data        = $sth->fetchAll();

            foreach ($data as $gameData) {
                $gameData['Type'] = $key;
                $game             = new GameConstructor();
                $game->formatFrom('DB', $gameData);
                $games[$key][$gameData['Id']] = $game;

                /* search application by name */
                if($gameData['Type'] === 'online')
                    $games[$key][$gameData['Key']] = $game;
            }

        }

        return $games;
    }

    public function update(Entity $game)
    {

        /* todo merge tables */
        switch ($game->getType()) {

            case 'chance':

                $sql     = "REPLACE INTO `QuickGames` (`Id`, `Key`, `Title`, `Description`, `Prizes`, `Field`, `Audio`, `Enabled`) VALUES (:id, :key, :t, :d, :p, :f, :a, :e)";
                $options = array(
                    ':id'  => $game->getId(),
                    ':key' => $game->getKey(),
                    ':t'   => @serialize($game->getTitle()),
                    ':d'   => @serialize($game->getDescription()),
                    ':p'   => @serialize($game->getPrizes()),
                    ':f'   => @serialize($game->getField()),
                    ':a'   => @serialize($game->getAudio()),
                    ':e'   => $game->isEnabled(),
                );
                break;

            case 'online':

                $sql     = "REPLACE INTO `OnlineGames` (`Id`, `Key`, `Title`, `Description`, `Modes`, `Options`, `Audio`, `Enabled`) VALUES (:id, :key, :t, :d, :p, :f, :a, :e)";
                $options = array(
                    ':id'  => $game->getId(),
                    ':key' => $game->getKey(),
                    ':t'   => @serialize($game->getTitle()),
                    ':d'   => @serialize($game->getDescription()),
                    ':p'   => @serialize($game->getModes()),
                    ':f'   => @serialize($game->getOptions()),
                    ':a'   => @serialize($game->getAudio()),
                    ':e'   => $game->isEnabled(),
                );
                break;
        }

        try {
            DB::Connect()->prepare($sql)->execute($options);
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query" . $e->getMessage(), 500);
        }

        $game->setId(DB::Connect()->lastInsertId());

        return $game;
    }

    public function fetch($game)
    {
        /* todo merge tables */
        switch ($game->getType()) {
            case "online":
                $table = 'OnlineGames';
                break;

            case "chance":
                $table = 'QuickGames';
                break;
        }

        $sql = "SELECT * FROM `" . $table . "` WHERE Id = :id";
        if($game->getType() == 'online' && $game->getKey()){
            $sql .= " OR (`Key` = '".$game->getKey()."' AND `Key` IS NOT NULL)";
        }

        try {

            $sth = DB::Connect()->prepare($sql);
            $sth->execute(
                array(
                    ':id'  => $game->getId()
                )
            );

        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query" . $e->getMessage(), 500);
        }

        $data         = $sth->fetch();
        $data['Type'] = $game->getType();
        $game->formatFrom('DB', $data);

        return $game;
    }

    public function recache()
    {
        return false;
    }

}
