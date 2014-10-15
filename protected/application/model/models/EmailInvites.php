<?php

Application::import(PATH_APPLICATION . 'model/Model.php');
Application::import(PATH_APPLICATION . 'model/entities/EmailInvite.php');
Application::import(PATH_APPLICATION . 'model/processors/EmailInvitesProcessor.php');

class EmailInvites extends Model
{
    public function init()
    {
        $this->setProcessor(new EmailInvitesProcessor());
    }

    public static function myClassName()
    {
        return __CLASS__;
    }

    public function getInvite($email)
    {
        return $this->getProcessor()->getInvite($email);        
    }
}