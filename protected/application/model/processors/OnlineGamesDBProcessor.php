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

        $fund = array();

        foreach ($sth->fetchAll() as $row) {

            $fund[$row['GameId']][$row['Currency']] = $row['Total'];

        }

        return $fund;
    }

    public function getRating($gameId=null)
    {
        $month = mktime(0, 0, 0, date("n"), 1);
        echo $month;

        if (ini_get('date.timezone')) {
            echo 'date.timezone: ' . ini_get('date.timezone');
        }

        /* Rating For All Games And Players */

            $sql = "(SELECT g.GameId, g.Currency, p.Nicname N,  p.Avatar A, p.Id I, (sum(g.Win)*25+count(g.Id)) R, 0 Top
                                FROM `PlayerGames` g
                                JOIN Players p On p.Id=g.PlayerId
                                WHERE g.`Month`=:month AND g.`IsFee` = 1 ". ($gameId?' AND g.`GameId` = '.$gameId:'') ."
                                group by g.GameId, g.Currency, g.PlayerId)

                    UNION ALL

                    (SELECT t.GameId, t.Currency, p.Nicname N,  p.Avatar A, p.Id I, t.Rating R, 1 Top
                                FROM `OnlineGamesTop` t
                                JOIN Players p On p.Id=t.PlayerId
                                WHERE t.`Month`=:month ". ($gameId?' AND t.`GameId` = '.$gameId:'') ."
                                )

                                order by Currency, R DESC
                                ";

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

            $cur = $row['Currency'];
            $gid = $row['GameId'];
            $top = $row['Top'];

            unset($row['Currency'],$row['GameId'], $row['Top']);

            if(!isset($rating[$gid][$cur]['#'.$row['I']]) || $top)
                $rating[$gid][$cur]['#'.$row['I']] = $row;

        }

        return $rating;

    }

    public function getPlayerRating($gameId=null,$playerId=null)
    {
        $month = mktime(0, 0, 0, date("n"), 1);

        $sql = "SELECT Currency, (sum(Win)*25+count(Id)) R
                FROM(
                  SELECT Win, Id, Currency
                  FROM `PlayerGames` g
                  WHERE g.`Month`=:month AND g.`IsFee` = 1 AND g.`GameId` = :gameid AND g.`PlayerId` = :playerid
                ) t
                group by Currency";

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

    public function saveGameTop($data)
    {
        $sql = "REPLACE INTO `OnlineGamesTop`
                (`Id`, `PlayerId`, `GameId`, `Month`, `Currency`, `Rating`, `Increment`, `Period`, `Start`, `End`)
                VALUES
                (:id, :pid, :gid, :mon, :cur, :rat, :inc, :per, :str, :end)";

        try {
            DB::Connect()->prepare($sql)->execute(array(
                ':id'   => $data['Id'],
                ':pid'  => $data['PlayerId'],
                ':gid'  => $data['GameId'],
                ':mon'  => $data['Month'],
                ':cur'  => $data['Currency'],
                ':rat'  => $data['Rating'],
                ':inc'  => $data['Increment'],
                ':per'  => $data['Period'],
                ':str'  => strtotime($data['Start'],0),
                ':end'  => strtotime($data['End'],0),
            ));
        } catch (PDOException $e) {
            echo $e->getMessage();
            throw new ModelException("Error processing storage query" . $e->getMessage(), 500);
        }

        $data['Id'] = DB::Connect()->lastInsertId();
        return $data;
    }

    public function getGameTop($month=null)
    {
        $month = $month ? : mktime(0, 0, 0, date("n"), 1);

        $sql = "SELECT g.*, p.Avatar, p.Nicname
                  FROM `OnlineGamesTop` g
                  LEFT JOIN `Players` p ON p.Id = g.PlayerId
                  WHERE g.`Month`=:month
                  ORDER BY g.GameId, g.Currency, g.Rating
                ";


        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(
                array(
                    ':month' => $month,
                ));
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query", 500);
        }

        $top = array();

        foreach ($sth->fetchAll() as $row) {

            $row['Start'] = date("H:i",$row['Start']);
            $row['End'] = date("H:i",$row['End']);
            $top[] = $row;
        }

        return $top;

    }

    public function incrementGameTop()
    {

        $month = mktime(0, 0, 0, date("n"), 1);
        $time  = strtotime(date("H:i"), 0);
        $now   = time();

        $sql = "UPDATE `OnlineGamesTop`
                  SET Rating = Rating + Increment, `LastUpdate` = :now
                  WHERE `Month` = :month AND `Start` <= :time AND `End` >= :time AND `Period` > 0 AND `LastUpdate` < :now - Period*60
                ";

        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(
                array(
                    ':month' => $month,
                    ':time' => $time,
                    ':now' => $now,
                ));
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query: ".$e->getMessage(), 500);
        }

    }

    public function deleteGameTop($id)
    {

        $sql = "DELETE FROM `OnlineGamesTop` WHERE `Id` = :id";

        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(
                ':id' => $id
            ));

        } catch (PDOExeption $e) {
            throw new ModelException("Unable to process delete query", 500);
        }

        return true;

    }

    public function recacheRatingAndFund()
    {
        return false;
    }

}
