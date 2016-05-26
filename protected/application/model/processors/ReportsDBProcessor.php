<?php
Application::import(PATH_INTERFACES . 'IProcessor.php');

class ReportsDBProcessor implements IProcessor
{
    public function create(Entity $order) 
    {
    } 


    public function update(Entity $order) 
    {
    } 

    public function delete(Entity $order) 
    {
        
    } 

    public function fetch(Entity $order) 
    {
    }

/*
    public function updateMoneyOrders()
    {
        $sql = "
        SELECT o. * , IFNULL(m.Coefficient,3) Coef
        FROM  `MoneyOrders` o
        LEFT JOIN Players p ON p.Id = o.PlayerId
        LEFT JOIN MUICountries c ON c.Code = p.Country
        LEFT JOIN MUICurrency m ON m.Id = c.Currency
        WHERE  o.Type!='points'";
// o.Sum IS NULL AND
        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute();
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query", 500);
        }

        $case = array();
        if ($sth->rowCount()) {
            foreach ($sth->fetchAll() as $orderData) {
                $orderData['Data']=unserialize($orderData['Data']);
                $case[] = "WHEN Id = {$orderData['Id']} THEN ".$orderData['Data']['summ']['value']/$orderData['Coef'];
            }
        }

        //print_r($case);

        $sth = DB::Connect()->prepare(" UPDATE  MoneyOrders SET Sum = CASE ".implode(' ',$case)." END")->execute();

    }
*/

