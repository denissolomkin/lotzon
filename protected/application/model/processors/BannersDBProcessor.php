<?php

Application::import(PATH_INTERFACES . 'IProcessor.php');

class BannersDBProcessor implements IProcessor
{
    public function hitBanner($userId, $device, $location, $page, $title, $country)
    {
        $sql = "INSERT INTO `BannersHit` (`Date`, `UserId`, `Device`, `Location`, `Page`, `Title`, `Country`) VALUES (:date, :userId, :device, :location, :page, :title, :country)";

        try {
            DB::Connect()->prepare($sql)->execute(array(
                ':date'     => time(),
                ':userId'   => $userId,
                ':device'   => $device,
                ':location' => $location,
                ':page'     => $page,
                ':title'    => $title,
                ':country'  => $country,
            ));
        } catch (PDOException $e) {
            throw new ModelException("Unable to proccess storage query", 500);
        }

        return DB::Connect()->lastInsertId();
    }

    public function fetch(Entity $currency)
    {

    }

    public function create(Entity $currency)
    {

    }

    public function update(Entity $country)
    {

    }

    public function delete(Entity $country)
    {

    }
}