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

        if (($date = $this->fetch($player))) {

            $time = time() - $date;

            $this->delete($player);

            $sql = "INSERT INTO `PlayerCaptcha` (`PlayerId`, `Day`, `Date`, `Time`)
                VALUES (:id, :day, :date, :time)";

            try {
                DB::Connect()->prepare($sql)->execute(array(
                    ':id'   => $player->getId(),
                    ':date' => $date,
                    ':day'  => strtotime('today'),
                    ':time' => $time
                ));
            } catch (PDOException $e) {
                throw new ModelException("Error processing storage query", 500);
            }

            /* Direct update for possible received data from different sessions*/

            $sql = "UPDATE `PlayerCounters`
                    SET `CaptchaTime` = (`CaptchaTime` * `CaptchaCount` + :time) / (`CaptchaCount` + 1), `CaptchaCount` = `CaptchaCount` + 1
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
        $sql = "DELETE FROM `PlayerTmpCaptcha` WHERE `PlayerId` = :pid";

        try {

            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(
                ':pid' => $player->getId(),
            ));

        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query", 500);
        }
    }

    public function getList()
    {
    }

    public function getTimes()
    {
        $sql = "SELECT CaptchaTime Time, COUNT(*) Cnt  FROM `PlayerCounters` WHERE 1
                GROUP BY CaptchaTime";

        try {

            $sth = DB::Connect()->prepare($sql);
            $sth->execute();

        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query", 500);
        }

        return $sth->fetchAll();
    }

    public function getStat()
    {

        $sql = "(SELECT 'Today' Period, AVG(Time) Time, COUNT(*) Cnt
                FROM `PlayerCaptcha` WHERE `Day` = :today)
                UNION ALL
                (SELECT 'Yesterday' Period, AVG(Time) Time, COUNT(*) Count
                FROM `PlayerCaptcha` WHERE `Day` = :yesterday)
                UNION ALL
                (SELECT 'Week' Period, AVG(Time) Time, COUNT(*) Cnt
                FROM `PlayerCaptcha` WHERE `Day` >= :week)
                UNION ALL
                (SELECT 'Month' Period, AVG(Time) Time, COUNT(*) Cnt
                FROM `PlayerCaptcha` WHERE `Day` >= :month)
                ";

        try {

            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(
                ':today' => strtotime('today'),
                ':yesterday' => strtotime('-1 day'),
                ':week' => strtotime('-1 week'),
                ':month' => strtotime('-1 month')
            ));

        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query", 500);
        }

        return $sth->fetchAll();
    }
}
