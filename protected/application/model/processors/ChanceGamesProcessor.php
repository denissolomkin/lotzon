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
}