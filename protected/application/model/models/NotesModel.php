<?php

Application::import(PATH_APPLICATION . 'model/Model.php');
Application::import(PATH_APPLICATION . 'model/entities/Note.php');
Application::import(PATH_APPLICATION . 'model/processors/NotesDBProcessor.php');
Application::import(PATH_APPLICATION . 'model/processors/NotesCacheProcessor.php');


class NotesModel extends Model
{
    public function init()
    {
        $this->setProcessor(Config::instance()->cacheEnabled ? new NotesCacheProcessor() : new NotesDBProcessor());
    }

    public static function myClassName()
    {
        return __CLASS__;
    }

    public function getList($playerId = null, $date = null, $limit = null, $offset = null) {
        return $this->getProcessor()->getList($playerId, $date, $limit, $offset);
    }

}