<?php

Application::import(PATH_INTERFACES . 'IProcessor.php');

class LogoutMemoryProcessor implements IProcessor
{

    public function fetch(Entity $player)
    {

        $sql = "SELECT `Date` FROM `PlayerTmpLogout` WHERE `PlayerId` = :pid";

        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(
                ':pid' => $player->getId(),
            ));
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query", 500);
        }

        return $sth->fetchColumn();

    }

    public function create(Entity $player)
    {
        $sql = "REPLACE INTO `PlayerTmpLogout` (`PlayerId`, `Date`) VALUES (:pid, :date)";

        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(
                ':pid' => $player->getId(),
                ':date' => time(),
            ));
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query".$e, 500);
        }

        return $player;

    }

    public function update(Entity $player)
    {
    }

    public function delete(Entity $player)
    {
        $sql = "DELETE FROM `PlayerTmpLogout` WHERE `PlayerId` = :pid";

        try {

            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(
                ':pid' => $player->getId(),
            ));

        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query", 500);
        }

        return true;
    }
}
