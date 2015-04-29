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
        GROUP BY Date, Type";


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
        GROUP BY Date, ItemId";

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
            .(!$args['GroupBy'] || $args['GroupBy']=='' || $args['GroupBy']=='Month' ? "CONCAT(YEAR(FROM_UNIXTIME(DateRegistered)),' ', MONTHNAME(FROM_UNIXTIME(DateRegistered))) Month, " : '')
            .(!$args['GroupBy'] || $args['GroupBy']=='' || $args['GroupBy']=='Country' ? 'Country, ' : '').
        " COUNT( * ) `Quantity`
        FROM   `Players`
        WHERE  `DateRegistered` > :from
        AND    `DateRegistered` < :to
        GROUP BY ".($args['GroupBy'] && $args['GroupBy']!=''?$args['GroupBy']:'Month,Country')."
        ORDER BY ".($args['GroupBy']!='Country'?'`DateRegistered`,':'')."Quantity Desc";

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
        ORDER BY `Month`, GameId, a.Mode";

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
        SELECT CONCAT(YEAR(FROM_UNIXTIME(Date)),' ', MONTHNAME(FROM_UNIXTIME(Date))) Date, COUNT( * ) `Quantity`
        FROM   `PlayerReviews`
        WHERE  `Date` > :from
        AND    `Date` < :to
        ".(is_numeric($args['Status'])?"AND `Status` = {$args['Status']}":'')."
        GROUP BY
            MONTH(FROM_UNIXTIME(Date)),
            YEAR(FROM_UNIXTIME(Date))
        ORDER BY Date";

        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(':from' => $dateFrom,':to' => $dateTo));
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query", 500);
        }
        return $sth->fetchAll();
    }

}