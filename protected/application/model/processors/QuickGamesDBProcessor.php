<?php

class QuickGamesDBProcessor
{
    public function save(Entity $game)
    {
        $sql = "REPLACE INTO `QuickGames` (`Id`, `Key`, `Title`, `Description`, `Prizes`, `Field`, `Audio`, `Enabled`) VALUES (:id, :key, :t, :d, :p, :f, :a, :e)";

        try {
            DB::Connect()->prepare($sql)->execute(array(
                ':id'  => $game->getId(),
                ':key' => $game->getKey(),
                ':t'   => @serialize($game->getTitle()),
                ':d'   => @serialize($game->getDescription()),
                ':p'   => @serialize($game->getPrizes()),
                ':f'   => @serialize($game->getField()),
                ':a'   => @serialize($game->getAudio()),
                ':e'   => $game->isEnabled(),
            ));
        } catch (PDOException $e) {
            echo $e->getMessage();
            throw new ModelException("Error processing storage query" . $e->getMessage(), 500);
        }
        $game->setId(DB::Connect()->lastInsertId());

        return $game;
    }

    public function getGamesSettings()
    {
        $sql = "SELECT * FROM `QuickGames`";

        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute();
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query", 500);
        }
        $games = array();
        $data  = $sth->fetchAll();
        foreach ($data as $gameData) {
            $games[$gameData['Id']]                = $gameData;
            $games[$gameData['Id']]['Field']       = @unserialize($gameData['Field']);
            $games[$gameData['Id']]['Audio']       = @unserialize($gameData['Audio']);
            $games[$gameData['Id']]['Prizes']      = @unserialize($gameData['Prizes']);
            $games[$gameData['Id']]['Title']       = @unserialize($gameData['Title']);
            $games[$gameData['Id']]['Description'] = @unserialize($gameData['Description']);
        }

        return $games;

    }

    public function getList($count = NULL, $beforeId = NULL, $afterId = NULL, $offset = NULL)
    {
        $sql = "SELECT * FROM `QuickGames` WHERE 1"
            . (($beforeId === NULL) ? "" : " AND `QuickGames`.`Id` < $beforeId")
            . (($afterId === NULL) ? "" : " AND `QuickGames`.`Id` > $afterId")
            . " ORDER BY `QuickGames`.`Id` DESC"
            . (($count === NULL) ? "" : " LIMIT " . (int)$count);

        if ($offset) {
            $sql .= " OFFSET " . (int)$offset;
        }

        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute();
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query", 500);
        }

        $games = array();
        $data  = $sth->fetchAll();
        foreach ($data as $gameData) {
            $game = new QuickGame();
            $game->formatFrom('DB', $gameData);
            $games[$gameData['Id']] = $game;
        }

        return $games;
    }

    public function getRandomGame()
    {
        $sql = "SELECT Id FROM `QuickGames` WHERE Enabled=1";
        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute();
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query", 500);
        }
        $id = $sth->fetchAll();
        shuffle($id);
        $id = array_values($id)[0]['Id'];

        $sql = "SELECT * FROM `QuickGames` WHERE Id=:id";

        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array('id' => $id));
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query", 500);
        }
        $data = $sth->fetch();

        if (!$data)
            return false;

        $game = new QuickGame();
        $game->formatFrom('DB', $data);

        return $game;
    }

    public function logWin($game, $combination, $clicks, $player, $prize)
    {
        $sql = "INSERT INTO `ChanceGameWins` (`GameId`, `Combination`, `Clicks`, `Date`, `PlayerId`, `ItemId`) VALUES (:gid, :comb, :clicks, :date, :plid, :iid)";

        try {
            DB::Connect()->prepare($sql)->execute(array(
                ':gid'    => $game->getIdentifier(),
                ':comb'   => serialize($combination),
                ':clicks' => serialize($clicks),
                ':date'   => time(),
                ':plid'   => $player->getId(),
                ':iid'    => $prize->getId(),
            ));
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query", 500);
        }

        return DB::Connect()->lastInsertId();
    }
}
