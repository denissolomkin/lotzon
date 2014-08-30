<?php

Application::import(PATH_INTERFACES . 'IProcessor.php');

class AdminDBProcessor implements IProcessor
{
    public function create(Entity $admin)
    {

    }

    public function update(Entity $admin)
    {
        $sql = "UPDATE `Admins` SET `LastLogin` = :lastlogin, `LastLoginIP` = :lastip WHERE `Login` = :login";

        try {
            $sth = DB::Connect()->prepare($sql)->execute(array(
                ':lastlogin' => $admin->getLastLogin(),
                ':lastip'    => $admin->getLastIp(),
                ':login'     => $admin->getLogin(),
            ));
        } catch (PDOExeption $e) {
            throw new ModelException("Unable to update storage", 500);
        }

        return true;
    }

    public function delete(Entity $admin)
    {

    }

    public function fetch(Entity $admin)
    {

        $sql = "SELECT * FROM `Admins` WHERE `Login` = :login LIMIT 1";

        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(
                ':login' => $admin->getLogin()
            ));
        } catch (PDOExeption $e) {
            throw new ModelException("Unable to get data from storage", 500);
        }
        
        if (!$sth->rowCount()) {
            throw new ModelException("Admin does not exists", 404);   
        }
        $adminRecord = $sth->fetch();

        return $admin->formatFrom('DB', $adminRecord);
    }

}