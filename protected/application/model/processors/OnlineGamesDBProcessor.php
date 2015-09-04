<?php

class OnlineGamesDBProcessor
{
    public function save(Entity $game)
    {
        $sql = "REPLACE INTO `OnlineGames`
                (`Id`, `Key`, `Title`, `Description`, `Modes`, `Options`, `Audio`, `Enabled`)
                VALUES (:id, :k, :t, :d, :m, :f, :a, :e)";

        try {
            DB::Connect()->prepare($sql)->execute(array(
                ':id'    => $game->getId(),
                ':k'     => $game->getKey(),
                ':t'     => @serialize($game->getTitle()),
                ':d'     => @serialize($game->getDescription()),
                ':m'     => @serialize($game->getModes()),
                ':f'     => @serialize($game->getOptions()),
                ':a'     => @serialize($game->getAudio()),
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

    public function getFund($gameId = null)
    {

        $month = mktime(0, 0, 0, date("n"), 1);

        $sql = "SELECT SUM(Price) Total, Currency, GameId
                FROM (
                  SELECT DISTINCT GameId, GameUid, Date, Currency, Price
                  FROM `PlayerGames` WHERE `Month` = :month AND `IsFee` = 1
                  ) a
                GROUP BY GameId, Currency";

        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(
                array(
                    ':month' => $month
                ));
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query", 500);
        }

        $rating = array();

        foreach ($sth->fetchAll() as $row) {

            $rating[$row['GameId']][$row['Currency']] = $row['Total'];

        }

        return ($gameId) ? (isset($rating[$gameId])?$rating[$gameId]:array()) : $rating;
    }

    public function getRating($gameId=null,$playerId=null)
    {
        $month = mktime(0, 0, 0, date("n"), 1);

        /* Rating For All Games And Players */
        if(!max($gameId,$playerId)) {
            $sql = "SELECT g.GameId, g.Currency, sum(g.Win) W, count(g.Id) T, p.Nicname N,  p.Avatar A, p.Id I, (sum(g.Win)*25+count(g.Id)) R
                                FROM `PlayerGames` g
                                JOIN Players p On p.Id=g.PlayerId
                                WHERE g.`Month`=:month AND g.`IsFee` = 1
                                group by g.GameId, g.Currency, g.PlayerId
                                order by Currency, R DESC, T DESC";

            try {
                $sth = DB::Connect()->prepare($sql);
                $sth->execute(
                    array(
                        ':month' => $month
                    ));
            } catch (PDOException $e) {
                throw new ModelException("Error processing storage query", 500);

            }
        }

        /* Rating For Concrete Game And All Players */
        elseif($gameId and !$playerId) {
            $sql = "SELECT g.GameId, g.Currency, sum(g.Win) W, count(g.Id) T, p.Nicname N,  p.Avatar A, p.Id I, (sum(g.Win)*25+count(g.Id)) R
                                FROM `PlayerGames` g
                                JOIN Players p On p.Id=g.PlayerId
                                WHERE g.`Month`=:month AND g.`IsFee` = 1 AND g.GameId = :gameid
                                group by g.GameId, g.Currency, g.PlayerId
                                order by Currency, R DESC, T DESC";

            try {
                $sth = DB::Connect()->prepare($sql);
                $sth->execute(
                    array(
                        ':month' => $month,
                        ':gameid' => $gameId,
                    ));
            } catch (PDOException $e) {
                throw new ModelException("Error processing storage query", 500);

            }
        }

        /* Rating For Concrete Game And Player */
        elseif($gameId AND $playerId) {
            $sql = "SELECT g.GameId, g.Currency, sum(g.Win) W, count(g.Id) T, p.Nicname N,  p.Avatar A, p.Id I, (sum(g.Win)*25+count(g.Id)) R
                                FROM `PlayerGames` g
                                JOIN Players p On p.Id=g.PlayerId
                                WHERE g.`Month`=:month AND g.`IsFee` = 1 AND g.GameId = :gameid AND g.PlayerId = :playerid
                                group by g.GameId, g.Currency, g.PlayerId
                                order by Currency, R DESC, T DESC";

            try {
                $sth = DB::Connect()->prepare($sql);
                $sth->execute(
                    array(
                        ':month' => $month,
                        ':gameid' => $gameId,
                        ':playerid' => $playerId,
                    ));
            } catch (PDOException $e) {
                throw new ModelException("Error processing storage query", 500);

            }
        }

        $rating = array();

        foreach ($sth->fetchAll() as $row) {

            $cur = $row['Currency'];
            $gid = $row['GameId'];

            unset($row['Currency'],$row['GameId']);

            if(isset($playerId)){

                $rating[$cur] = $row;

            } elseif(isset($gameId)){

                $rating[$cur][$row['I']] = $row;

            } else {

                $rating[$gid][$cur][$row['I']] = $row;

            }
        }

        return $rating;

    }

    public function getPlayerRating($gameId=null,$playerId=null)
    {
        $month = mktime(0, 0, 0, date("n"), 1);

        $sql = "SELECT g.Currency, sum(g.Win) W, count(g.Id) T, (sum(g.Win)*25+count(g.Id)) R
                                FROM `PlayerGames` g
                                JOIN Players p On p.Id=g.PlayerId
                                WHERE g.`Month`=:month AND g.`IsFee` = 1 AND g.`GameId` = :gameid AND g.`PlayerId` = :playerid
                                group by g.Currency";

        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(
                array(
                    ':month' => $month,
                    ':gameid' => $gameId,
                    ':playerid' => $playerId,
                ));
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query", 500);

        }

        $rating = array();

        foreach ($sth->fetchAll() as $row) {

            $rating[$row['Currency']] = $row['R'];

        }

        return $rating;

    }

    public function recacheRatingAndFund()
    {
        return false;
    }

}
