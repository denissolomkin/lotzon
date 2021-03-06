<?php
require_once(PATH_ROOT . 'vendor/phpmailer/PHPMailerAutoload.php');

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
        'Valid' => array(
            'description' => 'Подтверждён email',
            'type'        => 'text',
            'default'     => '1',
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
    private $_lastStart   = '';

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

    public function setLastStart($lastStart)
    {
        $this->_lastStart = $lastStart;
        return $this;
    }

    public function getLastStart()
    {
        return $this->_lastStart;
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
                 ->setStatus($data['Status'])
                 ->setLastStart($data['LastStart']);
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
            'Status'      => $this->getStatus(),
            'LastStart'   => $this->getLastStart()
        );
    }

    public function getCountEmails()
    {
        return count($this->getEmails());
    }

    public function getEmails()
    {
        $model = $this->getModelClass();
        return $model::instance()->getEmails($this);
    }

    public function isDoStart()
    {
        if ($this->getEnable()==false) {
            return false;
        }
        if (($this->isTimeToStart())and(!($this->isDoneToday()))) {
            return true;
        } else {
            return false;
        }
    }

    public function isDoneToday()
    {
        if ($this->getSchedule()) {
            // if start done today
            if ((strtotime($this->getLastStart()))>(strtotime($this->getSettings()['timeFrom']))) {
                return true;
            }
        } else {
            // if once start
            if (strtotime($this->getLastStart())>strtotime($this->getSettings()['dateFrom'])) {
                return true;
            }
        }
        return false;
    }

    public function isTimeToStart()
    {
        // check fromDate
        if (strtotime($this->getSettings()['dateFrom'])>time()) {
            return false;
        }

        // check from_to Time
        if ((strtotime($this->getSettings()['timeFrom'])>time())or(strtotime($this->getSettings()['timeTo'])<time())) {
            return false;
        }

        if ($this->getSchedule()) {
            switch ($this->getSettings()['period']) {
                case 'day':
                    break;
                case 'week':
                    if (!(is_array($this->getSettings()['parameter']))) {
                        return false;
                    }
                    if (!(in_array(strtolower(date('l')),$this->getSettings()['parameter']))) {
                        return false;
                    }
                    break;
                case 'month':
                    if (!(is_array($this->getSettings()['parameter']))) {
                        return false;
                    }
                    if (!(in_array(strtolower(date('j')),$this->getSettings()['parameter']))) {
                        if (!((in_array('last',$this->getSettings()['parameter']))and(date('j')==date('t')))) {
                            return false;
                        }
                    }
                    break;
                default:
                    return false;
            }
        }
        return true;
    }

    public function send()
    {
        $this->setStatus('in progress');
        $this->setLastStart(date("Y-m-d H:i:s"));
        $this->update();

        echo date("Y-m-d H:i:s").' Sending '.$this->getCountEmails().' emails'.PHP_EOL;

        $emails  = $this->getEmails();

        $message = MaillistModel::instance()->getMessage($this->getMessageId());
        $from    = $message->getSettings()['from'];

        $mailer = new PHPMailer;
        $mailer->isSMTP();
        $mailer->Host       = Config::instance()->mailServers[$from]['host'];
        $mailer->SMTPAuth   = true;
        $mailer->Username   = Config::instance()->mailServers[$from]['user'];
        $mailer->Password   = Config::instance()->mailServers[$from]['pass'];
        $mailer->SMTPSecure = Config::instance()->mailServers[$from]['secure'];
        $mailer->Port       = Config::instance()->mailServers[$from]['port'];
        $mailer->From       = Config::instance()->mailServers[$from]['from'];
        $mailer->FromName   = Config::instance()->mailServers[$from]['fromName'];
        $mailer->isHTML(true);
        $mailer->CharSet    = 'utf-8';

        $utm_variables = array(
            "%taskId%" => $this->getId()
        );

        foreach ($emails as $email) {
            $playerId  = $email['Id'];
            $emailLang = $email['Lang'];
            $address   = $email['Email'];
            $render    = $message->render($playerId, $emailLang, $utm_variables);
            $html      = $render['html'];
            $header    = $render['header'];


            //send email
            $mailer->clearAddresses();
            $mailer->clearCustomHeaders();

            //add unsubscribe method
            if ($playerId>0) {
                $player = new \Player;
                $player->setId($playerId)->fetch();
                $mailer->addCustomHeader("List-Unsubscribe", '<mailto:'.Config::instance()->unsubscribeMail.'?body=unsubscribe.'.$player->getSalt().'>, <http://lotzon.com/unsubscribe/?email='.$address.'&hash='.$player->getSalt().'>');
            }
            $mailer->addCustomHeader("Precedence: bulk");
            $mailer->addCustomHeader("List-id: ".$this->getId());

            $mailer->addAddress($address);
            $mailer->Subject    = $header;
            $mailer->Body       = $html;
            if ($mailer->send()) {
                $status = 'send';
            } else {
                $status = 'error';
            }

            echo date("Y-m-d H:i:s").' ['.$status.'] '.$address.' ['.$this->getId().'] - '.$header.PHP_EOL;

            $model = $this->getModelClass();
            $model::instance()->saveHistory(
                array(
                    'playerId' => $playerId,
                    'taskId'   => $this->getId(),
                    'email'    => $address,
                    'header'   => $header,
                    'body'     => $html,
                ),
                $status
            );
        }

        if ($this->getSchedule()) {
            $this->setStatus('waiting');
        } else {
            $this->setStatus('done');
        }
        $this->update();
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