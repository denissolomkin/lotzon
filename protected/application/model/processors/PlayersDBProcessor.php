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
        $sql = "UPDATE `Players` SET  
                    `DateLogined` = :dl, `Country` = :cc, 
                    `Nicname` = :nic, `Name` = :name, `Surname` = :surname, `SecondName` = :secname, 
                    `Phone` = :phone, `Birthday` = :bd, `Avatar` = :avatar, `Visible` = :vis, `Favorite` = :fav,
                    `Money` = :money, `Points`  = :points, `GamesPlayed` = :gp
                WHERE `Id` = :id OR `Email` = :email";

        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(
                ':dl'     => $player->getDateLastLogin(),
                ':cc'     => $player->getCountry(),
                ':nic'    => $player->getNicname(),
                ':name'   => $player->getName(),
                ':surname'  => $player->getSurname(),
                ':secname'  => $player->getSecondName(),
                ':phone'    => $player->getPhone(),
                ':bd'       => $player->getBirthday(),
                ':avatar'   => $player->getAvatar(),
                ':id'       => $player->getId(),
                ':email'    => $player->getEmail(),
                ':vis'      => (int)$player->getVisibility(),
                ':fav'      => is_array($player->getFavoriteCombination()) ? serialize($player->getFavoriteCombination()) : '',
                ':money'    => $player->getMoney(),
                ':points'   => $player->getPoints(),
                ':gp'       => $player->getGamesPlayed(),
            ));
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query" . $e->getMessage(), 500);   
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

    public function getPlayersCount()
    {
        $sql = "SELECT COUNT(*) as `counter`  FROM `Players`";

        try {
            $res = DB::Connect()->query($sql);
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query", 500);
        }

        return $res->fetchColumn(0);
    }

    public function getList($limit = 0, $offset = 0) 
    {
        $sql = "SELECT * FROM `Players`";

        if ($limit) {
            $sql .= ' LIMIT ' . (int)$limit;
        }
        if ($offset) {
            $sql .= ' OFFSET ' . (int)$offset;
        }

        try {
            $res = DB::Connect()->prepare($sql);
            $res->execute();
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query", 500);   
        }

        $players = array();
        foreach ($res->fetchAll() as $playerData) {
            $player = new Player();
            $player->formatFrom('DB', $playerData);   

            $players[] = $player;
        }

        return $players;
    }
}