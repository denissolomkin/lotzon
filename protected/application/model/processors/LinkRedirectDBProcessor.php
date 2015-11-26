<?php

Application::import(PATH_INTERFACES . 'IProcessor.php');

class LinkRedirectDBProcessor implements IProcessor
{
    public function create(Entity $linkRedirect)
    {
        $sql = "INSERT INTO `LinkRedirect` (`Uin`, `Link`) VALUES (:uin, :link)";

        try {
            $sth = DB::Connect()->prepare($sql)->execute(array(
                ':uin'   => $linkRedirect->getUin(),
                ':link'  => $linkRedirect->getLink(),
            ));
        } catch (PDOExeption $e) {
            throw new ModelException("Unable to proccess storage query", 500);
        }

        return $linkRedirect;
    }

    public function update(Entity $linkRedirect)
    {
        return $linkRedirect;
    }

    public function delete(Entity $linkRedirect)
    {
        $sql = "DELETE FROM `LinkRedirect` WHERE `Uin` = :uin";

        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(
                ':uin' => $linkRedirect->getUin()
            ));

        } catch (PDOExeption $e) {
            throw new ModelException("Unable to process delete query", 500);
        }

        return true;
    }

    public function fetch(Entity $linkRedirect)
    {
        $sql = "SELECT * FROM `LinkRedirect` WHERE `Uin` = :uin";
        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(
                ':uin'    => $linkRedirect->getUin(),
            ));
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query", 500);
        }

        if (!$sth->rowCount()) {
            throw new ModelException("Link not found", 404);
        }
        $data = $sth->fetch();
        $linkRedirect->formatFrom('DB', $data);

        return $linkRedirect;
    }

    public function getUin($link = '')
    {
        $sql = "SELECT `Uin` FROM `LinkRedirect` WHERE `link` = :link";
        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(
                ':link' => $link,
            ));
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query", 500);
        }

        if (!$sth->rowCount()) {
            return NULL;
        }
        $data = $sth->fetch();

        return $data['Uin'];
    }
}
