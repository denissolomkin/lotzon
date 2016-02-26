<?php

class FriendsDBProcessor implements IProcessor
{
    public function create(Entity $message)
    {

    }

    public function update(Entity $message)
    {

    }

    public function delete(Entity $message)
    {

    }

    public function fetch(Entity $message)
    {

    }

    public function getList($playerId, $count = NULL, $offset = NULL, $status = NULL, $search = NULL)
    {
        $sql = "SELECT
                    pl.`Id` PlayerId,
                    pl.`Avatar` PlayerImg,
                    pl.`Nicname` PlayerName,
                    pl.`Points` PlayerPoints,
                    pl.`Money` PlayerMoney,
                    pl.`GamesPlayed` PlayerGamesPlayed,
                    dat.`Ping` PlayerPing,
                    fr.`Status` Status,
                    fr.`ModifyDate` ModifyDate,
                    fr.UserId UserId
                FROM
                  `Friends` AS fr
                JOIN
                  `Players` AS pl
                ON
                  pl.`Id` = IF(fr.UserId=:playerid, fr.FriendId, fr.UserId)
                JOIN
                  `PlayerDates` AS dat
                ON
                  dat.`PlayerId` = pl.`Id`
                WHERE
                    (fr.`FriendId` = :playerid
                    OR
                    fr.`UserId` = :playerid)"
                . (($status === NULL) ? "" : " AND (`fr`.`Status` = ".(int)$status.")")
                . (($search === NULL) ? "" : " AND (LOWER(`pl`.`Nicname`) LIKE LOWER('%".$search."%'))")
                . "ORDER BY PlayerName"
            . (($count === NULL)  ? "" : " LIMIT " . (int)$count);
        if ($offset) {
            $sql .= " OFFSET " . (int)$offset;
        }
        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(
                ':playerid' => $playerId,
            ));
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query " . $e, 1);
        }

        return $sth->fetchAll();
    }

    public function updateRequest($playerId, $toPlayerId, $status)
    {
        $sql = "UPDATE `Friends` SET `Status` = :status, `ModifyDate` = :date WHERE `FriendId` = :playerid AND `UserId` = :toplayerid";

        try {
            $sth = DB::Connect()->prepare($sql)->execute(array(
                ':playerid'   => $playerId,
                ':toplayerid' => $toPlayerId,
                ':date'       => time(),
                ':status'     => $status,
            ));
        } catch (PDOexception $e) {
            throw new ModelException("Unable to proccess storage query", 500);
        }

        return true;
    }

    public function deleteRequest($playerId, $toPlayerId)
    {
        $sql = "DELETE FROM `Friends` WHERE `UserId` = :playerid AND `FriendId` = :toplayerid";

        try {
            $sth = DB::Connect()->prepare($sql)->execute(array(
                ':playerid'   => $playerId,
                ':toplayerid' => $toPlayerId,
            ));
        } catch (PDOexception $e) {
            throw new ModelException("Unable to proccess storage query", 500);
        }

        return true;
    }

    public function remove($playerId, $toPlayerId)
    {
        $sql = "DELETE FROM `Friends` WHERE (`UserId` = :playerid AND `FriendId` = :toplayerid) OR (`FriendId` = :playerid AND `UserId` = :toplayerid)";

        try {
            $sth = DB::Connect()->prepare($sql)->execute(array(
                ':playerid'   => $playerId,
                ':toplayerid' => $toPlayerId,
            ));
        } catch (PDOexception $e) {
            throw new ModelException("Unable to proccess storage query", 500);
        }

        return true;
    }

    public function addRequest($playerId, $toPlayerId)
    {
        $sql = "INSERT INTO `Friends` (`UserId`, `FriendId`, `Status`, `ModifyDate`) VALUES (:playerid, :toplayerid, 0, :date)";

        try {
            $dbh = DB::Connect();
            $sth = $dbh->prepare($sql);
            $sth->execute(array(
                ':playerid'   => $playerId,
                ':toplayerid' => $toPlayerId,
                ':date'       => time(),
            ));
        } catch (PDOExeption $e) {
            throw new ModelException("Unable to proccess storage query", 500);
        }

        return true;
    }

    public function getStatusCount($playerId, $status = 0)
    {
        $sql = "SELECT
                    count(*) as c
                FROM
                  `Friends`
                WHERE
                (
                    `UserId` = :playerid
                OR
                    `FriendId` = :playerid
                )
                AND
                    `Status` = :status";
        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(
                ':playerid' => $playerId,
                ':status'   => $status,
            ));
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query " . $e, 1);
        }

        $count = $sth->fetch()['c'];

        return $count;
    }

    public function getStatus($playerId, $toPlayerId)
    {
        $sql = "SELECT `Status` FROM `Friends` WHERE `UserId` = :playerid AND `FriendId` = :toplayerid";

        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(
                ':playerid'   => $playerId,
                ':toplayerid' => $toPlayerId,
            ));
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query", 500);
        }
        if ($sth->rowCount()==0) {
            return null;
        }
        return (int)$sth->fetch()['Status'];
    }

    public function isFriend($playerId, $toPlayerId)
    {
        $sql = "SELECT count(*) as c FROM `Friends` WHERE ((`UserId` = :playerid AND `FriendId` = :toplayerid) OR (`FriendId` = :playerid AND `UserId` = :toplayerid)) AND `Status` = 1";

        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(
                ':playerid'   => $playerId,
                ':toplayerid' => $toPlayerId,
            ));
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query", 500);
        }

        return $sth->fetch()['c']==1?true:false;
    }
}
