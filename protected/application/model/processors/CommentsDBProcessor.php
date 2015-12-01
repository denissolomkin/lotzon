<?php

class CommentsDBProcessor implements IProcessor
{
    public function create(Entity $comment) 
    {
        $sql = "REPLACE INTO `PlayerReviews` (`Id`, `ParentId`, `ToPlayerId`, `PlayerId`, `Text`, `Date`, `Image`, `IsPromo`, `Status`, `AdminId`, `ModifyDate`, `Module`, `ObjectId`) VALUES (:id, :parentid, :toplayerid, :playerid, :text, :date, :image, :ispromo, :status, :adminid, :modifydate, :module, :objectid)";

        try {
            $sth = DB::Connect()->prepare($sql)->execute(array(
                ':id'         => $comment->getId(),
                ':playerid'   => $comment->getPlayerId(),
                ':toplayerid' => $comment->getToPlayerId()?:null,
                ':parentid'   => $comment->getParentId()?:null,
                ':text'       => $comment->getText(),
                ':date'       => $comment->getDate()?:time(),
                ':image'      => $comment->getImage(),
                ':ispromo'    => $comment->getIsPromo(),
                ':status'     => $comment->getStatus(),
                ':adminid'    => $comment->getAdminId(),
                ':module'     => $comment->getModule(),
                ':objectid'   => $comment->getObjectId(),
                ':modifydate' => time(),
            ));
        } catch (PDOExeption $e) {
            throw new ModelException("Unable to proccess storage query", 500);
        }

        return $comment;
    }

    public function update(Entity $comment)
    {
        $sql = "UPDATE `PlayerReviews` SET `Status` = :status, `Text` = :text, `AdminId` = :adminid, `ModifyDate` = :modifydate WHERE `Id` = :id";

        try {
            $sth = DB::Connect()->prepare($sql)->execute(array(
                ':id'         => $comment->getId(),
                ':status'     => $comment->getStatus(),
                ':text'       => $comment->getText(),
                ':adminid'    => $comment->getUserId(),
                ':modifydate' => time(),
            ));
        } catch (PDOexception $e) {
            throw new ModelException("Unable to proccess storage query", 500);
        }

        return $comment;
    }

    public function delete(Entity $comment)
    {
        $sql = "DELETE FROM `PlayerReviews` WHERE `Id` = :id";

        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(
                ':id' => $comment->getId()
            ));

        } catch (PDOExeption $e) {
            throw new ModelException("Unable to process delete query", 500);
        }

        return true;
    }

    public function fetch(Entity $comment)
    {
        $sql = "SELECT
                    `PlayerReviews`.*,
                    `Players`.`Avatar` PlayerImg,
                    `Players`.`Nicname` PlayerName,
                    (SELECT COUNT(*) FROM `PlayerReviewsLikes` WHERE `PlayerReviewsLikes`.CommentId=`PlayerReviews`.Id) AS LikesCount
                FROM `PlayerReviews`
                LEFT JOIN
                    `Players`
                  ON
                    `Players`.`Id` = `PlayerReviews`.`PlayerId`
                WHERE
                    `PlayerReviews`.`Id` = :id
                LIMIT 1";

        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(
                ':id'    => $comment->getId(),
            ));
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query", 500);
        }

        if (!$sth->rowCount()) {
            throw new ModelException("Review not found", 404);
        }

        $data = $sth->fetch();
        $comment->formatFrom('DB', $data);

        return $comment;
    }

    public function getList($module, $objectId, $count, $beforeId = NULL, $afterId = NULL, $status = 1, $parentId = NULL)
    {
        $sql = "SELECT
                    `PlayerReviews`.*,
                    `Players`.`Avatar` PlayerImg,
                    `Players`.`Nicname` PlayerName,
                    (SELECT COUNT(*) FROM `PlayerReviewsLikes` WHERE `PlayerReviewsLikes`.CommentId=`PlayerReviews`.Id) AS LikesCount
                FROM `PlayerReviews`
                LEFT JOIN
                    `Players`
                  ON
                    `Players`.`Id` = `PlayerReviews`.`PlayerId`
                WHERE
                    `Module` = :module
                AND
                    `Status` = :status
                AND
                    `ObjectId` = :objectId"
                . (($parentId === NULL) ? " AND (`ParentId` IS NULL)" : " AND (`PlayerReviews`.`ParentId` = $parentId)")
                . (($beforeId === NULL) ? "" : " AND (`PlayerReviews`.`Id` < $beforeId)")
                . (($afterId === NULL)  ? "" : " AND (`PlayerReviews`.`Id` > $afterId)")
                . "
                ORDER BY `PlayerReviews`.`Id` DESC
                LIMIT " . (int)$count;
        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(
                ':module'   => $module,
                ':objectId' => $objectId,
                ':status'   => $status,
            ));
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query " . $e, 1);
        }

        $comments = array();
        foreach ($sth->fetchAll() as $commentData) {
            $comment = new \Comment;
            $comments[$commentData['Id']] = $comment->formatFrom('DB',$commentData)->export('JSON');
            $comments[$commentData['Id']]['answers'] = $this->getList($module, $objectId, $count, $beforeId = NULL, $afterId = NULL, $status, $commentData['Id']);
        }

        return $comments;
    }
}
