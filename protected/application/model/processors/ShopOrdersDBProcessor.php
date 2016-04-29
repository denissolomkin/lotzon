<?php
Application::import(PATH_APPLICATION . 'DBProcessor.php');

class ShopOrdersDBProcessor extends DBProcessor
{
    public function create(Entity $order) 
    {
        $order->setDateOrdered(time());
        $sql = "INSERT INTO `ShopOrders` (`PlayerId`, `ItemId`, `Number`, `Sum`, `Equivalent`, `DateOrdered`, `Name`, `Surname`, `SecondName`, `Phone`, `Region`, `City`, `Address`, `ChanceGameId`) VALUES
                                         (:plid, :aid, :num, :sum, :eq, :do, :name, :surname, :secname, :phone, :region, :city, :addr, :cgid)";

        try {
            $sth = DB::Connect()->prepare($sql)->execute(array(
                ':plid' => $order->getPlayer()->getId(),
                ':aid'  => $order->getItem()->getId(),
                ':num'   => $order->getNumber(),
                ':do'   => $order->getDateOrdered(),
                ':name' => $order->getName(),
                ':surname'  => $order->getSurname(),
                ':secname'  => $order->getSecondName(),
                ':sum'      => $order->getSum(),
                ':eq'       => $order->getEquivalent(),
                ':phone'    => $order->getPhone(),
                ':region'   => $order->getRegion(),
                ':city'     => $order->getCity(),
                ':addr'     => $order->getAddress(),  
                ':cgid'     => $order->getChanceGameId(),
            ));
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query " . $e->getMessage(), 500);
        }

        $order->setId(DB::Connect()->lastInsertId());

        if ($order->getChanceGameId()) {
            try {
                DB::Connect()->prepare("UPDATE `ChanceGameWins` SET `OrderRecieved` = 1 WHERE `Id` = :id")->execute(array(
                    ':id' => $order->getChanceGameId(),
                ));    
            } catch (PDOException $e) {
                throw new ModelException("Error processing storage query", 500);       
            }
        }

        return $order;
    } 

    public function update(Entity $order) 
    {
        $sql = "UPDATE `ShopOrders` SET `Status` = :status, `AdminId` = :adminid, `DateProcessed` = :dp WHERE `Id` = :id";
        try {
            $sth = DB::Connect()->prepare($sql)->execute(array(
                ':status' => $order->getStatus(),
                ':adminid' => $order->getAdminId(),
                ':dp'  => $order->getDateProcessed(),
                ':id'   => $order->getId(),
            ));
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query " . $e->getMessage(), 500);
        }
        return $order;
    } 

    public function delete(Entity $order) 
    {
        
    } 

    public function fetch(Entity $order) 
    {
        $sql = "SELECT * FROM `ShopOrders` WHERE `Id` = :id ORDER BY `DateOrdered` DESC";

        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(
                ':id' => $order->getId(),
            ));
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query", 500);    
        }
        $data = $sth->fetch();
        $order->formatFrom('DB', $data);

        return $order;
    } 

    public function process(Entity $order) 
    {
        
    } 

    public function getOrdersToProcess($limit = 0, $offset = 0, $playerid=0, $status=0, $number=0)
    {

        $order=" ORDER BY `Id` ASC";

        if($number){
            $where[]='`Number` = :num';
            $args[':num']=$number;
        }elseif($playerid){
            $order=" ORDER BY `Id` DESC";
            $where[]='`PlayerId` = :pid';
            $args[':pid']=$playerid;
            if($status){
                $where[]='`Status` = :status';
                $args[':status']=$status;}
        }else{
            $where[]='`Status` = :status';
            $args[':status']=($status?:ShopItemOrder::STATUS_ORDERED);
        }

        $sql = "SELECT ((SELECT COUNT(DISTINCT(PlayerId)) FROM `ShopOrders` o WHERE  o.`Number`=`ShopOrders`.`Number` AND o.`PlayerId`!=`ShopOrders`.`PlayerId`)
                        +(SELECT COUNT(DISTINCT(PlayerId)) FROM `MoneyOrders` o WHERE  o.`Number`=`ShopOrders`.`Number` AND o.`PlayerId`!=`ShopOrders`.`PlayerId`)) Count,
                `Admins`.`Login` AdminName, `ShopOrders`.*
                FROM `ShopOrders`
                LEFT JOIN `Admins` ON `Admins`.`Id` = `AdminId`
                WHERE ".implode('AND',$where).$order;

        if ($limit) {
            $sql .= ' LIMIT ' . (int)$limit;
        }
        if ($offset) {
            $sql .= ' OFFSET ' . (int)$offset;
        }

        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute($args);
        } catch (PDOException $e) {echo $e->getMessage();
            throw new ModelException("Error processing storage query", 500);    
        }

        $orders = array();
        if ($sth->rowCount()) {
            foreach ($sth->fetchAll() as $orderData) {

                if($playerid)
                    unset($orderData['PlayerId']);

                $order = new ShopItemOrder();
                $order->formatFrom('DB', $orderData);

                $orders[] = $order;
            }
        }
        return $orders;
    }

    public function getOrdersToProcessCount($status=null)
    {
        if(!$status)
            $status=ShopItemOrder::STATUS_ORDERED;

        $sql = "SELECT COUNT(*) FROM `ShopOrders` WHERE `Status` = :status";

        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(
                ':status' => $status,
            ));
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query", 500);    
        }

        return $sth->fetchColumn(0);
    }

    public function getOrdersList($playerId, $limit = NULL, $offset = NULL)
    {
        $sql = "SELECT
                so.`DateOrdered` as date,
                'shop'           as type,
                so.`Address`     as data,
                si.`Title`       as prize,
                NULL             as sum,
                so.`Status`      as status
                FROM `ShopOrders` as so
                JOIN `ShopItems` as si
                ON si.`Id` = so.`ItemId`
                WHERE
                so.`PlayerId` = :plid
                UNION
                SELECT
                mo.`DateOrdered` as date,
                mo.`Type`        as type,
                mo.`Number`      as data,
                mo.`Equivalent`  as prize,
                mo.`Sum`         as sum,
                mo.`Status`      as status
                FROM `MoneyOrders` as mo
                WHERE
                mo.`PlayerId` = :plid
                AND
                mo.`Type` <> 'points'
                ORDER BY date DESC";
        if (!is_null($limit)) {
            $sql .= " LIMIT " . (int)$limit;
        }
        if (!is_null($offset)) {
            $sql .= " OFFSET " . (int)$offset;
        }
        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(
                ':plid' => $playerId,
            ));
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query", 500);
        }

        return $sth->fetchAll();
    }

}