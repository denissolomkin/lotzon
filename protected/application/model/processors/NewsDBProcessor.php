<?php

Application::import(PATH_INTERFACES . 'IProcessor.php');

class NewsDBProcessor implements IProcessor
{
    public function create(Entity $news)
    {
        $sql = "INSERT INTO `News` (`Id`, `Lang`, `Title`, `Date`, `Text`) VALUES (:id, :lang, :title, :date, :text)";

        try {
            $sth = DB::Connect()->prepare($sql)->execute(array(
                ':id'    => $news->getId(),
                ':lang'  => $news->getLang(),
                ':title' => $news->getTitle(),
                ':date'  => time(),
                ':text'  => $news->getText(),
            ));
        } catch (PDOExeption $e) {
            throw new ModelException("Unable to proccess storage query", 500);            
        }

        return $news;
    }

    public function update(Entity $news)
    {
        $sql = "UPDATE `News` SET `Title` = :title, `Text` = :text WHERE `Id` = :id";

        try {
            $sth = DB::Connect()->prepare($sql)->execute(array(
                ':id'    => $news->getId(),
                ':title' => $news->getTitle(),
                ':text'  => $news->getText(),
            ));       
        } catch (PDOexception $e) {
            throw new ModelException("Unable to proccess storage query", 500);    
        }

        return $news;
    }

    public function delete(Entity $news)
    {
        $sql = "DELETE FROM `News` WHERE `Id` = :id";

        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(
                ':id' => $news->getId()
            ));

        } catch (PDOExeption $e) {
            throw new ModelException("Unable to process delete query", 500);
        }

        return true;
    }

    public function fetch(Entity $news)
    {


    }

    public function getList($lang, $limit = null, $offset = null)
    {
        $sql = "SELECT * FROM `News` WHERE `Lang` = :lang ORDER BY `Date` DESC";
        if (!is_null($limit)) {
            $sql .= " LIMIT " . (int)$limit; 
        }
        if (!is_null($offset)) {
            $sql .= " OFFSET " . (int)$offset;
        }
        
        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(
                ':lang' => $lang,
            ));
        } catch (PDOExeption $e) {
            throw new ModelException("Unable to proccess storage query", 500);   
        }

        $news = array();
        
        $list = $sth->fetchAll();
        if (count($list)) {
            foreach ($list as $newsData) {
                $newsObj = new News();
                $news[] = $newsObj->formatFrom('DB', $newsData);   
            }
        }

        return $news;
    }

}