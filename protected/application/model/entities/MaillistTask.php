<?php
Application::import(PATH_APPLICATION . 'model/Entity.php');

class MaillistTask extends Entity
{
    static $FILTERS = array(
        'DateRegistered' => array (
            'description' => 'Дата регистрации',
            'type'        => 'date',
            'default'     => '2015-01-01',
        ),
        'OnlineTime' => array (
            'description' => 'Последняя активность',
            'type'        => 'date',
            'default'     => '2015-01-01',
        ),
        'Language' => array (
            'description' => 'Язык',
            'type'        => 'text',
            'default'     => 'RU',
        ),
        'Country' => array (
            'description' => 'Страна',
            'type'        => 'text',
            'default'     => 'RU',
        ),
        'GamesPlayed' => array (
            'description' => 'Игр сыграно',
            'type'        => 'text',
            'default'     => '100',
        ),
        'Browser' => array(
            'description' => 'Браузер',
            'type'        => 'text',
            'default'     => 'Android',
        ),
        'Email' => array(
            'description' => 'Email',
            'type'        => 'text',
            'default'     => 'test@lotzon.com',
        ),
    );

    static $EVENTS = array(
        'Inactive' => array(
            'description' => 'Отсутствие активности за последние n дней',
            'parameter'   => true,
        ),
        'NewMail'   => array(
            'description' => 'Новый email',
            'parameter'   => false,
        ),
        'WinMoney' => array(
            'description' => 'Выиграно более чем n денег в последнем розыгрыше',
            'parameter'   => true,
        ),
    );

    private $_id          = 0;
    private $_description = '';
    private $_messageId   = 0;
    private $_schedule    = false;
    private $_settings    = array();
    private $_enable      = false;
    private $_status      = '';

    public function init()
    {
        $this->setModelClass('MaillistModel');
    }

    public function setId($id)
    {
        $this->_id = (int)$id;
        return $this;
    }

    public function getId()
    {
        return (int)$this->_id;
    }

    public function setDescription($description)
    {
        $this->_description = $description;
        return $this;
    }

    public function getDescription()
    {
        return $this->_description;
    }

    public function setMessageId($messageId)
    {
        $this->_messageId = (int)$messageId;
        return $this;
    }

    public function getMessageId()
    {
        return (int)$this->_messageId;
    }

    public function setSchedule($schedule)
    {
        $this->_schedule = $schedule;
        return $this;
    }

    public function getSchedule()
    {
        return $this->_schedule;
    }

    public function setSettings($settings)
    {
        $this->_settings = $settings;
        return $this;
    }

    public function getSettings()
    {
        return $this->_settings;
    }

    public function setEnable($enable)
    {
        $this->_enable = $enable;
        return $this;
    }

    public function getEnable()
    {
        return $this->_enable;
    }


    public function setStatus($status)
    {
        $this->_status = $status;
        return $this;
    }

    public function getStatus()
    {
        return $this->_status;
    }

    public function formatFrom($from, $data)
    {
        if ($from == 'DB') {
            $this->setId($data['Id'])
                 ->setDescription($data['Description'])
                 ->setMessageId($data['MessageId'])
                 ->setSchedule($data['Schedule'])
                 ->setSettings(unserialize($data['Settings']))
                 ->setEnable($data['Enable'])
                 ->setStatus($data['Status']);
        }
        return $this;
    }

    public function getArray()
    {
        return array(
            'Id'          => $this->getId(),
            'Description' => $this->getDescription(),
            'MessageId'   => $this->getMessageId(),
            'Schedule'    => $this->getSchedule(),
            'Settings'    => $this->getSettings(),
            'Enable'      => $this->getEnable(),
            'Status'      => $this->getStatus()
        );
    }

    public function getEmails()
    {
        $model = $this->getModelClass();
        return $model::instance()->getEmails($this);
    }

    public function send()
    {
        $emails  = $this->getEmails();

        $message = MaillistModel::instance()->getMessage($this->getMessageId());

        foreach ($emails as $email) {
            $playerId    = $email['Id'];
            $emailLang = $email['Lang'];
            $address   = $email['Email'];
            $render    = $message->render($playerId, $emailLang);
            $html      = $render['html'];
            $header    = $render['header'];
            $from      = $message->getSettings()['from'];

            var_dump($playerId);
            var_dump($emailLang);
            var_dump($address);
            var_dump($from);
            var_dump($header);
            var_dump($html);

            $model = $this->getModelClass();
            $model::instance()->saveHistory(
                array(
                    'playerId' => $playerId,
                    'taskId'   => $this->getId(),
                    'email'    => $address,
                    'header'   => $header,
                    'body'     => $html,
                )
            );
        }
    }

    public function delete()
    {
        $model = $this->getModelClass();
        $model::instance()->deleteTask($this);
    }

    public function archive()
    {
        $model = $this->getModelClass();
        $model::instance()->archiveTask($this);
    }

    public function create()
    {
        $model = $this->getModelClass();
        $model::instance()->createTask($this);
    }

    public function update()
    {
        $model = $this->getModelClass();
        $model::instance()->updateTask($this);
    }
}