<?php

Application::import(PATH_APPLICATION . 'model/Model.php');
Application::import(PATH_APPLICATION . 'model/entities/MaillistMessage.php');
Application::import(PATH_APPLICATION . 'model/entities/MaillistTask.php');
Application::import(PATH_APPLICATION . 'model/entities/MaillistTemplates.php');
Application::import(PATH_APPLICATION . 'model/processors/MaillistDBProcessor.php');


class MaillistModel extends Model
{
    public function init()
    {
        $this->setProcessor(new MaillistDBProcessor());
    }

    public static function myClassName()
    {
        return __CLASS__;
    }

    public function getTemplateList()
    {
        return $this->getProcessor()->getTemplateList();
    }

    public function getTemplate($identifier)
    {
        $list = $this->getTemplateList();
        if (isset($list[$identifier])) {
            return $list[$identifier];
        } else {
            return false;
        }
    }

    public function getTask($identifier)
    {
        $list = $this->getTaskList();
        if (isset($list[$identifier])) {
            return $list[$identifier];
        } else {
            return false;
        }
    }

    public function getMessage($identifier)
    {
        $list = $this->getMessageList();
        if (isset($list[$identifier])) {
            return $list[$identifier];
        } else {
            return false;
        }
    }

    public function getTaskList($status = false)
    {
        return $this->getProcessor()->getTaskList($status);
    }

    public function getMessageList()
    {
        return $this->getProcessor()->getMessageList();
    }

    public function deleteMessage($message)
    {
        return $this->getProcessor()->deleteMessage($message);
    }

    public function deleteTask($task)
    {
        return $this->getProcessor()->deleteTask($task);
    }

    public function archiveTask($task)
    {
        return $this->getProcessor()->archiveTask($task);
    }

    public function createMessage($message)
    {
        return $this->getProcessor()->createMessage($message);
    }

    public function updateMessage($message)
    {
        return $this->getProcessor()->updateMessage($message);
    }

    public function createTask($task)
    {
        return $this->getProcessor()->createTask($task);
    }

    public function updateTask($task)
    {
        return $this->getProcessor()->updateTask($task);
    }

    public function getEmails($task)
    {
        return $this->getProcessor()->getEmails($task);
    }

    public function saveHistory($message)
    {
        return $this->getProcessor()->saveHistory($message);
    }

}