<?php

Application::import(PATH_INTERFACES . 'IProcessor.php');

class StaticSiteTextDBProcessor implements IProcessor
{
    public function create(Entity $text)
    {
        $sql = "REPLACE INTO `SiteStaticTexts` (`Id`, `Lang`, `Text`) VALUES (:id, :lang, :text)";

        try {
            $sth = DB::Connect()->prepare($sql)->execute(array(
                ':id'   => $text->getId(),
                ':lang' => $text->getLang(),
                ':text' => $text->getText(),
            ));
        } catch (PDOExeption $e) {
            throw new ModelException("Unable to proccess storage query", 500);            
        }

        return $text;
    }

    public function update(Entity $text)
    {
    
    }

    public function delete(Entity $text)
    {
        $sql = "DELETE FROM `SiteStaticTexts` WHERE `Id` = :id";

        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(
                ':id' => $text->getId()
            ));

        } catch (PDOExeption $e) {
            throw new ModelException("Unable to process delete query", 500);
        }

        return true;
    }

    public function fetch(Entity $text)
    {


    }

    public function getList()
    {
        $sql = "SELECT * FROM `SiteStaticTexts`";
        try {
            $sth = DB::Connect()->query($sql);
        } catch (PDOExeption $e) {
            throw new ModelException("Unable to proccess storage query", 500);   
        }

        $texts = array();

        $list = $sth->fetchAll();
        if (count($list)) {
            foreach ($list as $textData) {
                $text = new StaticSiteText();
                $texts[] = $text->formatFrom('DB', $textData);   
            }
        }

        return $texts;
    }

}