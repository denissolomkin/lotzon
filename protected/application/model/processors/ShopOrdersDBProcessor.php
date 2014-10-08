<?php
Application::import(PATH_INTERFACES . 'IProcessor.php');

class ShopOrdersDBProcessor implements IProcessor
{
    public function create(Entity $order) 
    {
        $order->setDateOrdered(time());
        $sql = "INSERT INTO `ShopOrders` (`PlayerId`, `ItemId`, `DateOrdered`, `Name`, `Surname`, `SecondName`, `Phone`, `Region`, `City`, `Address`) VALUES 
                                         (:plid, :aid, :do, :name, :surname, :secname, :phone, :region, :city, :addr)";

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
            ));
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query", 500);
        }

        $order->setId(DB::Connect()->lastInsertId());

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