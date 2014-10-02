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
}