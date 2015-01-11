<?php

Application::import(PATH_INTERFACES . 'IProcessor.php');

class NotesDBProcessor implements IProcessor
{
    public function create(Entity $note)
    {
        $sql = "INSERT INTO `PlayerNotes` (`Id`, `PlayerId`, `Date`, `UserId`, `Text`) VALUES (:id, :playerid, :date, :userid, :text)";

        try {
            $sth = DB::Connect()->prepare($sql)->execute(array(
                ':id'    => $note->getId(),
                ':playerid'  => $note->getPlayerId(),
                ':date'  => time(),
                ':userid'  => $note->getUserId(),
                ':text'  => $note->getText(),
            ));
        } catch (PDOExeption $e) {
            throw new ModelException("Unable to proccess storage query", 500);            
        }

        return $note;
    }

    public function update(Entity $note)
    {
        $sql = "UPDATE `PlayerNotes` SET `Text` = :text WHERE `Id` = :id";

        try {
            $sth = DB::Connect()->prepare($sql)->execute(array(
                ':text'  => $note->getText(),
                ':id'    => $note->getId(),
            ));       
        } catch (PDOexception $e) {
            throw new ModelException("Unable to proccess storage query", 500);    
        }

        return $note;
    }

    public function delete(Entity $note)
    {
        $sql = "DELETE FROM `PlayerNotes` WHERE `Id` = :id";

        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(
                ':id' => $note->getId()
            ));

        } catch (PDOExeption $e) {
            throw new ModelException("Unable to process delete query", 500);
        }

        return true;
    }

    public function fetch(Entity $note)
    {

        return $note;

    }

    public function getList($playerId = null, $date=null, $limit = null, $offset = null)
    {
        $sql = "SELECT PlayerNotes.*, Admins.Login as User FROM `PlayerNotes` LEFT JOIN Admins ON UserId = Admins.Id WHERE ";

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

        $notes = array();
        
        $list = $sth->fetchAll();
        if (count($list)) {
            foreach ($list as $noteData) {
                $noteObj = new Note();
                $notes[] = $noteObj->formatFrom('DB', $noteData);
            }
        }

        return $notes;
    }

}