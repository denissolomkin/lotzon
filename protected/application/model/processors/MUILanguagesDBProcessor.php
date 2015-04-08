<?php

Application::import(PATH_INTERFACES . 'IProcessor.php');

class LanguagesDBProcessor implements IProcessor
{
    public function getList()
    {
        $sql = "SELECT * FROM `MUILanguages` ORDER BY Id";

        try {
            $languageData = DB::Connect()->query($sql)->fetchAll();
        } catch (PDOException $e) {
            throw new ModelException("Unable to execute storage query", 500);
        }

        $language = array();
        foreach ($languageData as $data) {
            $cur = new language();
            $cur->formatFrom('DB', $data);
            $language[$cur->getId()] = $cur;
        }

        return $language;
    }

    public function fetch(Entity $language)
    {

        $sql = "SELECT * FROM `MUILanguages` WHERE Id = :id LIMIT 1";

        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(
                ':id'    => $language->getId(),
            ));

        } catch (PDOException $e) {
            throw new ModelException("Unable to execute storage query", 500);
        }

        if (!$sth->rowCount())
            throw new ModelException("language not found", 404);
        else
            $language->formatFrom('DB', $sth->fetch());

        return $language;
    }

    public function create(Entity $language)
    {
        $sql = "REPLACE INTO `MUILanguages` (`Id`, `Code`, `Title`) VALUES (:id, :code, :title)";

        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(
                ':id'    => $language->getId(),
                ':code'    => $language->getCode(),
                ':title'     => $language->getTitle(),
            ));
        } catch (PDOException $e) {
            throw new ModelException("Unable to execute storage query", 500);
        }

        if(!$language->getId())
            $language->setId(DB::Connect()->lastInsertId());
        return $language;
    }

    public function update(Entity $country)
    {

    }

    public function delete(Entity $country)
    {

    }
}