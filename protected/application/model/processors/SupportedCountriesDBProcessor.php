<?php

Application::import(PATH_INTERFACES . 'IProcessor.php');

class SupportedCountriesDBProcessor implements IProcessor
{
    public function getEnabledCountriesList()
    {
        $sql = "SELECT * FROM `SupportedCountries` WHERE `Enabled` = 1 ORDER BY `Position`";

        try {
            $countriesData = DB::Connect()->query($sql)->fetchAll();
        } catch (PDOException $e) {
            throw new ModelException("Unable to execute storage query", 500);
        }

        $countries = array();
        foreach ($countriesData as $data) {
            $country = new SupportedCountry();
            $country->formatFrom('DB', $data);

            $countries[$country->getCountryCode()] = $country;
        }

        return $countries;
    }

    public function fetch(Entity $country)
    {

    }

    public function create(Entity $country)
    {
        $sql = "INSERT INTO `SupportedCountries` (`CountryCode`, `Title`, `Enabled`, `Lang`) VALUES (:cc, :title, 1, :lang)";

        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(
                ':cc'    => $country->getCountryCode(),
                ':title' => $country->getTitle(),
                ':lang'  => $country->getLang(),
            ));
        } catch (PDOException $e) {
            throw new ModelException("Unable to execute storage query", 500);
        }

        return $country;
    }

    public function update(Entity $country)
    {

    }

    public function delete(Entity $country)
    {

    }
}