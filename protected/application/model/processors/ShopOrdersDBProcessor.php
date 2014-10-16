<?php
Application::import(PATH_INTERFACES . 'IProcessor.php');

class ShopOrdersDBProcessor implements IProcessor
{
    public function create(Entity $order) 
    {
        $order->setDateOrdered(time());
        $sql = "INSERT INTO `ShopOrders` (`PlayerId`, `ItemId`, `DateOrdered`, `Name`, `Surname`, `SecondName`, `Phone`, `Region`, `City`, `Address`, `ChanceGameId`) VALUES 
                                         (:plid, :aid, :do, :name, :surname, :secname, :phone, :region, :city, :addr, :cgid)";

        try {
            $sth = DB::Connect()->prepare($sql)->execute(array(
                ':plid' => $order->getPlayer()->getId(),
                ':aid'  => $order->getItem()->getId(),
                ':do'   => $order->getDateOrdered(),
                ':name' => $order->getName(),
                ':surname'  => $order->getSurname(),
                ':secname'  => $order->getSecondName(),
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
        
    } 

    public function delete(Entity $order) 
    {
        
    } 

    public function fetch(Entity $order) 
    {
        
    } 

    public function process(Entity $order) 
    {
        
    } 
}