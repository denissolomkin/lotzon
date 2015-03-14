<?php

class GameSettingsDBProcessor
{
    public function update(Entity $game)
    {
        $sql = "REPLACE INTO `GameSettings` (`Key`, `Title`, `Options`, `Games`) VALUES (:key, :gt, :opt, :gms)";

        try {
            DB::Connect()->prepare($sql)->execute(array(
                ':key'  => $game->getKey(),
                ':gt'   => $game->getTitle(),
                ':opt'  => @serialize($game->getOptions()),
                ':gms'  => @serialize($game->getGames()),
            ));
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query" . $e->getMessage(), 500);
        }

        return $game;
    }

    public function getList()
    {
        $sql = "SELECT * FROM `GameSettings`";

        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute();
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query", 500);
        }

        $games = array();
        $data = $sth->fetchAll();

        foreach ($data as $gameData) {

            $game = new GameSettings();
            $game->formatFrom('DB', $gameData);
            $games[$game->getKey()] = $game;
        }

        return $games;
    }

}
