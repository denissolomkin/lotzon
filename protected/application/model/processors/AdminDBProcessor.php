<?php

Application::import(PATH_INTERFACES . 'IProcessor.php');

class AdminDBProcessor implements IProcessor
{
    public function create(Entity $admin)
    {
        $sql = "INSERT INTO `Admins` (`Login`, `Password`, `Salt`, `Role`) VALUES (:login, :password, :salt, :role);";

        try {
            $sth = DB::Connect()->prepare($sql)->execute(array(
                ':login'    => $admin->getLogin(),
                ':password' => $admin->getPassword(),
                ':salt'     => $admin->getSalt(),
                ':role'     => $admin->getRole(),
            ));
        } catch (PDOException $e) {
            throw new EntityException("Unable to process create query", 500);
        }

        return $admin;
    }

    public function update(Entity $admin)
    {
        $sql = "UPDATE `Admins` SET %s WHERE `Login` = :login";

        $sets = array(
            'sql' => array(),
            'values' => array(),
        );
        
        foreach (array('Password', 'Role', 'LastLogin', 'LastLoginIP', 'Salt') as $field) {
            $getter = 'get' . $field;
            if ($admin->$getter()) {
                $sets['sql'][] = $field .  ' = :' . $field;
                $sets['values'][":" . $field] = $admin->$getter();
            }
        }
        if (!count($sets['values'])) {
            return false;
        }

        $sets['values'][':login'] = $admin->getLogin();
        try {
            $sth = DB::Connect()->prepare(sprintf($sql, join(",", $sets['sql'])))->execute($sets['values']);
        } catch (PDOException $e) {
            throw new ModelException("Unable to update storage", 500);
        }

        return $admin;
    }

    public function delete(Entity $admin)
    {
        $sql = "DELETE FROM `Admins` WHERE `Login` = :login";

        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(
                ':login' => $admin->getLogin()
            ));

        } catch (PDOException $e) {
            throw new ModelException("Unable to process delete query", 500);
        }

        return true;
    }

    public function fetch(Entity $admin)
    {

        $sql = "SELECT * FROM `Admins` WHERE `Login` = :login LIMIT 1";

        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(
                ':login' => $admin->getLogin()
            ));
        } catch (PDOException $e) {
            throw new ModelException("Unable to get data from storage", 500);
        }
        
        if (!$sth->rowCount()) {
            throw new ModelException("Admin does not exists", 404);   
        }
        $adminRecord = $sth->fetch();

        return $admin->formatFrom('DB', $adminRecord);
    }

    public function getList()
    {
        $admins = array();
        $sql = "SELECT * FROM `Admins`";

        try {
            $sth = DB::Connect()->query($sql);
        } catch (PDOException $e) {
            throw new ModelException("Unable to get data from storage", 500);
        }

        if ($sth->rowCount()) {
            $admins = $sth->fetchAll();

            foreach ($admins as &$admin) {
                $adminObj = new Admin();
                $admin = $adminObj->formatFrom('DB', $admin);
            }
        }

        return $admins;
    }

}