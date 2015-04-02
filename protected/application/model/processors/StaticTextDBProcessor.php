<?php

Application::import(PATH_INTERFACES . 'IProcessor.php');

class StaticTextDBProcessor implements IProcessor
{
    public function create(Entity $text)
    {
        $sql = "REPLACE INTO `StaticTexts` (`Id`, `Key`, `Category`, `Text`) VALUES (:id, :key, :cat, :text)";

        try {
            DB::Connect()->prepare($sql)->execute(array(
                ':id'   => $text->getId(),
                ':key'   => $text->getKey(),
                ':cat'  => $text->getCategory(),
                ':text' => serialize($text->getText()),
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
        $sql = "DELETE FROM `StaticTexts` WHERE `Id` = :id";

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

        $sql = "SELECT * FROM `StaticTexts`";
        try {
            $sth = DB::Connect()->query($sql);
        } catch (PDOExeption $e) {
            throw new ModelException("Unable to proccess storage query", 500);   
        }

        $texts = array();

        $list = $sth->fetchAll();
        if (count($list)) {
            foreach ($list as $textData) {
                $text = new StaticText();
                $text->formatFrom('DB', $textData);

                if (!isset($texts[$text->getCategory()]))
                    $texts[$text->getCategory()] = array();

                $texts[$text->getCategory()][$text->getKey()] = $text;
                $texts[$text->getKey()] = $text;

            }

        }

        return $texts;
    }

}