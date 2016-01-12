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

    public function getCount($module, $objectId, $status = 1)
    {
        $sql = "SELECT
                    count(*) as c
                FROM
                  `PlayerReviews`
                WHERE
                    `Module` = :module
                AND
                    `Status` = :status
                AND
                    `ObjectId` = :objectId";
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

        $count = $sth->fetch()['c'];

        return $count;
    }

    public function getList($module, $objectId, $count = NULL, $beforeId = NULL, $afterId = NULL, $status = 1, $parentId = NULL, $modifyDate = NULL)
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
                . (($modifyDate === NULL)  ? "" : " AND (`PlayerReviews`.`ModifyDate` > $modifyDate)
                                                    OR (`PlayerReviews`.Id IN
                                                        (SELECT ParentId FROM PlayerReviews WHERE
                                                                   `Module` = :module
                                                                AND
                                                                    `Status` = :status
                                                                AND
                                                                    `ObjectId` = :objectId
                                                                AND
                                                                    `ModifyDate` > $modifyDate)
                                                        )")
                . "
                ORDER BY `PlayerReviews`.`Id` DESC"
                . (($count === NULL)  ? "" : " LIMIT " . (int)$count);
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
            if (!$commentData['ParentId']) {
                $comments[$commentData['Id']]['answers'] = $this->getList($module, $objectId, $count, $beforeId = NULL, $afterId = NULL, $status, $commentData['Id']);
            } else {
                $comments[$commentData['Id']]['comment_id'] = $commentData['ParentId'];
            }
        }

        return $comments;
    }

    public function getLikes($commentId)
    {
        $sql = "SELECT
                    count(*) as c
                FROM
                  `PlayerReviewsLikes`
                WHERE
                  CommentId=:commentid";

        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(
                ':commentid' => $commentId,
            ));
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query", 500);
        }

        $count = $sth->fetch()['c'];

        return $count;
    }

    public function isLiked($commentId, $playerId)
    {
        $sql = "SELECT
                    *
                FROM
                  `PlayerReviewsLikes`
                WHERE
                  CommentId=:commentid
                AND
                  PlayerId=:playerid";

        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(
                ':commentid' => $commentId,
                ':playerid'  => $playerId,
            ));
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query", 500);
        }

        if (!$sth->rowCount()) {
            return false;
        }

        return true;
    }

    public function like($commentId, $playerId)
    {
        $sql = "INSERT INTO `PlayerReviewsLikes` (`CommentId`, `PlayerId`) VALUES (:commentid, :playerid)";

        try {
            $sth = DB::Connect()->prepare($sql)->execute(array(
                ':commentid' => $commentId,
                ':playerid'  => $playerId,
            ));
        } catch (PDOExeption $e) {
            throw new ModelException("Like already set", 500);
        }

        return true;
    }

    public function dislike($commentId, $playerId)
    {
        $sql = "DELETE FROM `PlayerReviewsLikes` WHERE `CommentId`=:commentid AND `PlayerId`=:playerid";

        try {
            $sth = DB::Connect()->prepare($sql)->execute(array(
                ':commentid' => $commentId,
                ':playerid'  => $playerId,
            ));
        } catch (PDOExeption $e) {
            throw new ModelException("Like not set", 500);
        }

        return true;
    }

    public function setNotificationsDate($playerId, $time = NULL)
    {
        $sql = "UPDATE `PlayerDates` SET `Notification` = :date WHERE `PlayerId` = :playerid";

        try {
            $sth = DB::Connect()->prepare($sql)->execute(array(
                ':playerid' => $playerId,
                ':date'     => ($time ? $time : time()),
            ));
        } catch (PDOexception $e) {
            throw new ModelException("Unable to proccess storage query", 500);
        }

        return true;
    }

    public function getNotificationsCount($playerId, $module = 'comments', $objectId = 0)
    {
        $sql = "SELECT
                    count(*) as c
                FROM `PlayerReviews` AS pr
                LEFT JOIN
                    `PlayerReviews` AS prparent
                ON
                    prparent.`Id` = pr.`ParentId`
                LEFT JOIN
                    `Players`
                ON
                    `Players`.`Id` = pr.`PlayerId`
                JOIN
                    `PlayerDates` AS pd
                ON
                    pd.`PlayerId` = :playerid
                WHERE
                    pr.`Module` = :module
                AND
                    pr.`Status` = 1
                AND
                    pr.`ObjectId` = :objectId
                AND
                    pr.`Date` > pd.`Notification`
                AND (
                        pr.`ToPlayerId` = :playerid
                    OR
                        prparent.`PlayerId` = :playerid
                    )
                AND
                    pr.`PlayerId`<>:playerid";
        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(
                ':module'   => $module,
                ':objectId' => $objectId,
                ':playerid' => $playerId,
            ));
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query " . $e, 1);
        }

        $count = $sth->fetch()['c'];

        return $count;
    }

    public function getNotificationsList($playerId, $count = 10, $offset = NULL, $module = 'comments', $objectId = 0)
    {
        $sql = "SELECT
                    pr.*,
                    `Players`.`Avatar` PlayerImg,
                    `Players`.`Nicname` PlayerName,
                    prparent.`Text` ParentText
                FROM `PlayerReviews` AS pr
                LEFT JOIN
                    `PlayerReviews` AS prparent
                ON
                    prparent.`Id` = pr.`ParentId`
                LEFT JOIN
                    `Players`
                ON
                    `Players`.`Id` = pr.`PlayerId`
                JOIN
                    `PlayerDates` AS pd
                ON
                    pd.`PlayerId` = :playerid
                WHERE
                    pr.`Module` = :module
                AND
                    pr.`Status` = 1
                AND
                    pr.`ObjectId` = :objectId
                AND
                    pr.`Date` > pd.`Notification`
                AND (
                        pr.`ToPlayerId` = :playerid
                    OR
                        prparent.`PlayerId` = :playerid
                    )
                AND
                    pr.`PlayerId`<>:playerid
                ORDER BY pr.`Id`"
            . (($count === NULL)  ? "" : " LIMIT " . (int)$count);
        if ($offset) {
            $sql .= " OFFSET " . (int)$offset;
        }
        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(
                ':module'   => $module,
                ':objectId' => $objectId,
                ':playerid' => $playerId,
            ));
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query " . $e, 1);
        }

        return $sth->fetchAll();
    }

}
