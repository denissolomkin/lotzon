<?php

Application::import(PATH_APPLICATION . 'DBProcessor.php');

class ReviewsDBProcessor extends DBProcessor
{
    public function create(Entity $review)
    {
        $sql = "REPLACE INTO `PlayerReviews` (`Id`, `ParentId`, `ToPlayerId`, `PlayerId`, `Text`, `Date`, `Image`, `IsPromo`, `Status`, `AdminId`, `ModifyDate`, `Module`, `ObjectId`)
                VALUES (:id, :parentid, :toplayerid, :playerid, :text, :date, :image, :ispromo, :status, :adminid, :modifydate, :module, :objectid)";

        try {
            $sth = DB::Connect()->prepare($sql)->execute(array(
                ':id'         => $review->getId(),
                ':playerid'   => $review->getPlayerId(),
                ':toplayerid' => $review->getToPlayerId(),
                ':parentid'   => $review->getReviewId()?:null,
                ':text'       => $review->getText(),
                ':date'       => $review->getDate()?:time(),
                ':image'      => $review->getImage(),
                ':ispromo'    => $review->isPromo(),
                ':status'     => $review->getStatus(),
                ':adminid'    => $review->getUserId(),
                ':modifydate' => time(),
                ':module'     => $review->getModule(),
                ':objectid'   => $review->getObjectId(),
            ));
        } catch (PDOExeption $e) {
            throw new ModelException("Unable to proccess storage query", 500);            
        }

        return $review;
    }

    public function update(Entity $review)
    {
        $sql = "UPDATE `PlayerReviews` SET `Status` = :status, `Text` = :text, `AdminId` = :adminid, `ModifyDate` = :modifydate WHERE `Id` = :id";

        try {
            DB::Connect()->prepare($sql)->execute(array(
                ':id'         => $review->getId(),
                ':status'     => $review->getStatus(),
                ':text'       => $review->getText(),
                ':adminid'    => $review->getUserId(),
                ':modifydate' => time(),
            ));       
        } catch (PDOexception $e) {
            throw new ModelException("Unable to proccess storage query", 500);    
        }

        return $review;
    }

