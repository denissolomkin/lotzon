<?php

Application::import(PATH_INTERFACES . 'IProcessor.php');

class CountriesDBProcessor implements IProcessor
{
    public function getList()
    {
        $sql = "SELECT * FROM `MUICountries` ORDER BY Id";

        try {
            $countriesData = DB::Connect()->query($sql)->fetchAll();
        } catch (PDOException $e) {
            throw new ModelException("Unable to execute storage query", 500);
        }

        $countries = array();
        foreach ($countriesData as $data) {
            $country = new Country();
            $country->formatFrom('DB', $data);

            $countries[$country->getCode()] = $country;
        }

        return $countries;
    }

    public function getCountries()
    {
        $sql = "SELECT Code FROM `MUICountries` ORDER BY Id";

        try {
            $countriesData = DB::Connect()->query($sql)->fetchAll();
        } catch (PDOException $e) {
            throw new ModelException("Unable to execute storage query", 500);
        }

        $countries = array();
        foreach ($countriesData as $data) {
            $countries[] = $data['Code'];
        }

        return $countries;
    }

    public function getLangs()
    {
        $sql = "SELECT DISTINCT(Lang) FROM `MUICountries`";

        try {
            $countriesData = DB::Connect()->query($sql)->fetchAll();
        } catch (PDOException $e) {
            throw new ModelException("Unable to execute storage query", 500);
        }

        $langs = array();
        foreach ($countriesData as $data) {
            $langs[] = $data['Lang'];
        }

        return $langs;
    }

    public function getAvailabledCountries()
    {

        $sql = "SELECT Country, COUNT(*) Count
                FROM `Players`
                GROUP BY Country ORDER BY Count DESC";

        try {
            $countriesData = DB::Connect()->query($sql)->fetchAll();
        } catch (PDOException $e) {
            throw new ModelException("Unable to execute storage query", 500);
        }

        return $countriesData;
    }

    public function fetch(Entity $country)
    {

        $sql = "SELECT * FROM `MUICountries` WHERE Code = :code LIMIT 1";

        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(
                ':code'    => $country->getCode(),
            ));

        } catch (PDOException $e) {
            throw new ModelException("Unable to execute storage query", 500);
        }

        $country->formatFrom('DB', $sth->fetch());
        return $country;
    }

    public function create(Entity $country)
    {
        $sql = "REPLACE INTO `MUICountries` (`Id`, `Code`, `Lang`, `Currency`) VALUES (:id, :code, :lang, :cur)";

        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(
                ':id'    => $country->getId(),
                ':code'    => $country->getCode(),
                ':lang'    => $country->getLang(),
                ':cur'     => $country->getCurrency(),
            ));
        } catch (PDOException $e) {
            throw new ModelException("Unable to execute storage query", 500);
        }

        if(!$country->getId())
            $country->setId(DB::Connect()->lastInsertId());
        return $country;
    }

    public function update(Entity $country)
    {

    }

    public function delete(Entity $country)
    {

    }
}