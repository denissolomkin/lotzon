<?php

Application::import(PATH_INTERFACES . 'IProcessor.php');

class SettingsDBProcessor implements IProcessor
{
    public function getList()
    {
        $sql = "SELECT * FROM `Settings`";

        try {
            $settingsData = DB::Connect()->query($sql)->fetchAll();
        } catch (PDOException $e) {
            throw new ModelException("Unable to execute storage query", 500);
        }

        $settings  = array();
        foreach ($settingsData as $data) {
            $setting = new Settings;
            $setting->formatFrom('DB', $data);
            $settings[$setting->getKey()] = $setting ;
        }

        return $settings;
    }

    public function fetch(Entity $setting)
    {

        $sql = "SELECT * FROM `Settings` WHERE Key = :key LIMIT 1";

        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(
                ':key'    => $setting->getKey(),
            ));

        } catch (PDOException $e) {
            throw new ModelException("Unable to execute storage query", 500);
        }

        if (!$sth->rowCount())
            throw new ModelException("Currency not found", 404);
        else
            $setting->formatFrom('DB', $sth->fetch());

        return $setting;
    }

    public function create(Entity $setting)
    {

        $sql = "REPLACE INTO `Settings` (`Key`, `Value`) VALUES(:k, :v)";

        try {

            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(
                ':k'=>$setting->getKey(),
                ':v'=>serialize($setting->getValue())
            ));

        } catch (PDOException $e) {
            throw new ModelException("Unable to execute storage query", 500);
        }

        return $setting;
    }

    public function update(Entity $country)
    {

    }

    public function delete(Entity $country)
    {

    }
}