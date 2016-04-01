<?php

Application::import(PATH_INTERFACES . 'IProcessor.php');

class MaillistDBProcessor implements IProcessor
{
    const TaskStatisticPlayerGamesBars = 7;

    public function getTaskStatisticPlayerGames($taskId)
    {
        $ret = array();
        $ret['bars_count']   = MaillistDBProcessor::TaskStatisticPlayerGamesBars;
        $ret['bars']         = array_fill(0,MaillistDBProcessor::TaskStatisticPlayerGamesBars,0);
        $ret['bars']['over'] = 0;

        $sql = "SELECT COUNT(*) as count FROM `MaillistHistory` WHERE `TaskId` = :taskId";
        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(
                ':taskId'   => $taskId,
            ));
        } catch (PDOExeption $e) {
            throw new ModelException("Unable to proccess storage query", 500);
        }
        $ret['count']   = $sth->fetch()['count'];
        $ret['bars'][0] = $ret['count'];

        $sql = "SELECT
                    COUNT(*) AS count, COUNT AS games
                    FROM
                    (
                        SELECT COUNT(*)AS COUNT, PlayerId
                        FROM
                        (
                            SELECT DISTINCT
                                la.`LotteryId`, la.`PlayerId`
                            FROM
                                `LotteryTicketsArchive` AS la
                            JOIN
                                `MaillistHistory` AS mh
                            ON
                                mh.`PlayerId` = la.`PlayerId`
                            WHERE
                                mh.`TaskId`=:taskId
                                AND
                                la.`DateCreated`>UNIX_TIMESTAMP(mh.`Date`)
                        ) AS playerGame
                        GROUP BY PlayerId
                    ) AS playerCound
                    GROUP BY COUNT";

        $sth = DB::Connect()->prepare($sql);
        $sth->execute(array(
            ':taskId'   => $taskId,
        ));
        $gamesData = $sth->fetchAll();
        foreach ($gamesData as $data) {
            $ret['bars'][0] -= $data['count'];
            if ($data['games'] > MaillistDBProcessor::TaskStatisticPlayerGamesBars) {
                $ret['bars']['over'] += $data['count'];
            } else {
                $ret['bars'][$data['games']] = $data['count'];
            }
        }

        return $ret;
    }

    public function saveHistory($message, $status = 'ok')
    {
        $sql = "INSERT INTO `MaillistHistory` (`TaskId`, `Date`, `PlayerId`, `Email`, `Header`, `Body`, `Status`) VALUES (:taskId, NOW(), :playerId, :email, :header, :body, :status)";
        try {
            $sth = DB::Connect()->prepare($sql)->execute(array(
                ':taskId'   => $message['taskId'],
                ':playerId' => $message['playerId'],
                ':email'    => $message['email'],
                ':header'   => $message['header'],
                ':body'     => gzencode($message['body']),
                ':status'   => $status,
            ));
        } catch (PDOExeption $e) {
            throw new ModelException("Unable to proccess storage query", 500);
        }
        return true;
    }

    public function getEmails(Entity $task = null)
    {
        if ($task === null) {
            return array();
        }
        $tables = array();
        $where  = array();
        $values = array();
        $union  = array();

        $filters = isset($task->getSettings()['filters']) ? $task->getSettings()['filters'] : array();
        $message          = MaillistModel::instance()->getMessage($task->getMessageId());
        $message_settings = $message->getSettings();
        $message_values   = $message->getValues();

        if ($message_settings['defaultLanguage'] == "") {
            $filters[] = array(
                'filter' => 'Language',
                'equal'  => 'IN',
                'value'  => array_keys($message_values)
            );
        } else {
            if (!isset($message_values[$message_settings['defaultLanguage']])) {
                return array();
            }
        }
        foreach ($filters as $filter) {
            if ($filter['value']=='') {
                continue;
            }
            if (($filter['equal']=='IN') or ($filter['equal']=='!IN')) {
                $val = $filter['value'];
            } elseif ($filter['equal']=='LIKE') {
                $val = array(0 => '%'.$filter['value'].'%');
            } else {
                $val = array(0 => $filter['value']);
            }
            $ids = ':'.implode(',:', array_keys(array_fill(count($values),count($val), '?')));
            switch ($filter['filter']) {
                case 'Language':
                    $where[] = 'p.Lang '.$filter['equal'].' ('.$ids.')';
                    break;
                case 'DateRegistered':
                    $tables[] = 'PlayerDates as pd ON pd.PlayerId = p.Id';
                    $where[] = 'FROM_UNIXTIME(pd.Registration) '.$filter['equal'].' ('.$ids.')';
                    break;
                case 'OnlineTime':
                    $tables[] = 'PlayerDates as pd ON pd.PlayerId = p.Id';
                    $where[]  = 'FROM_UNIXTIME(pd.Ping) '.$filter['equal'].' ('.$ids.')';
                    break;
                case 'Country':
                    $where[] = 'p.Country '.$filter['equal'].' ('.$ids.')';
                    break;
                case 'GamesPlayed':
                    $where[] = 'p.GamesPlayed '.$filter['equal'].' ('.$ids.')';
                    break;
                case 'Browser':
                    $tables[] = 'PlayerLogins as pl ON pl.PlayerId = p.Id';
                    $where[]  = 'pl.Agent '.$filter['equal'].' ('.$ids.')';
                    break;
                case 'Email':
                    $where[] = 'p.Email '.$filter['equal'].' ('.$ids.')';
                    foreach ($val as $key => $value) {
                        $union[] = '(SELECT 0 as Id, :'.(count($values)+$key).' as Email, :language as Lang)';
                    }
                    break;
                case 'Valid':
                    $where[] = 'p.Valid '.$filter['equal'].' ('.$ids.')';
                    break;
                case 'Id':
                    $where[] = 'p.Id '.$filter['equal'].' ('.$ids.')';
                    break;
            }
            $values = array_merge($values, $val);
        }
        if ($union != array() and (count($filters)==1)) {
            $sql = implode(' UNION ',$union);
        } else {
            $sql     = 'SELECT DISTINCT p.Id, p.Email, p.Lang FROM `Players` as p ';
            $where[] = '(p.NewsSubscribe = 1)';
            $where[] = '(p.Ban = 0)';
            if (count($tables)>0) {
                $sql .= ' JOIN '.implode('JOIN ',array_unique($tables));
            }
            if (count($where)>0) {
                $sql .= ' WHERE '.implode(' AND ',$where);
            }
        }
        $sth = DB::Connect()->prepare($sql);
        foreach ($values as $key=>$value) {
            $sth->bindValue(':'.$key, $value);
        }
        if ($union != array() and (count($filters)==1)) {
            $sth->bindValue(':language', $message_settings['defaultLanguage']);
        }
        $sth->execute();
        $emailsData = $sth->fetchAll();
        $emails = array();
        foreach ($emailsData as $data) {
            $emails[] = $data;
        }
        return $emails;
    }

    public function getTemplateList()
    {
        $sql = "SELECT * FROM `MaillistTemplates`";
        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute();
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query", 500);
        }

        $templatesData = $sth->fetchAll();
        $templates = array();
        foreach ($templatesData as $data) {
            $template = new MaillistTemplate();
            $template->formatFrom('DB', $data);
            $templates[$template->getId()] = $template;
        }

        return $templates;
    }

    /**
     * get list of tasks with some status
     * @param $status
     */
    public function getTaskList($status = false)
    {
        $sql = "SELECT * FROM `MaillistTasks`";
        switch ($status) {
            case 'archived':
                $sql .= ' WHERE `status`="archived" ';
                break;
            case 'waiting':
                $sql .= ' WHERE `status`="waiting" ';
                break;
            case false:
                break;
            default:
                $sql .= ' WHERE `status`<>"archived" ';
        }
        $sql .= " ORDER BY Id";
        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute();
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query", 500);
        }

        $tasksData = $sth->fetchAll();
        $tasks = array();
        foreach ($tasksData as $data) {
            $task = new MaillistTask();
            $task->formatFrom('DB', $data);
            $tasks[$task->getId()] = $task;
        }

        return $tasks;
    }

    public function getMessageList()
    {
        $sql = "SELECT * FROM `MaillistMessages`";
        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute();
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query", 500);
        }

        $messagesData = $sth->fetchAll();
        $messages = array();
        foreach ($messagesData as $data) {
            $message = new MaillistMessage();
            $message->formatFrom('DB', $data);
            $messages[$message->getId()] = $message;
        }

        return $messages;
    }

    public function createMessage(Entity $message)
    {
        $sql = "INSERT INTO `MaillistMessages` (`Id`, `Description`, `TemplateId`, `Values`, `Settings`) VALUES (:id, :description, :templateId, :values, :settings)";
        try {
            $sth = DB::Connect()->prepare($sql)->execute(array(
                ':id'           => $message->getId(),
                ':description'  => $message->getDescription(),
                ':templateId'   => $message->getTemplateId(),
                ':values'       => serialize($message->getValues()),
                ':settings'     => serialize($message->getSettings()),
            ));
        } catch (PDOExeption $e) {
            throw new ModelException("Unable to proccess storage query", 500);
        }
        return $message;
    }

    public function createTask(Entity $task)
    {
        $sql = "INSERT INTO `MaillistTasks` (`Id`, `Description`, `MessageId`, `Schedule`, `Settings`, `Enable`, `Status`, `LastStart`) VALUES (:id, :description, :messageId, :schedule, :settings, :enable, :status, :lastStart)";

        try {
            $sth = DB::Connect()->prepare($sql)->execute(array(
                ':id'          => $task->getId(),
                ':description' => $task->getDescription(),
                ':messageId'   => $task->getMessageId(),
                ':schedule'    => $task->getSchedule(),
                ':settings'    => serialize($task->getSettings()),
                ':enable'      => $task->getEnable(),
                ':status'      => $task->getStatus(),
                ':lastStart'   => $task->getLastStart(),
            ));
        } catch (PDOExeption $e) {
            throw new ModelException("Unable to proccess storage query", 500);
        }
        return $task;
    }

    public function updateTask(Entity $task)
    {
        $sql = "UPDATE `MaillistTasks` SET `Description` = :description, `MessageId` = :messageId, `Schedule` = :schedule, `Settings` = :settings, `Enable` = :enable, `Status` = :status, `LastStart` = :lastStart WHERE `Id` = :id";
        try {
            $sth = DB::Connect()->prepare($sql)->execute(array(
                ':id'          => $task->getId(),
                ':description' => $task->getDescription(),
                ':messageId'   => $task->getMessageId(),
                ':schedule'    => $task->getSchedule(),
                ':settings'    => serialize($task->getSettings()),
                ':enable'      => $task->getEnable(),
                ':status'      => $task->getStatus(),
                ':lastStart'   => $task->getLastStart(),
            ));
        } catch (PDOExeption $e) {
            throw new ModelException("Unable to proccess storage query", 500);
        }
        return $task;
    }

    public function updateMessage(Entity $message)
    {
        $sql = "UPDATE `MaillistMessages` SET `Description` = :description, `TemplateId` = :templateId, `Values` = :values, `Settings` = :settings WHERE `Id` = :id";
        try {
            $sth = DB::Connect()->prepare($sql)->execute(array(
                ':id'           => $message->getId(),
                ':description'  => $message->getDescription(),
                ':templateId'   => $message->getTemplateId(),
                ':values'       => serialize($message->getValues()),
                ':settings'     => serialize($message->getSettings()),
            ));
        } catch (PDOExeption $e) {
            throw new ModelException("Unable to proccess storage query", 500);
        }
        return $message;
    }

    public function deleteMessage($message)
    {
        $sql = "DELETE FROM `MaillistMessages` WHERE `Id` = :id";

        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(
                ':id' => $message->getId(),
            ));

        } catch (PDOExeption $e) {
            throw new ModelException("Unable to process delete query", 500);
        }

        return true;
    }

    public function deleteTask($task)
    {
        $sql = "DELETE FROM `MaillistTasks` WHERE `Id` = :id";

        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(
                ':id' => $task->getId(),
            ));

        } catch (PDOExeption $e) {
            throw new ModelException("Unable to process delete query", 500);
        }

        return true;
    }

    public function archiveTask($task)
    {
        $sql = "UPDATE `MaillistTasks` SET `status` = :status WHERE `Id` = :id";

        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(
                ':id'     => $task->getId(),
                ':status' => 'archived',
            ));

        } catch (PDOExeption $e) {
            throw new ModelException("Unable to process delete query", 500);
        }

        return true;
    }

    public function delete(Entity $lottery)
    {
        return $lottery;
    }

    public function fetch(Entity $lottery)
    {
        return $lottery;
    }

    public function create(Entity $lottery)
    {
        return $lottery;
    }

    public function update(Entity $lottery)
    {
        return $lottery;
    }
}