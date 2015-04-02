<?php

Application::import(PATH_INTERFACES . 'IProcessor.php');

class CurrencyDBProcessor implements IProcessor
{
    public function getList()
    {
        $sql = "SELECT * FROM `Currency` ORDER BY Id DESC";

        try {
            $currencyData = DB::Connect()->query($sql)->fetchAll();
        } catch (PDOException $e) {
            throw new ModelException("Unable to execute storage query", 500);
        }

        $currency = array();
        foreach ($currencyData as $data) {
            $cur = new Currency();
            $cur->formatFrom('DB', $data);
            $currency[$cur->getId()] = $cur;
        }

        return $currency;
    }

    public function fetch(Entity $currency)
    {

        $sql = "SELECT * FROM `Currency` WHERE Id = :id LIMIT 1";

        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(
                ':id'    => $currency->getId(),
            ));

        } catch (PDOException $e) {
            throw new ModelException("Unable to execute storage query", 500);
        }

        if (!$sth->rowCount())
            throw new ModelException("Currency not found", 404);
        else
            $currency->formatFrom('DB', $sth->fetch());

        return $currency;
    }

    public function create(Entity $currency)
    {
        $sql = "REPLACE INTO `Currency` (`Id`, `Code`, `Title`, `Rate`, `Coefficient`) VALUES (:id, :code, :title, :rate, :coef)";

        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(
                ':id'    => $currency->getId(),
                ':code'    => $currency->getCode(),
                ':title'     => serialize($currency->getTitle()),
                ':rate'    => $currency->getRate(),
                ':coef'    => $currency->getCoefficient(),
            ));
        } catch (PDOException $e) {
            throw new ModelException("Unable to execute storage query", 500);
        }

        if(!$currency->getId())
            $currency->setId(DB::Connect()->lastInsertId());
        return $currency;
    }

    public function update(Entity $country)
    {

    }

    public function delete(Entity $country)
    {

    }
}