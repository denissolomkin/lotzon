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

    public function getList($playerId, $count = NULL, $offset = NULL, $status = NULL)
    {
        $sql = "SELECT
                    pl.`Id` PlayerId,
                    pl.`Avatar` PlayerImg,
                    pl.`Nicname` PlayerName,
                    pl.`Points` PlayerPoints,
                    pl.`Money` PlayerMoney,
                    pl.`GamesPlayed` PlayerGamesPlayed,
                    dat.`Ping` PlayerPing
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
                . (($status === NULL) ? "" : " AND (`fr`.`Status` = (int)$status)")
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

}
