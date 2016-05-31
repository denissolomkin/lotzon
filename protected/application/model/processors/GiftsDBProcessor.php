<?php

Application::import(PATH_INTERFACES . 'IProcessor.php');

class GiftsDBProcessor implements IProcessor
{
    public function create(Entity $gift)
    {
        $sql = "INSERT INTO `Gifts` (`Id`, `PlayerId`, `GiftPlayerId`, `ObjectType`, `ObjectId`, `Currency`, `Sum`, `Equivalent`, `Date`, `ExpiryDate`, `Used`) VALUES (:id, :playerId, :giftPlayerId, :objectType, :objectId, :currency, :sum, :equivalent, :date, :expiryDate, :used)";

        try {
            $sth = DB::Connect()->prepare($sql)->execute(array(
                ':id'           => $gift->getId(),
                ':playerId'     => $gift->getPlayerId(),
                ':giftPlayerId' => $gift->getGiftPlayerId(),
                ':objectType'   => $gift->getObjectType(),
                ':objectId'     => $gift->getObjectId(),
                ':currency'     => $gift->getCurrency(),
                ':sum'          => $gift->getSum(),
                ':equivalent'   => $gift->getEquivalent(),
                ':date'         => time(),
                ':expiryDate'   => $gift->getExpiryDate(),
                ':used'         => $gift->getUsed(),
            ));
        } catch (PDOExeption $e) {
            throw new ModelException("Unable to proccess storage query", 500);
        }

        return $gift;
    }

    public function update(Entity $gift)
    {
        $sql = "UPDATE `Gifts` SET `Used` = :used WHERE `Id` = :id";

        try {
            $sth = DB::Connect()->prepare($sql)->execute(array(
                ':id'           => $gift->getId(),
                ':used'         => $gift->getUsed(),
            ));
        } catch (PDOexception $e) {
            throw new ModelException("Unable to proccess storage query", 500);
        }

        return $gift;
    }

    public function delete(Entity $gift)
    {
        $sql  = "DELETE FROM `Gifts` WHERE `Id` = :id";

        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(
                ':id' => $gift->getId()
            ));

        } catch (PDOExeption $e) {
            throw new ModelException("Unable to process delete query", 500);
        }

        return true;
    }

    public function fetch(Entity $gift)
    {
        $sql = "SELECT *
                FROM `Gifts`
                WHERE
                    `Id` = :id
                LIMIT 1";

        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(
                ':id'    => $gift->getId(),
            ));
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query", 500);
        }

        if (!$sth->rowCount()) {
            throw new ModelException("Gift not found", 404);
        }

        $data = $sth->fetch();
        $gift->formatFrom('DB', $data);

        return $gift;
    }

    public function getList($playerId, $objectType = null, $objectId = null, $used = false, $expiryDate = null)
    {
        $sql = "SELECT
                    *
                FROM `Gifts`
                WHERE
                    PlayerId = :playerId
                AND
                    Used = :used"
            . (($objectType === null) ? "" : " AND (`ObjectType` = :objectType)")
            . (($objectId === null)   ? "" : " AND (`ObjectId`   = :objectId)")
            . (($expiryDate === null)   ? "" : " AND (`ExpiryDate`   > :expiryDate)")
            . "
                ORDER BY `ExpiryDate` ";
        try {
            $sth = DB::Connect()->prepare($sql);
            $sql_arr = array(
                ':playerId' => $playerId,
                ':used'     => $used,
            );
            if ($objectType !== null) {
                $sql_arr[':objectType'] = $objectType;
            }
            if ($objectId !== null) {
                $sql_arr[':objectId'] = $objectId;
            }
            if ($expiryDate !== null) {
                $sql_arr[':expiryDate'] = $expiryDate;
            }
            $sth->execute($sql_arr);
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query " . $e, 1);
        }

        $gifts = array();
        foreach ($sth->fetchAll() as $giftData) {
            $gift                  = new \Gift;
            $gifts[$giftData['Id']] = $gift->formatFrom('DB', $giftData);
        }

        return $gifts;
    }

}
