<?php
Application::import(PATH_INTERFACES . 'IProcessor.php');

class LotteriesModelDBProcessor implements IProcessor
{
    public function create(Entity $lottery)
    {
        $lottery->setDate(time());

        $sql = "INSERT INTO Lotteries (`Date`, `Combination`, `WinnersCount`, `MoneyTotal`, `PointsTotal`) VALUES (:date, :comb, :wc, :mt, :pt)";
        
        try {
            DB::Connect()->prepare($sql)->execute(array(
                ':date' => $lottery->getDate(),
                ':comb' => @serialize($lottery->getCombination()),
                ':wc'   => $lottery->getWinnersCount(),
                ':mt'   => $lottery->getMoneyTotal(),
                ':pt'   => $lottery->getPointsTotal(),
            ));
        } catch (PDOException $e) {
            throw new ModelException("Error process storage query", 500);
        }

        $lottery->setId(DB::Connect()->lastInsertId());

        return $lottery;
    }

    public function update(Entity $lottery)
    {
        return $lottery;
    }

    public function delete(Entity $lottery)
    {
        return $lottery;
    }

    public function fetch(Entity $lottery)
    {
        return $lottery;
    }

    public function publish(Entity $lottery)
    {
        $sql = "UPDATE `Lotteries` SET `Ready` = 1 WHERE `Id` = :id";

        try {
            DB::Connect()->prepare($sql)->execute(array(
                ':id' => $lottery->getId(),
            )); 
        } catch (PDOException $e) {
            throw new ModelException("Error process storage query", 500);            
        }

        return $lottery;
    }

    public function getPublishedLotteriesList($limit, $offset = 0) 
    {
        $sql = "SELECT * FROM `Lotteries` WHERE `Ready` = 1 ORDER BY `Date` DESC";
        if ($limit > -1) {
            $sql .= " LIMIT " . (int)$limit;
        }

        if ($offset > -1) {
            $sql .= " OFFSET " . (int)$offset;
        }
        
        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute();
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query", 500);
        }

        $lotteriesData = $sth->fetchAll();
        $lotteries = array();

        foreach ($lotteriesData as $data) {
            $lottery = new Lottery();
            $lottery->formatFrom('DB', $data);

            $lotteries[$lottery->getId()] = $lottery;
        }

        return $lotteries;
    }

    public function getLastPlayedLottery()
    {
        $currentMinute = strtotime(date("H:i"));
        $sql = "SELECT * FROM `Lotteries` WHERE `Date` >= :curminute LIMIT 1";
        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(
                ':curminute' => $currentMinute,
            ));
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query", 500);
        }

        $lotteryData = $sth->fetch();
        
        $lottery = new Lottery();
        $lottery->formatFrom('DB', $lotteryData);

        return $lottery;
    }

    public function getPlayerPlayedLotteries($playerId, $limit = 0, $offset = 0) 
    {
        $sql = "SELECT `lt`.* FROM `PlayerLotteryWins` AS `plt`
                LEFT JOIN `Lotteries` AS `lt` ON `plt`.`LotteryId` = `lt`.`Id`
                WHERE `plt`.`PlayerId` = :plid AND `lt`.`Ready` = 1 ORDER BY `lt`.`Date` DESC";

        if (!empty($limit)) {
            $sql .= " LIMIT " . (int)$limit;
        }
        if (!empty($offset)) {
            $sql .= " OFFSET " . (int)$offset;   
        }
        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(
                ':plid' => $playerId,
            ));
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query ", 500);
        }

        $lotteriesData = $sth->fetchAll();
        $lotteries = array();

        foreach ($lotteriesData as $data) {
            $lottery = new Lottery();
            $lottery->formatFrom('DB', $data);

            $lotteries[$lottery->getId()] = $lottery;
        }

        return $lotteries;
    }
}