    public function delete(Entity $review)
    {
        $sql = "DELETE FROM `PlayerReviews` WHERE `Id` = :id";

        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(
                ':id' => $review->getId()
            ));

        } catch (PDOExeption $e) {
            throw new ModelException("Unable to process delete query", 500);
        }

        return true;
    }

    public function fetch(Entity $review)
    {
        $sql = "SELECT `PlayerReviews`.*, `Players`.`Avatar` PlayerAvatar,`Players`.`Nicname` PlayerName,`Admins`.`Login` UserName
                FROM `PlayerReviews`
                LEFT JOIN `Players` ON `Players`.`Id` = `PlayerReviews`.`PlayerId`
                LEFT JOIN `Admins` ON `Admins`.`Id` = `PlayerReviews`.`AdminId`
                WHERE `PlayerReviews`.`Id` = :id
                LIMIT 1";

        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(
                ':id'    => $review->getId(),
            ));
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query", 500);
        }

        if (!$sth->rowCount()) {
            throw new ModelException("Review not found", 404);
        }
        $data = $sth->fetch();

        $review->formatFrom('DB', $data);

        return $review;
    }

    public function imageExists($image)
    {
        $sql = "SELECT 1
                FROM `PlayerReviews`
                WHERE `Image` = :image
                LIMIT 1";

        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(
                ':image'    => $image
            ));
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query", 500);
        }

        return $sth->fetchColumn(0);
    }


    public function getReview($id)
    {
            $sql = "SELECT `PlayerReviews`.*, `Players`.`Email` PlayerEmail, m.`Nicname` ModeratorName, `Players`.`Avatar` PlayerAvatar,`Players`.`Nicname` PlayerName,`Admins`.`Login` UserName
                FROM `PlayerReviews`
                LEFT JOIN `Players` ON `Players`.`Id` = `PlayerReviews`.`PlayerId`
                LEFT JOIN `Players` m ON m.`Id` = `PlayerReviews`.`ModeratorId`
                LEFT JOIN `Admins` ON `Admins`.`Id` = `PlayerReviews`.`AdminId`
                WHERE `PlayerReviews`.Id = :id OR `PlayerReviews`.ParentId = :id
                ORDER BY `Id` ";

            try {
                $sth = DB::Connect()->prepare($sql);
                $sth->execute(array(':id' => $id));
            } catch (PDOExeption $e) {
                throw new ModelException("Unable to proccess storage query", 500);
            }


        $reviews = array();
        foreach ($sth->fetchAll() as $reviewData) {
            $reviewData['Date']=date('d.m.Y H:i:s', $reviewData['Date']);
            $reviews[] = $reviewData;
        }

        return $reviews;
    }

    public function getList($limit = null, $offset = null, $args = array())
    {

        $where = array();

        foreach($args as $key => $value)
            if(isset($value))
                $where[] = "`$key`" . ($value === 'isnull' ? " IS NULL" : ($value === 'notzero' ? " != 0" : " = " . (is_numeric($value)?$value:"'".$value."'")));

        $sql = "SELECT Id
                FROM `PlayerReviews`";

        if(!empty($where))
            $sql.=' WHERE '.implode(' AND ', $where);

        $sql.=" ORDER BY `Id` DESC";

        if (!is_null($limit))
            $sql .= " LIMIT " . (int)$limit;

        if (!is_null($offset))
            $sql .= " OFFSET " . (int)$offset;

        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute();
        } catch (PDOExeption $e) {
            throw new ModelException("Unable to proccess storage query", 500);
        }

        if($sth->rowCount()) {

            $ids = array();
            foreach ($sth->fetchAll() as $id)
                $ids[] = $id['Id'];
            $ids = implode(',', $ids);

            $sql = "SELECT `PlayerReviews`.*, m.`Nicname` ModeratorName, `Players`.`Email` PlayerEmail,`Players`.`Avatar` PlayerAvatar,`Players`.`Nicname` PlayerName,`Admins`.`Login` UserName
                FROM `PlayerReviews`
                LEFT JOIN `Players` ON `Players`.`Id` = `PlayerReviews`.`PlayerId`
                LEFT JOIN `Players` m ON m.`Id` = `PlayerReviews`.`ModeratorId`
                LEFT JOIN `Admins` ON `Admins`.`Id` = `PlayerReviews`.`AdminId`
                WHERE `Status` = :status AND ( `PlayerReviews`.Id IN ({$ids}) OR `PlayerReviews`.ParentId IN ({$ids}))" .
                " ORDER BY `Id` ";

            try {
                $sth = DB::Connect()->prepare($sql);
                $sth->execute(array(':status' => $args['Status']));
            } catch (PDOExeption $e) {
                throw new ModelException("Unable to proccess storage query", 500);
            }
        }

        $reviews = array();

        $list = $sth->fetchAll();
        if (count($list)) {
            foreach ($list as $reviewData) {
                $reviewObj = new Review();
                $reviews[$reviewData['ParentId']?:$reviewData['Id']][] = $reviewObj->formatFrom('DB', $reviewData);
            }
        }

        return $reviews;
    }

    public function getCount($args) {

        $sql = "SELECT COUNT(*) FROM `PlayerReviews`";
        $where = array();

        foreach($args as $key => $value)
            if(isset($value))
                $where[] = "`$key`" . ($value === 'isnull' ? " IS NULL" : ($value === 'notzero' ? " != 0" : " = " . (is_numeric($value)?$value:"'".$value."'")));

        if(!empty($where))
            $sql.=' WHERE '.implode(' AND ', $where);

        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute();
        } catch (PDOExeption $e) {
            throw new ModelException("Unable to proccess storage query", 500);
        }

        return $sth->fetchColumn(0);
    }

}