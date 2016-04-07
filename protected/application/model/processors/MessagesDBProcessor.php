<?php

class MessagesDBProcessor implements IProcessor
{
    public function create(Entity $message)
    {
        $sql = "INSERT INTO `Messages` (`Id`, `PlayerId`, `ToPlayerId`, `Text`, `Image`, `Date`) VALUES (:id, :playerid, :toplayerid, :text, :image, :date)";

        try {
            $dbh = DB::Connect();
            $sth = $dbh->prepare($sql);
            $sth->execute(array(
                ':id'         => $message->getId(),
                ':playerid'   => $message->getPlayerId(),
                ':toplayerid' => $message->getToPlayerId()?:null,
                ':text'       => $message->getText(),
                ':image'      => $message->getImage(),
                ':date'       => time(),
            ));
        } catch (PDOExeption $e) {
            throw new ModelException("Unable to proccess storage query", 500);
        }

        $message->setId($dbh->lastInsertId());
        $message->fetch();

        return $message;
    }

    public function update(Entity $message)
    {
        $sql = "UPDATE `Messages` SET `Status` = :status, `Text` = :text, `Image` = :image WHERE `Id` = :id";

        try {
            $sth = DB::Connect()->prepare($sql)->execute(array(
                ':id'         => $message->getId(),
                ':status'     => $message->getStatus(),
                ':text'       => $message->getText(),
                ':image'      => $message->getImage(),
            ));
        } catch (PDOexception $e) {
            throw new ModelException("Unable to proccess storage query", 500);
        }

        return $message;
    }

