<?php

Application::import(PATH_INTERFACES . 'IProcessor.php');

class CaptchaDBProcessor implements IProcessor
{

    public function fetch(Entity $player)
    {

        $sql = "SELECT `Date` FROM `PlayerTmpCaptcha` WHERE `PlayerId` = :pid";

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
        $sql = "REPLACE INTO `PlayerTmpCaptcha` (`PlayerId`, `Date`) VALUES (:pid, :date)";

        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(
                ':pid' => $player->getId(),
                ':date' => time(),
            ));
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query", 500);
        }

        return $player;

    }

    public function update(Entity $player)
    {

        if (($date = $this->validateCaptcha($player->getId()))) {

            $time = time() - $date;

            $this->delete($player);

            $sql = "INSERT INTO `PlayerCaptcha` (`PlayerId`, `Date`, `Time`)
                VALUES (:id, :date, :time)";

            try {
                DB::Connect()->prepare($sql)->execute(array(
                    ':id' => $player->getId(),
                    ':date' => $date,
                    ':time' => $time
                ));
            } catch (PDOException $e) {
                throw new ModelException("Error processing storage query", 500);
            }

            /* Direct update for possible received data from different sessions*/

            $sql = "UPDATE `PlayerCounters`
                    SET `CaptchaCount` = `CaptchaCount` + 1, `CaptchaTime` = (`CaptchaTime` * `CaptchaCount` + :time) / (`CaptchaCount` + 1)
                    WHERE `PlayerId` = :pid";

            try {
                $sth = DB::Connect()->prepare($sql);
                $sth->execute(array(
                    ':time' => $time,
                    ':pid' => $player->getId(),
                ));
            } catch (PDOException $e) {
                throw new ModelException("Error processing storage query", 500);
            }

            return true;

        } else
            return false;


    }

    public function delete(Entity $player)
    {
        $sql = "DELETE FROM `PlayerTmpCaptcha` WHERE `PlayerId` = pid";

        try {

            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(
                ':id' => $player->getId(),
            ));

        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query", 500);
        }
    }

    public function getList()
    {

    }

    public function getStat()
    {

    }
}
