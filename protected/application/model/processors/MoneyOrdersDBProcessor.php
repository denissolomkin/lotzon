<?php
Application::import(PATH_INTERFACES . 'IProcessor.php');

class MoneyOrdersDBProcessor implements IProcessor
{
    public function create(Entity $order) 
    {
        $order->setDateOrdered(time());
        $sql = "INSERT INTO `MoneyOrders` (`PlayerId`, `DateOrdered`, `Type`, `Number`, `Sum`, `ItemId`, `Status`, `Data`) VALUES
                                         (:plid, :do, :type, :num, :sum, :item, :status, :data)";

        try {
            $sth = DB::Connect()->prepare($sql)->execute(array(
                ':plid' => $order->getPlayer()->getId(),
                ':do'   => $order->getDateOrdered(),
                ':type' => $order->getType(),
                ':num' => $order->getNumber(),
                ':sum' => $order->getSum(),
                ':item' => $order->getItem() ? $order->getItem()->getId() : null,
                ':status'   => $order->getStatus(),
                ':data'  => is_array($order->getData()) ? serialize($order->getData()) : serialize(array()),
            ));
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query " . $e->getMessage(), 500);
        }

        $order->setId(DB::Connect()->lastInsertId());

        return $order;
    } 

    public function update(Entity $order) 
    {
        $sql = "UPDATE `MoneyOrders` SET `Status` = :status, `UserId` = :userid, `DateProcessed` = :dp WHERE `Id` = :id";
        try {
            $sth = DB::Connect()->prepare($sql)->execute(array(
                ':status' => $order->getStatus(),
                ':userid' => $order->getUserId(),
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
        $sql = "SELECT * FROM `MoneyOrders` WHERE `Id` = :id ORDER BY `DateOrdered` DESC";

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

    public function getOrdersToProcess($limit = 0, $offset = 0, $playerid=0, $status=0, $type=0,$number=0)
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
            if($type){
                $where[]='`Type` = :type';
                $args[':type']=$type;
            }
        }

        $where[]="`Type` != 'points'";
        $sql = "SELECT ((SELECT COUNT(DISTINCT(PlayerId)) FROM `ShopOrders` o WHERE  o.`Number`=`MoneyOrders`.`Number` AND o.`PlayerId`!=`MoneyOrders`.`PlayerId`)
                        +(SELECT COUNT(DISTINCT(PlayerId)) FROM `MoneyOrders` o WHERE  o.`Number`=`MoneyOrders`.`Number` AND o.`PlayerId`!=`MoneyOrders`.`PlayerId`)) Count,
                `Admins`.`Login` UserName, `MoneyOrders`.*
                FROM `MoneyOrders`
                LEFT JOIN `Admins` ON `Admins`.`Id` = `UserId`
                WHERE ".implode(' AND ',$where).$order;

        if ($limit) {
            $sql .= ' LIMIT ' . (int)$limit;
        }
        if ($offset) {
            $sql .= ' OFFSET ' . (int)$offset;
        }

        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute($args);
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query", 500);    
        }

        $orders = array();
        if ($sth->rowCount()) {
            foreach ($sth->fetchAll() as $orderData) {
                if($playerid)
                    unset($orderData['PlayerId']);

                $order = new MoneyOrder();
                $order->formatFrom('DB', $orderData);

                $orders[] = $order;
            }
        }

        return $orders;
    }

    public function getOrdersToProcessCount($status=null,$type=null)
    {
        if(!$status)
            $status=ShopItemOrder::STATUS_ORDERED;

        $sql = "SELECT COUNT(*) FROM `MoneyOrders` WHERE `Type`!='points' AND `Status` = :status".($type?" AND `Type`=:type":'');

        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(($type?array(':status' => $status,':type' => $type,):array(':status' => $status,)));
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query", 500);    
        }

        return $sth->fetchColumn(0);
    }

    public function beginTransaction()
    {
        try {
            DB::Connect()->beginTransaction();
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query", 500);    
        }
        return true;
    }

    public function commit()
    {
        try {
            DB::Connect()->commit();
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query", 500);    
        }
        return true;
    }

    public function rollBack()
    {
        try {
            DB::Connect()->rollBack();
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query", 500);    
        }
        return true;
    }    
}