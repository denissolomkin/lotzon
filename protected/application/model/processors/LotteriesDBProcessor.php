<?php
Application::import(PATH_INTERFACES . 'IProcessor.php');

class LotteriesDBProcessor implements IProcessor
{
    public function create(Entity $lottery)
    {
        $lottery->setDate(time());

        $sql = "INSERT INTO Lotteries (`Date`, `Combination`, `WinnersCount`, `MoneyTotal`, `PointsTotal`, `BallsTotal`) VALUES (:date, :comb, :wc, :mt, :pt, :bt)";
        
        try {
            DB::Connect()->prepare($sql)->execute(array(
                ':date' => $lottery->getDate(),
                ':comb' => @serialize($lottery->getCombination()),
                ':wc'   => $lottery->getWinnersCount(),
                ':mt'   => $lottery->getMoneyTotal(),
                ':pt'   => $lottery->getPointsTotal(),
                ':bt'   => @serialize($lottery->getBallsTotal()),
            ));
        } catch (PDOException $e) {
            throw new ModelException("Error process storage query", 500);
        }

        $lottery->setId(DB::Connect()->lastInsertId());

        return $lottery;
    }

    public function update(Entity $lottery)
    {

        $sql = "REPLACE INTO Lotteries
                (`Id`,`Date`, `Combination`, `WinnersCount`, `MoneyTotal`, `PointsTotal`, `BallsTotal`)
                VALUES (:id, :date, :comb, :wc, :mt, :pt, :bt)";

        try {
            DB::Connect()->prepare($sql)->execute(array(
                ':id' => $lottery->getId(),
                ':date' => $lottery->getDate(),
                ':comb' => @serialize($lottery->getCombination()),
                ':wc'   => $lottery->getWinnersCount(),
                ':mt'   => $lottery->getMoneyTotal(),
                ':pt'   => $lottery->getPointsTotal(),
                ':bt'   => @serialize($lottery->getBallsTotal()),
            ));
        } catch (PDOException $e) {
            throw new ModelException($e->getMessage()."Error process storage query", 500);
        }

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

    public function getLastPublishedLottery()
    {
        $sql = "SELECT * FROM `Lotteries` WHERE `Ready` = 1 ORDER BY `Date` DESC LIMIT 1";

        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute();
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query", 500);
        }

        $lotteryData = $sth->fetch();

        $lottery = new Lottery();
        $lottery->formatFrom('DB', $lotteryData);

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

    public function getPlayerPlayedLotteries($playerId, $limit = 0, $offset = 0)
    {

        $sql = "SELECT  `lt` . *
                FROM  `LotteryTicketsArchive` AS  `plt`
                LEFT JOIN  `Lotteries` AS  `lt` ON  `plt`.`LotteryId` =  `lt`.`Id`
                WHERE `plt`.`PlayerId` = :plid AND `lt`.`Ready` =1
                GROUP BY  `plt`.`LotteryId`
                ORDER BY  `lt`.`Date` DESC ";

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
            throw new ModelException("Error processing storage query: ".$e->getMessage(), 500);
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

    public function getPlayerHistory($playerId, $limit, $offset) 
    {

        $sql = "SELECT PlayerId, l.Date, LotteryId,
                SUM(CASE WHEN TicketWinCurrency = 'MONEY' THEN TicketWin ELSE 0 END) AS MoneyWin,
                SUM(CASE WHEN TicketWinCurrency = 'POINT' THEN TicketWin ELSE 0 END) AS PointsWin
                FROM `LotteryTicketsArchive` t
                LEFT JOIN Lotteries l
                  ON l.Id = t.LotteryId
                WHERE `PlayerId` = :plid
                GROUP BY LotteryId
                ORDER BY `DateCreated` DESC";

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

        return $lotteriesData;
    }

    public function getLotteryDetails($lotteryId)
    {
        // get lottery basic info
        $sql = "SELECT * FROM `Lotteries` WHERE `Id` = :lotid LIMIT 1";

        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(
                ':lotid' => (int)$lotteryId,
            ));
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query " . $e->getMessage(), 500);
        }

        $lotteryData = $sth->fetch();
        if (!$lotteryData) {
            throw new ModelException("LOTTERY_NOT_FOUND", 404);
        }

        $lottery = new Lottery();
        $lottery->formatFrom('DB', $lotteryData);

        return $lottery;
    }

    public function isPlayerPlayedLotteries($lotteriesId, $playerId)
    {
        if (!is_array($lotteriesId)) {
            throw new ModelException('Bad request', 403);
        }

        if ($lotteriesId == array()) {
            return array();
        }

        $inQuery = implode(',', array_fill(0, count($lotteriesId), '?'));

        $sql = "SELECT DISTINCT
                    l.id id
                FROM
                  `Lotteries` l
                JOIN LotteryTicketsArchive la
                  ON la.LotteryId = l.Id
                WHERE
                  l.id IN (".$inQuery.")
                AND
                  la.PlayerId=?";

        try {
            $sth = DB::Connect()->prepare($sql);
            foreach ($lotteriesId as $k => $id) {
                $sth->bindValue(($k + 1), $id);
            }
            $sth->bindParam($k + 2,$playerId);
            $sth->execute();
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query", 500);
        }

        $lotteries = array();
        foreach ($sth->fetchAll() as $lottery) {
            $lotteries[] = $lottery['id'];
        }

        return $lotteries;
    }

    public function getDependentLotteryId($lotteryId, $dependency, $playerId = false)
    {
        if ($playerId == false) {
            $sql = "SELECT l.Id FROM
                  `Lotteries` l
                WHERE
                  l.`Id` " . ($dependency == 'next' ? '<' : '>') . " :lotid
                ORDER BY
                  l.`Id` " . ($dependency == 'next' ? 'DESC' : 'ASC') . " LIMIT 1";
            $sql_arr = array(
                ':lotid' => $lotteryId,
            );
        } else {
            $sql = "SELECT l.Id FROM
                  `Lotteries` l
                JOIN LotteryTicketsArchive la
                  ON la.LotteryId = l.Id
                WHERE
                  l.`Id` " . ($dependency == 'next' ? '<' : '>') . " :lotid
                AND
                  la.`PlayerId` = :plid
                ORDER BY
                  l.`Id` " . ($dependency == 'next' ? 'DESC' : 'ASC') . " LIMIT 1";
            $sql_arr = array(
                ':lotid' => $lotteryId,
                ':plid'  => $playerId,
            );
        }
        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute($sql_arr);
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query", 500);
        }

        if (!$sth->rowCount()) {
            return null;
        }

        return $sth->fetchColumn(0);

    }

    public function getWinnersCount()
    {
        $sql = "SELECT COUNT(PlayerId) FROM (SELECT DISTINCT PlayerId FROM LotteryTicketsArchive WHERE TicketWinCurrency = 'MONEY') Winners";
        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute();
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query", 500);
        }

        return $sth->fetchColumn(0);
    }

    public function getMoneyTotalWin()
    {
        $sql = "SELECT SUM(MoneyTotal) FROM Lotteries";
        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute();
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query", 500);
        }

        return $sth->fetchColumn(0);
    }


    public function recache()
    {
        return false;
    }
}