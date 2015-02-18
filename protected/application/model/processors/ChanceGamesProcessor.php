<?php

class ChanceGamesProcessor
{
    public function save(Entity $game)
    {
        $sql = "REPLACE INTO `ChanceGames` (`Identifier`, `MinFrom`, `MinTo`, `Prizes`, `GameTitle`, `GamePrice`, `PointsWin`, `TriesCount`) VALUES (:id, :mf, :mt, :prizes, :gt, :gp, :points, :tc)";

        try {
            DB::Connect()->prepare($sql)->execute(array(
                ':id'     => $game->getIdentifier(),
                ':mf'     => $game->getMinFrom(),
                ':mt'     => $game->getMinTo(),
                ':prizes' => @serialize($game->getPrizes()),
                ':gt'     => $game->getGameTitle(),
                ':gp'     => $game->getGamePrice(),
                ':points' => $game->getPointsWin(),
                ':tc'     => $game->getTriesCount(),
            ));
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query" . $e->getMessage(), 500);
        }

        return $game;
    }

    public function getGamesSettings()
    {
        $sql = "SELECT * FROM `ChanceGames`";

        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute();
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query", 500);   
        }

        $games = array();
        $data = $sth->fetchAll();

        foreach ($data as $gameData) {
            $game = new ChanceGame();
            $game->setIdentifier($gameData['Identifier'])
                 ->setMinFrom($gameData['MinFrom'])
                 ->setMinTo($gameData['MinTo'])
                 ->setPrizes(@unserialize($gameData['Prizes']))
                 ->setGameTitle($gameData['GameTitle'])
                 ->setGamePrice($gameData['GamePrice'])
                 ->setPointsWin($gameData['PointsWin'])
                 ->setTriesCount($gameData['TriesCount']);

            $games[$game->getIdentifier()] = $game;
        }

        return $games;
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

    public function getUnorderedChanceWinData($itemId, $player) {
        $sql = "SELECT * FROM `ChanceGameWins` WHERE `ItemId` = :iid AND `PlayerId` = :plid AND `OrderRecieved` = 0 ORDER BY `Date` DESC LIMIT 1";

        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(
                ':iid' => $itemId,
                ':plid' => $player->getId(),
            ));
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query", 500);    
        }

        if ($sth->rowCount()) {
            return $sth->fetch();
        }

        return false;
    }

    public function beginTransaction()
    {
        try {
            DB::Connect()->beginTransaction();
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query", 500);
        }
        return true;
    }

    public function commit()
    {
        try {
            DB::Connect()->commit();
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query", 500);
        }
        return true;
    }

    public function rollBack()
    {
        try {
            DB::Connect()->rollBack();
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query", 500);
        }
        return true;
    }
}
