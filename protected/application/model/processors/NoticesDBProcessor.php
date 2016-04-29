<?php

Application::import(PATH_INTERFACES . 'IProcessor.php');

class NoticesDBProcessor implements IProcessor
{
    public function create(Entity $notice)
    {
        $sql = "INSERT INTO `PlayerNotices` (`Id`, `PlayerId`, `AdminId`, `Type`, `Date`, `Title`, `Text`, `Country`, `MinLotteries`, `RegisteredUntil`, `RegisteredFrom`)
                VALUES (:id, :playerid, :adminid, :type, :date, :title, :text, :country, :minlot, :reguntil, :regfrom)";

        try {
            $sth = DB::Connect()->prepare($sql)->execute(array(
                ':id'    => $notice->getId(),
                ':playerid'  => $notice->getPlayerId(),
                ':adminid'  => $notice->getAdminId(),
                ':date'  => time(),
                ':title'  => $notice->getTitle(),
                ':type'  => $notice->getType(),
                ':text'  => $notice->getText(),
                ':country'  => $notice->getCountry(),
                ':minlot'  => $notice->getMinLotteries(),
                ':reguntil'  => $notice->getRegisteredUntil(),
                ':regfrom'  => $notice->getRegisteredFrom(),
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

    public function getList($playerId = null, $options=null, $limit = null, $offset = null)
    {
        $sql = "SELECT `PlayerNotices`.*, `Admins`.Login AdminName FROM `PlayerNotices` LEFT JOIN `Admins` ON AdminId = `Admins`.Id WHERE ";

        $where[]=1;

        // IF EXIST DATE OF REGISTRATION PLAYER
        if (!is_null($playerId)) {
            $where[]= " (".($options['date']?'`PlayerId` = 0 OR ':'')."`PlayerId` = " . (int)$playerId.')';

        }

        if($options['country'])
            $where[]= " (`Country` IS NULL OR `Country` = '". $options['country'] ."')";


        if($options['played'])
            $where[]= " (`MinLotteries` IS NULL OR `MinLotteries` <= {$options['played']})";


        if (!is_null($options['date'])) {
            $where[]= " (`Date` >= " . (int)$options['date'].")";
            $where[]= " (`RegisteredFrom` IS NULL OR `RegisteredFrom` <= " . (int)$options['date'].")";
            $where[]= " (`RegisteredUntil` IS NULL OR `RegisteredUntil` >= " . (int)$options['date'].")";
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

    public function getPlayerLastUnreadNotice(Entity $player)
    {
        $sql = "SELECT `Title` FROM `PlayerNotices`
                WHERE (`Date` >= :dl AND `Date` >= :do)
                AND (`MinLotteries` IS NULL OR `MinLotteries` <= :ml)
                AND (`RegisteredFrom` IS NULL OR `RegisteredFrom` <= :dr)
                AND (`RegisteredUntil` IS NULL OR `RegisteredUntil` >= :dr)
                AND (`Country` IS NULL OR `Country` = :cntr)
                AND (`PlayerId` = 0 OR `PlayerId` = :id) LIMIT 1";

        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(
                ':dl'  => $player->getDates('Login'),
                ':do'  => $player->getDates('Ping'),
                ':ml' => $player->getGamesPlayed(),
                ':dr' => $player->getDates('Registration'),
                ':cntr' => $player->getCountry(),
                ':id'  => $player->getId(),
            ));
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query", 500);
        }

        return $sth->fetchColumn(0);
    }

    public function getPlayerUnreadNotices(Player $player)
    {
        $sql = "SELECT COUNT(*) FROM `PlayerNotices`
                WHERE (`Date` >= :dn AND `Date` >= :dr )
                AND (`MinLotteries` IS NULL OR `MinLotteries` <= :ml)
                AND (`RegisteredFrom` IS NULL OR `RegisteredFrom` <= :dr)
                AND (`RegisteredUntil` IS NULL OR `RegisteredUntil` >= :dr)
                AND (`Country` IS NULL OR `Country` = :cntr)
                AND (`PlayerId` = 0 OR `PlayerId` = :id)";

        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(
                ':ml' => $player->getGamesPlayed(),
                ':dn' => $player->getDates('Notice'),
                ':dr' => $player->getDates('Registration'),
                ':cntr' => $player->getCountry(),
                ':id' => $player->getId()
            ));
        } catch (PDOExeption $e) {
            throw new ModelException("Unable to proccess storage query", 500);
        }

        return $sth->fetchColumn(0);
    }

}