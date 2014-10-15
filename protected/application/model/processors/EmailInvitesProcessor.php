<?php

Application::import(PATH_INTERFACES . 'IProcessor.php');

class EmailInvitesProcessor implements IProcessor
{
    public function create(Entity $invite)
    {
        $invite->setDate(time());
        $sql = "INSERT INTO `EmailInvites` (`Email`, `Date`, `InviterId`) VALUES (:email, :date, :inid)";

        try {
            $sth = DB::Connect()->prepare($sql)->execute(array(
                ':email' => $invite->getEmail(),
                ':date' => $invite->getDate(),
                ':inid' => $invite->getInviter()->getId(),
            ));
        } catch (PDOExeption $e) {
            throw new ModelException("Unable to proccess storage query", 500);            
        }

        return $invite;
    }

    public function update(Entity $invite)
    {
        return $invite;
    }

    public function delete(Entity $invite)
    {
        $sql = "DELETE FROM `EmailInvites` WHERE `Id` = :id";
        try {
            DB::Connect()->prepare($sql)->execute(array(
                ':id'   => $invite->getId(),
            )); 
        } catch (PDOExeption $e) {
            throw new ModelException("Unable to proccess storage query", 500);  
        }

        return true;
    }

    public function getInvite($email) 
    {

        $sql = "SELECT * FROM `EmailInvites` WHERE `Email` = :email LIMIT 1";
        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(
                ':email' => $email,
            ));
        } catch (PDOException $e) {
            throw new ModelException("Unable to proccess storage query", 500);
        }

        if ($sth->rowCount()) {
            $row = $sth->fetch();
            $invite = new EmailInvite();
            $invite->setId($row['Id'])
                   ->setDate($row['Date'])
                   ->setEmail($row['Email']);

            $inviter = new Player();

            try {
                $inviter->setId($row['InviterId'])->fetch();
                $invite->setInviter($inviter);

            } catch (EntityException $e) {
                if ($e->getCode() == 404) {
                    $invite->delete();
                }
                return false;
            }

            return $invite;
        } else {
            throw new ModelException('NOT_FOUND', 404);
        }

        return false;
    }

    public function fetch(Entity $invite)
    {
        return $invite;
    }
}