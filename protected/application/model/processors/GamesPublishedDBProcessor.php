<?php

Application::import(PATH_INTERFACES . 'IProcessor.php');

class GamesPublishedDBProcessor implements IProcessor
{

    public function getList()
    {
        $sql = "SELECT * FROM `GameSettings`";

        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute();
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query", 500);
        }

        $publishedGames = array();
        $data = $sth->fetchAll();

        foreach ($data as $gameData) {
            $games = new GamePublished();
            $games->formatFrom('DB', $gameData);
            $publishedGames[$games->getKey()] = $games;
        }

        return $publishedGames;
    }

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

    public function fetch(Entity $game)
    {
        return false;
    }

    public function create(Entity $game)
    {
        return false;
    }

    public function delete(Entity $game)
    {
        return false;
    }

    public function recache()
    {
        return false;
    }

}
