<?php

class OnlineGamesDBProcessor
{
    public function save(Entity $game)
    {
        $sql = "REPLACE INTO `OnlineGames`
                (`Id`, `Key`, `Title`, `Description`, `Modes`, `Options`, `Enabled`)
                VALUES (:id, :k, :t, :d, :m, :f, :e)";

        try {
            DB::Connect()->prepare($sql)->execute(array(
                ':id'    => $game->getId(),
                ':k'     => $game->getKey(),
                ':t'     => @serialize($game->getTitle()),
                ':d'     => @serialize($game->getDescription()),
                ':m'     => @serialize($game->getModes()),
                ':f'     => @serialize($game->getOptions()),
                ':e'     => $game->isEnabled(),
            ));
        } catch (PDOException $e) {
            echo $e->getMessage();
            throw new ModelException("Error processing storage query" . $e->getMessage(), 500);
        }
        $game->setId(DB::Connect()->lastInsertId());
        return $game;
    }

    public function getList()
    {
        $sql = "SELECT * FROM `OnlineGames`";

        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute();
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query", 500);
        }

        $games = array();
        $data = $sth->fetchAll();
        foreach ($data as $gameData) {

            $game=new OnlineGame();
            $game->formatFrom('DB', $gameData);
            $games[$gameData['Key']]=$game;
        }
        return $games;
    }

    public function getGame($key)
    {
        $sql = "SELECT * FROM `OnlineGames` WHERE `Key`= :k";

        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(':k'=>$key));
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query", 500);
        }

        $data = $sth->fetch();
        $game=new OnlineGame();
        $game->formatFrom('DB', $data);
        return $game;

    }

    public function logWin($game, $combination, $clicks, $player, $prize)
    {
        $sql = "INSERT INTO `ChanceGameWins` (`GameId`, `Combination`, `Clicks`, `Date`, `PlayerId`, `ItemId`) VALUES (:gid, :comb, :clicks, :date, :plid, :iid)";

        try {
            DB::Connect()->prepare($sql)->execute(array(
                ':gid' => $game->getIdentifier(),
                ':comb' => serialize($combination),
                ':clicks' => serialize($clicks),
                ':date' => time(),
                ':plid' => $player->getId(),
                ':iid'  => $prize->getId(),
            ));
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query", 500);
        }

        return DB::Connect()->lastInsertId();
    }
}
