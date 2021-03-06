<?php
Application::import(PATH_INTERFACES . 'IProcessor.php');

class TransactionsDBProcessor implements IProcessor
{
    public function create(Entity $transaction)
    {   
        $transaction->setDate(time());
        $sql = "INSERT INTO `Transactions` (`PlayerId`, `Currency`, `Sum`, `Balance`, `ObjectType`, `ObjectId`, `Description`, `Date`) VALUES (:plid, :curr, :sum,  :bal, :otype, :oid, :desc, :date)";

        try {
            DB::Connect()->prepare($sql)->execute(array(
                ':plid'   => $transaction->getPlayerId(),
                ':curr'   => $transaction->getCurrency(),
                ':sum'    => $transaction->getSum(),
                ':bal'    => $transaction->getBalance(),
                ':otype'  => $transaction->getObjectType(),
                ':oid'    => $transaction->getObjectId(),
                ':desc'   => $transaction->getDescription(),
                ':date'   => $transaction->getDate(),
            ));
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query", 500);
        }

        $transaction->setId(DB::Connect()->lastInsertId());

        return $transaction;
    }

    public function update(Entity $transaction)
    {
        return $transaction;
    }

    public function fetch(Entity $transaction)
    {
        $sql = "SELECT * FROM `Transactions` WHERE `Id` = :trid";
        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(
                ':trid' => $transaction->getId(),
            ));
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query", 500);
        }

        if (!$sth->rowCount()) {
            throw new ModelException("NOT_FOUND", 404);
        }

        $transactionData = $sth->fetch();

        $transaction->setId($transactionData['Id'])
                    ->setPlayerId($transactionData['PlayerId'])
                    ->setSum($transactionData['Sum'])
                    ->setBalance($transactionData['Balance'])
                    ->setDescription($transactionData['Description'])
                    ->setCurrency($transactionData['Currency'])
                    ->setDate($transactionData['Date']);

        return $transaction;
    }

    public function delete(Entity $transaction)
    {
        $sql = "DELETE FROM `Transactions` WHERE `Id` = :trid";
        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(
                ':trid' => $transaction->getId(),
            ));
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query", 500);
        }

        return true;
    }

    public function playerPointsHistory($playerId, $limit = 0, $offset = 0)
    {
        return $this->playerHistory($playerId, LotterySettings::CURRENCY_POINT, $limit, $offset);
    }


    public function playerMoneyHistory($playerId, $limit = 0, $offset = 0)
    {
        return $this->playerHistory($playerId, LotterySettings::CURRENCY_MONEY, $limit, $offset);
    }

    public function playerHistory($playerId, $currency, $limit=null, $offset=null)
    {   
        $sql = "SELECT * FROM `Transactions` WHERE `PlayerId` = :plid AND `Currency` = :curr ORDER BY `Date` DESC";
        if (!is_null($limit)) {
            $sql .= " LIMIT " . (int) $limit;
        }
        if (!is_null($offset)) {
            $sql .= " OFFSET " . (int) $offset;
        }
        //echo $sql;
        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(
                ':plid' => $playerId,
                ':curr' => $currency,
            ));
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query", 500);
        }

        $transactions = array();
        if ($sth->rowCount()) {
            $data = $sth->fetchAll();

            foreach ($data as $transactionData) {
                $transaction = new Transaction();
                $transaction->setId($transactionData['Id'])
                            ->setPlayerId($transactionData['PlayerId'])
                            ->setSum($transactionData['Sum'])
                            ->setBalance($transactionData['Balance'])
                            ->setDescription($transactionData['Description'])
                            ->setCurrency($transactionData['Currency'])
                            ->setDate($transactionData['Date']);
                $transactions[] = $transaction;
            }
        }

        return $transactions;
    }
}