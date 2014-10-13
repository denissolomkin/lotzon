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

    public function getPlayerHistory($playerId, $limit, $offset) 
    {
        $sql = "SELECT * FROM `PlayerLotteryWins` WHERE `PlayerId` = :plid ORDER BY `Date` DESC";

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
        $returnData = array(
            'lottery' => null,
            'winners' => array(),
            'tickets' => array(),
        );
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

        $winners = array();
        if ($lottery->getWinnersCount()) {
            // get winners
            $sql = "SELECT `p`.* FROM `PlayerLotteryWins` AS `plw` 
                    LEFT JOIN `Players` AS `p` ON `p`.`Id` = `plw`.`PlayerId`
                    WHERE `p`.`Visible` = 1 AND `plw`.`LotteryId` = :lotid AND `plw`.`MoneyWin` > 0";

            try {
                $sth = DB::Connect()->prepare($sql);
                $sth->execute(array(
                    ':lotid' => $lottery->getId(),
                ));

            } catch (PDOException $e) {
                throw new ModelException("Error processing storage query " . $e->getMessage(), 500);              
            }
            $playersids = array();
            if ($sth->rowCount()) {
                $playersData = $sth->fetchAll();

                foreach ($playersData as $playerData) {
                    $player = new Player();
                    $player->formatFrom('DB', $playerData);

                    $returnData['winners'][] = $player;
                    $playersids[] = $player->getId();
                }
            }
            // get lottery tikets
            $sql = "SELECT * FROM `LotteryTickets` WHERE `LotteryId` = :lotid AND `PlayerId` IN (%s)";

            try {
                $sth = DB::Connect()->prepare(sprintf($sql, join(',', $playersids)));
                $sth->execute(array(
                    ':lotid' => $lottery->getId(),
                ));
            } catch (PDOException $e) {
                throw new ModelException("Error processing storage query " . $e->getMessage(), 500);
            }

            if ($sth->rowCount()) {
                $ticketsData = $sth->fetchAll();

                foreach ($ticketsData as $ticketData) {
                    $ticket = new LotteryTicket();
                    $ticket->formatFrom('DB', $ticketData);

                    if (!isset($returnData['tickets'][$ticket->getPlayerId()])) {
                        $returnData['tickets'][$ticket->getPlayerId()] = array();
                    }
                    $returnData['tickets'][$ticket->getPlayerId()][$ticket->getTicketNum()] = $ticket;
                }

            }
        }   

        $returnData['lottery'] = $lottery;

        return $returnData;
    }

    public function getDependentLottery($lotteryId, $dependancy) 
    {
        $sql = "SELECT * FROM `Lotteries` WHERE `Id` " . ($dependancy == 'next' ? '<' : '>') . " :lotid AND `WinnersCount` > 0 ORDER BY `Id` " . ($dependancy == 'next' ? 'DESC' : 'ASC') . " LIMIT 1";
        
        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(
                ':lotid' => $lotteryId,
            ));
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query", 500);
        }

        $lotteryData = $sth->fetch();   

        if (!$lotteryData) {
            throw new ModelException("LOTTERY_NOT_FOUND", 404);
            
        }
        $lottery = new Lottery();
        $lottery->formatFrom('DB', $lotteryData);

        return $lottery;
    }
}