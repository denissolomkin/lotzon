<?php

class CommentsDBProcessor implements IProcessor
{
    public function create(Entity $comment) 
    {
        $sql = "INSERT INTO `Comments` (`Author`, `Link`, `Avatar`, `Text`, `Date`) VALUES (:author, :link, :avatar, :text, :date)";

        try {
            DB::Connect()->prepare($sql)->execute(array(
                ':author' => $comment->getAuthor(),
                ':link' => $comment->getLink(),
                ':avatar' => $comment->getAvatar(),
                ':text' => $comment->getText(),
                ':date' => $comment->getDate(),
            ));
        } catch (PDOException $e) {
            throw new ModelException ("Error processing storage query", 500);
        }

        $comment->setId(DB::Connect()->lastInsertId());

        return $comment;
    }

    public function update(Entity $comment) 
    {

    }

    public function delete(Entity $comment) 
    {
        $sql = "DELETE FROM `Comments` WHERE `Id` = :id";

        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(
                ':id'   => $comment->getId(),
            ));
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query", 1);
        }        

        return true;
    }

    public function fetch(Entity $comment)
    {
        $sql = "SELECT * FROM `Comments` WHERE `Id` = :id";

        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(
                ':id'   => $comment->getId(),
            ));
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query", 1);
        }
        $commentData = $sth->fetch();
        
        if ($commentData) {
            $comment = new Comment();
            $comment->setId($commentData['Id']) 
                ->setAvatar($commentData['Avatar'])
                ->setText($commentData['Text'])
                ->setLink($commentData['Link'])
                ->setAuthor($commentData['Author'])
                ->setDate($commentData['Date']);    
        } 

        return $comment;
    }

    public function getList()
    {
        $sql = "SELECT * FROM `Comments` ORDER BY `Date` DESC";

        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute();
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query", 1);
        }

        $comments = array();
        if ($sth->rowCount()) {
            $data = $sth->fetchAll();

            foreach ($data as $commentData) {
                $comment = new Comment();
                $comment->setId($commentData['Id']) 
                        ->setAvatar($commentData['Avatar'])
                        ->setText($commentData['Text'])
                        ->setLink($commentData['Link'])
                        ->setAuthor($commentData['Author'])
                        ->setDate($commentData['Date']);
                $comments[] = $comment;
            }
        }

        return $comments;
    }
}