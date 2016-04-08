<?php

Application::import(PATH_INTERFACES . 'IProcessor.php');

class DebugDBProcessor implements IProcessor
{

    public function create (Entity $entity)
    {}

    public function fetch(Entity $entity)
    {}

    public function delete(Entity $entity)
    {}

    public function update(Entity $entity)
    {}

    public function addLog(Entity $player, $data)
    {
        $sql = "INSERT INTO `Debug` (`PlayerId`, `Date`, `Agent`, `Ip`, `Log`, `Url`, `Line`)
                VALUES (:id, :date, :agent, :ip, :log, :url, :line)";

        try {
            DB::Connect()
                ->prepare($sql)
                ->execute(
                    array(
                        ':id'      => $player->getId(),
                        ':date'    => time(),
                        ':agent'   => $player->getAgent(),
                        ':ip'      => $player->getLastIp(),
                        ':log'     => $data['log'],
                        ':url'     => $data['url'],
                        ':line'     => $data['line'],
                    ));
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query", 500);
        }

        return true;
    }

    public function getList($mode = null)
    {
        switch ($mode) {
            case 1:
            default:
                $sql = "SELECT * FROM `Debug`
                ORDER BY Id DESC
                LIMIT 100";
                break;
            case 2:
                $sql = "SELECT Log, Count(*) Count FROM `Debug`
                GROUP BY Log
                ORDER BY Count DESC";
                break;
        }

        try {
            $sth = DB::Connect()
                ->prepare($sql);
            $sth->execute();
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query", 500);
        }

        $logs=array();
        foreach ($sth->fetchAll() as $data) {
            if(isset($data['Date']))
                $data['Date']=date('d.m.Y H:i:s', $data['Date']);
            $logs[] = $data;
        }
        return $logs;
    }

}