    public function delete(Entity $message)
    {
        $sql = "DELETE FROM `Messages` WHERE `Id` = :id";

        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(
                ':id' => $message->getId()
            ));

        } catch (PDOExeption $e) {
            throw new ModelException("Unable to process delete query", 500);
        }

        return true;
    }

    public function fetch(Entity $message)
    {
        $sql = "SELECT
                    `Messages`.*,
                    `Players`.`Avatar` PlayerImg,
                    `Players`.`Nicname` PlayerName
                FROM `Messages`
                LEFT JOIN
                    `Players`
                  ON
                    `Players`.`Id` = `Messages`.`PlayerId`
                WHERE
                    `Messages`.`Id` = :id
                LIMIT 1";

        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(
                ':id'    => $message->getId(),
            ));
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query", 500);
        }

        if (!$sth->rowCount()) {
            throw new ModelException("Message not found", 404);
        }

        $data = $sth->fetch();
        $message->formatFrom('DB', $data);

        return $message;
    }

    public function getStatusCount($playerId, $status = 0)
    {
        $sql = "SELECT
                    count(DISTINCT PlayerId) as c
                FROM
                  `Messages`
                WHERE
                    `ToPlayerId` = :toplaterid
                AND
                    `Status` = :status";
        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(
                ':toplaterid' => $playerId,
                ':status'     => $status,
            ));
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query " . $e, 1);
        }

        $count = $sth->fetch()['c'];

        return $count;
    }

    public function getList($playerFirst, $playerSecond, $count = NULL, $beforeId = NULL, $afterId = NULL, $offset = NULL)
    {
        $sql = "SELECT
                    `Messages`.*,
                    `Players`.`Avatar` PlayerImg,
                    `Players`.`Nicname` PlayerName
                FROM `Messages`
                LEFT JOIN
                    `Players`
                  ON
                    `Players`.`Id` = `Messages`.`PlayerId`
                WHERE
                (
                    (
                        `Messages`.`PlayerId` = :playerid
                    AND
                        `Messages`.`ToPlayerId` = :toplayerid
                    )
                    OR
                    (
                        `Messages`.`PlayerId` = :toplayerid
                    AND
                        `Messages`.`ToPlayerId` = :playerid
                    )
                )
                    "
                . (($beforeId === NULL) ? "" : " AND (`Messages`.`Id` < ".(int)$beforeId.")")
                . (($afterId === NULL)  ? "" : " AND (`Messages`.`Id` > ".(int)$afterId.")")
                . "
                ORDER BY `Messages`.`Id` DESC"
                . (($count === NULL)  ? "" : " LIMIT " . (int)$count);
        if ($offset) {
            $sql .= " OFFSET " . (int)$offset;
        }
        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(
                ':playerid'   => $playerFirst,
                ':toplayerid' => $playerSecond,
            ));
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query " . $e, 1);
        }

        $messages = array();
        foreach ($sth->fetchAll() as $messageData) {
            $message = new \Message;
            $messages[$messageData['Id']] = $message->formatFrom('DB',$messageData);
        }

        return $messages;
    }

    public function getUnreadMessages($playerId)
    {
        $sql = "SELECT
                    `Messages`.*,
                    `Players`.`Avatar` PlayerImg,
                    `Players`.`Nicname` PlayerName
                FROM `Messages`
                LEFT JOIN
                    `Players`
                  ON
                    `Players`.`Id` = `Messages`.`PlayerId`
                WHERE
                    `Messages`.`ToPlayerId` = :playerid
                AND
                    `Messages`.Date > (SELECT MessageNotification FROM `PlayerDates` WHERE PlayerId = :playerid)
                AND
                    `Messages`.Status = 0
                ORDER BY `Messages`.`Id` DESC";
        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(
                ':playerid'   => $playerId,
            ));
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query " . $e, 1);
        }

        $messages = array();
        foreach ($sth->fetchAll() as $messageData) {
            $message = new \Message;
            $messages[] = $message->formatFrom('DB',$messageData);
        }

        return $messages;
    }

    public function getLastTalks($playerId, $count = NULL, $offset = NULL, $modifyDate = NULL)
    {
        $sql = "SELECT
                    mes.*,
                    `Players`.`Id` PlayerId,
                    `Players`.`Avatar` PlayerImg,
                    `Players`.`Nicname` PlayerName,
                    (SELECT IF(mes.ToPlayerId=:playerid,IFNULL(MIN(m2.Status),1),mes.Status) FROM Messages as m2 WHERE m2.PlayerId = `Players`.Id AND m2.ToPlayerId = :playerid) as Status
                FROM `Messages` as mes
                JOIN
                (
                    SELECT MAX(m.id) AS messageid, IF(m.PlayerId=:playerid,m.ToPlayerId, m.`PlayerId`) AS pid  FROM Messages AS m
                    WHERE
                        m.PlayerId = :playerid
                    OR
                        m.ToPlayerId = :playerid
                    GROUP BY pid
                    ORDER BY messageid DESC
                ) AS q
                ON q.messageid = mes.`Id`
                LEFT JOIN
                    `Players`
                  ON
                    `Players`.`Id` = q.pid"
            . (($modifyDate === NULL)  ? "" : " WHERE mes.`Date` > ".(int)$modifyDate)
            . (($count === NULL)  ? "" : " LIMIT " . (int)$count);
        if ($offset) {
            $sql .= " OFFSET " . (int)$offset;
        }
        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(
                ':playerid'   => $playerId,
            ));
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query " . $e, 1);
        }

        $messages = array();
        foreach ($sth->fetchAll() as $messageData) {
            $message = new \Message;
            $messages[$messageData['Id']] = $message->formatFrom('DB',$messageData);
        }

        return $messages;
    }

    public function markRead($playerId, $toPlayerId)
    {
        $sql = "UPDATE `Messages` SET `Status` = 1 WHERE `PlayerId` = :playerid AND `ToPlayerId` = :toplayerid";

        try {
            $sth = DB::Connect()->prepare($sql)->execute(array(
                ':playerid'   => $playerId,
                ':toplayerid' => $toPlayerId
            ));
        } catch (PDOexception $e) {
            throw new ModelException("Unable to proccess storage query", 500);
        }

        return true;
    }

    public function setNotificationsDate($playerId, $time = NULL)
    {
        $sql = "UPDATE `PlayerDates` SET `MessageNotification` = :date WHERE `PlayerId` = :playerid";

        try {
            $sth = DB::Connect()->prepare($sql)->execute(array(
                ':playerid' => $playerId,
                ':date'     => ($time ? $time : time()),
            ));
        } catch (PDOexception $e) {
            throw new ModelException("Unable to proccess storage query", 500);
        }

        return true;
    }

}
