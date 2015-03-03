<?php

class QuickGamesProcessor
{
    public function save(Entity $game)
    {
        $sql = "REPLACE INTO `QuickGames` (`Id`, `Title`, `Description`, `Prizes`, `Field`, `Enabled`) VALUES (:id, :t, :d, :p, :f, :e)";

        try {
            DB::Connect()->prepare($sql)->execute(array(
                ':id'    => $game->getId(),
                ':t'     => @serialize($game->getTitle()),
                ':d'     => @serialize($game->getDescription()),
                ':p'     => @serialize($game->getPrizes()),
                ':f'     => @serialize($game->getField()),
                ':e'     => $game->isEnabled(),
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
        $data = $sth->fetchAll();
        foreach ($data as $gameData) {
            $games[$gameData['Id']] = $gameData;
            $games[$gameData['Id']]['Field'] = @unserialize($gameData['Field']);
            $games[$gameData['Id']]['Prizes'] = @unserialize($gameData['Prizes']);
            $games[$gameData['Id']]['Title'] = @unserialize($gameData['Title']);
            $games[$gameData['Id']]['Description'] = @unserialize($gameData['Description']);
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
        $id= $sth->fetchAll();
        shuffle($id);
        $id=array_values($id)[0]['Id'];

        $sql = "SELECT * FROM `QuickGames` WHERE Id=:id";

        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array('id'=>$id));
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query", 500);
        }
        $data = $sth->fetch();

        if(!$data)
            return false;

        $game = new QuickGame();
        $game->setId($data['Id'])
            ->setTitle(@unserialize($data['Title']))
            ->setDescription(@unserialize($data['Description']))
            ->setPrizes(@unserialize($data['Prizes']))
            ->setField(@unserialize($data['Field']))
            ->setEnabled($data['Enabled']);

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
