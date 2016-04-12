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

    public function getMoneyOrders($dateFrom=null, $dateTo=null, $args=null)
    {
        $sql = "
        SELECT CONCAT(YEAR(FROM_UNIXTIME(DateOrdered)),' ', MONTHNAME(FROM_UNIXTIME(DateOrdered))) Date, Type, COUNT( * ) `Quantity`, SUM(Sum) Sum
        FROM  `MoneyOrders`
        WHERE `DateOrdered` > :from
        AND   `DateOrdered` < :to
        AND Type!='points'
        ".(is_numeric($args['Status'])?"AND `Status` = {$args['Status']}":'')."
        ".(is_numeric($args['AdminID'])?"AND `UserID` = {$args['AdminID']}":'')."
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
        ".(is_numeric($args['AdminID'])?"AND `UserID` = {$args['AdminID']}":'')."
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

        $sql = "
        SELECT Concat('#',p.Id) Id, p.Nicname Name, g.Currency, count(g.Id) Total, sum(g.Win) Win, (sum(g.Win)*25+count(g.Id)) Rating
                                FROM `PlayerGames` g
                                JOIN Players p On p.Id=g.PlayerId
                                LEFT JOIN OnlineGames o ON o.Id = GameId

                                where g.`Date`>:from AND `Date`<:to AND g.Price>0
                                AND `GameId` = ".

            (is_numeric($args['GameId'])?$args['GameId']:'1').
            (isset($args['Currency']) && $args['Currency']!=''?" AND `Currency` = '{$args['Currency']}'":'').

                                " group by Currency, g.GameId, g.PlayerId
                                order by Currency, Rating DESC, Total DESC";

        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(':from' => $dateFrom,':to' => $dateTo));
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

}