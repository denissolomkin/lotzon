<?php

Application::import(PATH_INTERFACES . 'IProcessor.php');

class NoticesDBProcessor implements IProcessor
{
    public function create(Entity $notice)
    {
        $sql = "INSERT INTO `PlayerNotices` (`Id`, `PlayerId`, `Date`, `Title`, `Text`) VALUES (:id, :playerid, :date, :title, :text)";

        try {
            $sth = DB::Connect()->prepare($sql)->execute(array(
                ':id'    => $notice->getId(),
                ':playerid'  => $notice->getPlayerId(),
                ':date'  => time(),
                ':title'  => $notice->getTitle(),
                ':text'  => $notice->getText(),
            ));
        } catch (PDOExeption $e) {
            throw new ModelException("Unable to proccess storage query", 500);            
        }

        return $notice;
    }

    public function update(Entity $notice)
    {
        $sql = "UPDATE `PlayerNotices` SET `Title` = :title, `Text` = :text WHERE `Id` = :id";

        try {
            $sth = DB::Connect()->prepare($sql)->execute(array(
                ':title'  => $notice->getTitle(),
                ':text'  => $notice->getText(),
                ':id'    => $notice->getId(),
            ));       
        } catch (PDOexception $e) {
            throw new ModelException("Unable to proccess storage query", 500);    
        }

        return $notice;
    }

    public function delete(Entity $notice)
    {
        $sql = "DELETE FROM `PlayerNotices` WHERE `Id` = :id";

        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(
                ':id' => $notice->getId()
            ));

        } catch (PDOExeption $e) {
            throw new ModelException("Unable to process delete query", 500);
        }

        return true;
    }

    public function fetch(Entity $notice)
    {

        return $notice;

    }

    public function getList($playerId = null, $date=null, $limit = null, $offset = null)
    {
        $sql = "SELECT * FROM `PlayerNotices` WHERE ";

        $where[]=1;

        // IF EXIST DATE OF REGISTRATION PLAYER
        if (!is_null($playerId)) {
            $where[]= " (".($date?'`PlayerId` = 0 OR ':'')."`PlayerId` = " . (int)$playerId.')';
        }

        if (!is_null($date)) {
            $where[]= " (`Date` >= " . (int)$date.")";
        }

        $sql .= implode(" AND ",$where)." ORDER BY `Date` DESC";

        if (!is_null($limit)) {
            $sql .= "LIMIT " . (int)$limit;
        }
        if (!is_null($offset)) {
            $sql .= "OFFSET " . (int)$offset;
        }

        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute();
        } catch (PDOExeption $e) {
            throw new ModelException("Unable to proccess storage query", 500);   
        }

        $notices = array();
        
        $list = $sth->fetchAll();
        if (count($list)) {
            foreach ($list as $noticeData) {
                $noticeObj = new Notice();
                $notices[] = $noticeObj->formatFrom('DB', $noticeData);
            }
        }

        return $notices;
    }

    public function getPlayerUnreadNotices(Player $player) {
        $sql = "SELECT COUNT(*) FROM `PlayerNotices` WHERE (`Date` >= :dn AND `Date` >= :dr )AND (`PlayerId` = 0 OR `PlayerId` = :id)";

        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(
                ':dn' => ($player->getDateLastNotice()?:1),
                ':dr' => $player->getDateRegistered(),
                ':id' => $player->getId()
            ));
        } catch (PDOExeption $e) {
            throw new ModelException("Unable to proccess storage query", 500);
        }

        return $sth->fetchColumn(0);
    }

}