    public function getMoneyOrders($dateFrom=null, $dateTo=null, $args=null)
    {
        $sql = "
        SELECT CONCAT(YEAR(FROM_UNIXTIME(DateOrdered)),' ', MONTHNAME(FROM_UNIXTIME(DateOrdered))) Date, Type, COUNT( * ) `Quantity`, SUM(Equivalent) Sum
        FROM  `MoneyOrders`
        WHERE `DateOrdered` > :from
        AND   `DateOrdered` < :to
        AND Type!='points'
        ".(is_numeric($args['Status'])?"AND `Status` = {$args['Status']}":'')."
        ".(is_numeric($args['AdminID'])?"AND `AdminID` = {$args['AdminID']}":'')."
        GROUP BY Date, Type
        ORDER BY DateOrdered";


        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(':from' => $dateFrom,':to' => $dateTo));
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query", 500);
        }

        return $sth->fetchAll();
    }

    public function getShopOrders($dateFrom=null, $dateTo=null, $args=null)
    {
        $sql = "
        SELECT CONCAT(YEAR(FROM_UNIXTIME(DateOrdered)),' ', MONTHNAME(FROM_UNIXTIME(DateOrdered))) Date, i.Title, COUNT( * ) `Quantity`
        FROM   `ShopOrders` o
        LEFT JOIN ShopItems i ON i.Id = o.ItemId
        WHERE  `DateOrdered` > :from
        AND    `DateOrdered` < :to
        ".(is_numeric($args['Status'])?"AND `Status` = {$args['Status']}":'')."
        ".(is_numeric($args['AdminID'])?"AND `AdminID` = {$args['AdminID']}":'')."
        GROUP BY Date, ItemId
        ORDER BY DateOrdered";

        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(':from' => $dateFrom,':to' => $dateTo));
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query", 500);    
        }
        return $sth->fetchAll();
    }

    public function getUserRegistrations($dateFrom=null, $dateTo=null, $args=null)
    {

        $sql = "
        SELECT "
            .(!$args['GroupBy'] || $args['GroupBy']=='' || $args['GroupBy']=='Month' ? "CONCAT(YEAR(FROM_UNIXTIME(Registration)),' ', MONTHNAME(FROM_UNIXTIME(Registration))) Month, " : '')
            .(!$args['GroupBy'] || $args['GroupBy']=='' || $args['GroupBy']=='Country' ? 'Country, ' : '').
        " COUNT( * ) `Quantity`
        FROM   `Players`
        LEFT JOIN PlayerDates ON PlayerId = Id
        WHERE  `Registration` > :from
        AND    `Registration` < :to
        GROUP BY ".($args['GroupBy'] && $args['GroupBy']!=''?$args['GroupBy']:'Month,Country')."
        ORDER BY ".($args['GroupBy']!='Country'?'`Registration`,':'')."Quantity Desc";

        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(':from' => $dateFrom,':to' => $dateTo));
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query {$e->getMessage()}", 500);
        }
        return $sth->fetchAll();
    }

    public function getOnlineGames($dateFrom=null, $dateTo=null, $args=null)
    {

        $sql = "

SELECT CONCAT(YEAR(FROM_UNIXTIME(Date)),' ', MONTHNAME(FROM_UNIXTIME(Date))) `Month`, o.Key, a.Mode, COUNT( * ) `Quantity`, SUM(Price) `Sum` FROM
        ( SELECT GameId, Date, CONCAT(Currency,'-', Price) Mode, Price
        FROM   `PlayerGames` g
        WHERE  `Date` > :from
        AND    `Date` < :to
        AND    `Price` > 0
        ".(is_numeric($args['GameId'])?"AND `GameId` = {$args['GameId']}":'')."
        ".(isset($args['Currency']) && $args['Currency']!=''?"AND `Currency` = '{$args['Currency']}'":'')."
        GROUP BY GameUid, Date) a

        LEFT JOIN OnlineGames o ON o.Id = a.GameId
             GROUP BY Month,
            GameId, Mode
        ORDER BY YEAR(FROM_UNIXTIME(Date)), MONTH(FROM_UNIXTIME(Date)), GameId, a.Mode";

        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(':from' => $dateFrom,':to' => $dateTo));
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query {$e->getMessage()}", 500);
        }
        return $sth->fetchAll();
    }

    public function getRating($gameId=null)
    {
        $month = mktime(0, 0, 0, date("n"), 1);

        /* Rating For All Games And Players */

        $sql = "(SELECT g.GameId, g.Currency, p.Nicname N,  p.Avatar A, p.Id I, (sum(g.Win)*25+count(g.Id)) R, 0 Top
                                FROM `PlayerGames` g
                                JOIN Players p On p.Id=g.PlayerId
                                WHERE g.`Month`=:month AND g.`IsFee` = 1 ". ($gameId?' AND g.`GameId` = '.$gameId:'') ."
                                group by g.GameId, g.Currency, g.PlayerId)

                    UNION ALL

                    (SELECT t.GameId, t.Currency, p.Nicname N,  p.Avatar A, p.Id I, t.Rating R, 1 Top
                                FROM `OnlineGamesTop` t
                                JOIN Players p On p.Id=t.PlayerId
                                WHERE t.`Month`=:month ". ($gameId?' AND t.`GameId` = '.$gameId:'') ."
                                )

                                order by Currency, R DESC
                                ";

        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(
                array(
                    ':month' => $month
                ));
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query", 500);

        }


        $rating = array();

        foreach ($sth->fetchAll() as $row) {

            $cur = $row['Currency'];
            $gid = $row['GameId'];
            $top = $row['Top'];

            unset($row['Currency'],$row['GameId'], $row['Top']);

            if(!isset($rating[$gid][$cur]['#'.$row['I']]) || $top)
                $rating[$gid][$cur]['#'.$row['I']] = $row;

        }

        return $rating;

    }

    public function getTopOnlineGames($dateFrom=null, $dateTo=null, $args=null)
    {

        $sql = "(SELECT p.Id Id, p.Nicname Name, g.Currency, (sum(g.Win)*25+count(g.Id)) Rating, count(g.Id) Total, sum(g.Win) Win, 0 Top
        FROM `PlayerGames` g
        JOIN Players p On p.Id=g.PlayerId
        WHERE g.`Month`>=:from AND g.`Month`<=:to AND g.`IsFee` = 1 AND g.`GameId` = :gid".
            (isset($args['Currency']) && $args['Currency'] != '' ? " AND `Currency` = '{$args['Currency']}' " : '') .
            " group by g.GameId, g.Currency, g.PlayerId)

            UNION ALL

            (SELECT  p.Id Id, p.Nicname Name, t.Currency, t.Rating Rating, NULL Total, NULL Win, 1 Top
            FROM `OnlineGamesTop` t
            JOIN Players p On p.Id=t.PlayerId
            WHERE t.`Month` >= :from AND t.`Month` <= :to
            AND t.`GameId` = :gid" .
            (isset($args['Currency']) && $args['Currency'] != '' ? " AND `Currency` = '{$args['Currency']}' " : '') . ")

            order by Currency, Rating DESC";

        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(
                ':from' => $dateFrom,
                ':to' => $dateTo,
                ':gid' => is_numeric($args['GameId']) ? $args['GameId'] : 1
            ));
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query {$e->getMessage()}", 500);
        }

        return $sth->fetchAll();
    }

    public function getBotWins($dateFrom=null, $dateTo=null, $args=null)
    {

        $sql = "

        SELECT CONCAT(YEAR(FROM_UNIXTIME(Date)),' ', MONTHNAME(FROM_UNIXTIME(Date))) `Month`, o.Key, a.Mode, COUNT( * ) `Quantity`, SUM(Price) `Sum` FROM
        ( SELECT GameId, Date, CONCAT(Currency,'-', Price) Mode, SUM(IFNULL(Prize,Price*Result)) Price
        FROM   `PlayerGames` g
        LEFT JOIN Players p ON p.Id=g.PlayerId
        WHERE  `Date` > :from
        AND    `Date` < :to
        AND    `Price` > 0
        AND    p.`Id` IS NULL
        ".(is_numeric($args['GameId'])?"AND `GameId` = {$args['GameId']}":'')."
        ".(isset($args['Currency']) && $args['Currency']!=''?"AND `Currency` = '{$args['Currency']}'":'')."
        GROUP BY GameUid, Date) a

        LEFT JOIN OnlineGames o ON o.Id = a.GameId
             GROUP BY Month,
            GameId, Mode
        ORDER BY YEAR(FROM_UNIXTIME(Date)), MONTH(FROM_UNIXTIME(Date)), GameId, a.Mode";

        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(':from' => $dateFrom,':to' => $dateTo));
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query {$e->getMessage()}", 500);
        }
        return $sth->fetchAll();
    }

    public function getSlotsWins($dateFrom=null, $dateTo=null, $args=null)
    {

        $sql = "
        SELECT CURRENCY, COUNT_PLAY, COUNT_WIN, SUM_PLAY, SUM_WIN, SUM_PLAY - SUM_WIN BANDIT_WIN, CONCAT(ROUND((SUM_PLAY - SUM_WIN)/SUM_PLAY * 100), '%') BANDIT_LUCK FROM
        (SELECT 
        CURRENCY, SUM(IF(SUM>0,0,1)) COUNT_PLAY, SUM(IF(SUM>0,1,0)) COUNT_WIN,
        SUM(IF(SUM>0,0,IF(Currency = 'MONEY', Equivalent, Sum)))*-1 SUM_PLAY,
        SUM(IF(SUM>0,IF(Currency = 'MONEY', Equivalent, Sum),0)) SUM_WIN
        FROM `Transactions` 
        WHERE `ObjectType` = 'Slots' 
        AND `Date` > :from
        AND `Date` < :to
        ".(isset($args['Currency']) && $args['Currency']!=''?"AND `Currency` = '{$args['Currency']}'":'')." 
        GROUP BY CURRENCY) t";

        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(':from' => $dateFrom,':to' => $dateTo));
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query {$e->getMessage()}", 500);
        }
        return $sth->fetchAll();
    }

    public function getUserReviews($dateFrom=null, $dateTo=null, $args=null)
    {
        $sql = "
        SELECT CONCAT(YEAR(FROM_UNIXTIME(Date)),' ', MONTHNAME(FROM_UNIXTIME(Date))) Month, COUNT( * ) `Quantity`
        FROM   `PlayerReviews`
        WHERE  `Date` > :from
        AND    `Date` < :to
        ".(is_numeric($args['Status'])?"AND `Status` = {$args['Status']}":'')."
        ".(is_numeric($args['AdminID'])?"AND `AdminID` = {$args['AdminID']}":'')."
        GROUP BY Month
        ORDER BY Date";

        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(':from' => $dateFrom,':to' => $dateTo));
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query", 500);
        }
        return $sth->fetchAll();
    }

    public function getLotteryWins($dateFrom=null, $dateTo=null, $args=null)
    {
        $sql = "
        SELECT
          CONCAT(DAY(FROM_UNIXTIME(Date)),' ', MONTHNAME(FROM_UNIXTIME(Date)),' ', YEAR(FROM_UNIXTIME(Date))) Day,
          WinnersCount, MoneyTotal, PointsTotal, BallsTotal, Combination
        FROM  `Lotteries`
        WHERE  `Date` > :from
        AND    `Date` < :to";

        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(':from' => $dateFrom,':to' => $dateTo));
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query ".$e->getMessage(), 500);
        }

        $lotteries = $sth->fetchAll();
        if ($sth->rowCount()) {
            foreach ($lotteries as &$lottery) {
                $lottery['BallsTotal'] = @unserialize($lottery['BallsTotal']);
                for($i=1;$i<=6;$i++)
                    $lottery[$i . 'Ball'] = $lottery['BallsTotal'][$i];
                unset($lottery['BallsTotal']);
                $lottery['Combination'] = implode(',', unserialize($lottery['Combination']));
            }
        }

        return $lotteries;

    }

    public function getGoldTicketOrders($dateFrom=null, $dateTo=null, $args=null)
    {
        $sql = "
        SELECT
          CONCAT(DAY(FROM_UNIXTIME(Date)),' ', MONTHNAME(FROM_UNIXTIME(Date)),' ', YEAR(FROM_UNIXTIME(Date))) Day,
          COUNT(Currency) as cnt,
          Currency
        FROM  `Transactions`
        WHERE  `ObjectType`='Gold'
        AND    `Date` > :from
        AND    `Date` < :to
        GROUP BY DAY,currency
        ORDER BY DATE";

        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(':from' => $dateFrom,':to' => $dateTo));
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query ".$e->getMessage(), 500);
        }

        $lotteries = $sth->fetchAll();

        $days = array();
        foreach ($lotteries as $lottery) {
            if (!isset($days[$lottery['Day']])) {
                $days[$lottery['Day']] = array('Day'=>$lottery['Day'], 'POINT'=>0, 'MONEY'=>0, 'POINTS_BUY'=>0, 'MONEY_BUY'=>0, 'POINTS_WIN'=>0, 'MONEY_WIN'=>0);
            }
            $days[$lottery['Day']][$lottery['Currency']] = $lottery['cnt'];
        }

        $sql = "SELECT
                  CONCAT(DAY(FROM_UNIXTIME(DATE)),' ', MONTHNAME(FROM_UNIXTIME(DATE)),' ', YEAR(FROM_UNIXTIME(DATE))) Day,
                  (SELECT SUM(lta.TicketWin/mcu.Coefficient) FROM `LotteryTicketsArchive` AS lta
                JOIN
                    Players AS p
                ON
                    lta.PlayerId = p.Id
                JOIN
                    `MUICountries` AS mc
                ON
                    mc.Code = p.Country
                JOIN
                    `MUICurrency` AS mcu
                ON
                    mc.Currency = mcu.Id
                WHERE
                    lta.LotteryId = Lotteries.Id
                AND
                    lta.isGold = 1
                AND
                    lta.TicketWinCurrency = 'MONEY'
                  ) AS money,
                  (SELECT SUM(lta.TicketWin) FROM `LotteryTicketsArchive` AS lta
                WHERE
                    lta.LotteryId = Lotteries.Id
                AND
                    lta.isGold = 1
                AND
                    lta.TicketWinCurrency = 'POINT'
                  ) AS point
                FROM  `Lotteries`
                WHERE
                  `Date` > :from
                AND
                  `Date` < :to
                ORDER BY `Date`;
        ";

        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(':from' => $dateFrom,':to' => $dateTo));
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query ".$e->getMessage(), 500);
        }

        $wins = $sth->fetchAll();

        foreach ($wins as $win) {
            if (!$win['point'] and !$win['money']) {
                continue;
            }
            if (!isset($days[$win['Day']])) {
                $days[$win['Day']] = array('Day'=>$win['Day'], 'POINT'=>0, 'MONEY'=>0, 'POINTS_BUY'=>0, 'MONEY_BUY'=>0, 'POINTS_WIN'=>0, 'MONEY_WIN'=>0, 'POINTS_DIFF'=>0, 'POINTS_DIFF'=>0);
            }
            if ($win['point']) {
                $days[$win['Day']]['POINTS_WIN'] = $win['point'];
            }
            if ($win['money']) {
                $days[$win['Day']]['MONEY_WIN'] = $win['money'];
            }
        }

        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(':from' => $dateFrom,':to' => $dateTo));
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query ".$e->getMessage(), 500);
        }

        $days_index = array();
        foreach ($days as $day) {
            $day['POINTS_BUY'] = SettingsModel::instance()->getSettings('goldPrice')->getValue('POINTS') * $day['POINT'];
            $day['MONEY_BUY'] = SettingsModel::instance()->getSettings('goldPrice')->getValue('UA') * $day['MONEY'];
            $day['POINTS_DIFF'] = $day['POINTS_BUY'] - $day['POINTS_WIN'];
            $day['MONEY_DIFF'] = $day['MONEY_BUY'] - $day['MONEY_WIN'];
            $days_index[] = $day;
        }

        return $days_index;
    }

}