<?php

Application::import(PATH_INTERFACES . 'IProcessor.php');

class PlayersDBProcessor implements IProcessor
{
    public function create(Entity $player)
    {
        $sql = "INSERT INTO `Players` (`Email`, `Password`, `Salt`, `DateRegistered`, `DateLogined`, `Country`) VALUES (:email, :passwd, :salt, :dr, :dl, :cc)";

        try {
            DB::Connect()->prepare($sql)->execute(array(
                ':email'    => $player->getEmail(),
                ':passwd'   => $player->getPassword(),
                ':salt'     => $player->getSalt(),
                ':dr'       => time(),
                ':dl'       => time(),
                ':cc'       => $player->getCountry(),
            ));
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query", 500);            
        }

        $player->setId(DB::Connect()->lastInsertId());

        return $player;
    }

    public function update(Entity $player)
    {   
        $sql = "UPDATE `Players` SET  `Password` = :passwd, `Salt` = :salt, `DateLogined` = :dl, `Country` = :cc, `Nicname` = :nic, `Name` = :name, `Surname` = ':surname', `SecondName` = :secname, `Phone` = :phone, `Birthday` = :bd, `Avatar` = :avatar WHERE `Id` = :id OR `Email` = :email";

        try {
            DB::Connect()->prepare($sql)->execute(array(
                ':passwd' => $player->getPassword(),
                ':salt'   => $player->getSalt(),
                ':dl'     => $player->getDateLogined(),
                ':cc'     => $player->getCountry(),
                ':nic'    => $player->getNic(),
                ':name'   => $player->getName(),
                ':surname'  => $player->getSurname(),
                ':secname'  => $player->getSecondName(),
                ':phone'    => $player->getPhone(),
                ':bd'       => $player->getBirthday(),
                ':avatar'   => $player->getAvatar(),
                ':id'       => $player->getId(),
                ':email'    => $player->getEmail(),
            ));
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query", 500);   
        }

        return $player;
    }

    public function fetch(Entity $player)
    {
        $sql = "SELECT * FROM `Players` WHERE `Id` = :id OR `Email` = :email LIMIT 1";

        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(
                ':id'    => $player->getId(),
                ':email' => $player->getEmail(),
            ));
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query", 500);      
        }

        if (!$sth->rowCount()) {
            throw new ModelException("Player not found", 404);
        }

        $data = $sth->fetch();
        $player->formatFrom('DB', $data);

        return $player;
    }

    public function delete(Entity $player)
    { 
        return true;
    }
}