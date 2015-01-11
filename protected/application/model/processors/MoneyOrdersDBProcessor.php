<?php
Application::import(PATH_INTERFACES . 'IProcessor.php');

class MoneyOrdersDBProcessor implements IProcessor
{
    public function create(Entity $order) 
    {
        $order->setDateOrdered(time());
        $sql = "INSERT INTO `MoneyOrders` (`PlayerId`, `DateOrdered`, `Type`, `Status`, `Data`) VALUES 
                                         (:plid, :do, :type, :status, :data)";

        try {
            $sth = DB::Connect()->prepare($sql)->execute(array(
                ':plid' => $order->getPlayer()->getId(),
                ':do'   => $order->getDateOrdered(),
                ':type' => $order->getType(),
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
        $sql = "UPDATE `MoneyOrders` SET `Status` = :status, `DateProcessed` = :dp WHERE `Id` = :id";
        try {
            $sth = DB::Connect()->prepare($sql)->execute(array(
                ':status' => $order->getStatus(),
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

    public function getOrdersToProcess($limit = 0, $offset = 0, $playerid=0, $status=0)
    {
        if($playerid){
            $order=" ORDER BY `Id` DESC";
            $where[]='`PlayerId` = :pid';
            if($status)
                $where[]='`Status` = :status';

        }else{
            $order=" ORDER BY `Id` ASC";
            $where[]='`Status` = :status';
            $status=($status?:ShopItemOrder::STATUS_ORDERED);
        }


        $sql = "SELECT * FROM `MoneyOrders` WHERE ".implode('AND',$where).$order;

        if ($limit) {
            $sql .= ' LIMIT ' . (int)$limit;
        }
        if ($offset) {
            $sql .= ' OFFSET ' . (int)$offset;
        }

        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(
                ($playerid && $status?array(':pid' => $playerid,':status' => $status):$playerid?array(':pid' => $playerid):array(':status' => $status))
            );
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

    public function getOrdersToProcessCount($playerid=null,$status=null)
    {
        if(!$status)
            $status=ShopItemOrder::STATUS_ORDERED;

        $sql = "SELECT COUNT(*) FROM `MoneyOrders` WHERE `Status` = :status";

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
}