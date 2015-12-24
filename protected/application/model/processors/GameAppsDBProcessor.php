<?php

Application::import(PATH_INTERFACES . 'IProcessor.php');

class GameAppsDBProcessor implements IProcessor
{

    public function fetch(Entity $app)
    {
        $sql = "SELECT * FROM `GamesTmpApps`
                WHERE Uid = :uid";

        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(
                ':uid' => $app->getUid(),
            ));

        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query", 500);
        }

        if (!$sth->rowCount()) {
            throw new ModelException("App not found", 404);
        } elseif ($sth->rowCount() > 1) {
            throw new ModelException("Found more than one app", 400);
        }

        $data = $sth->fetch();
        $app->formatFrom('DB', $data);

        return $app;

    }

    public function getList($key = null)
    {
        $sql = "SELECT * FROM `GamesTmpApps`";

        if($key){
            $sql .=  ' WHERE `'.(is_numeric($key) ? 'Id' : 'Key' ) . "` = '$key'";
        }

        try {
            $res = DB::Connect()->prepare($sql);
            $res->execute();
        } catch (PDOException $e) {
            echo $e->getMessage();
            throw new ModelException("Error processing storage query", 500);
        }

        $apps = array();
        foreach ($res->fetchAll() as $appData) {
            $app = new GameApp();
            $app->formatFrom('DB', $appData);
            if (!isset($apps[$app->getKey()])) {
                $apps[$app->getKey()] = array();
                $apps[$app->getId()]  = array();
            }
            $apps[$app->getKey()][$app->getUid()] = $app;
            $apps[$app->getId()][$app->getUid()]  = $app;
        }

        if ($key) {
            return isset($apps[$key]) ? $apps[$key] : array();
        } else
            return $apps;
    }

    public function update(Entity $app)
    {

        $sql = "REPLACE INTO `GamesTmpApps`
                (`Uid`, `Id`, `Key`, `Mode`, `AppData`, `IsRun`,`IsOver`,`IsSaved`, `Players`, `RequiredPlayers`, `Ping`)
                VALUES
                (:uid, :id, :key, :mode, :data, :run, :over, :saved, :players, :nplayers, :ping)";

        try {
            DB::Connect()->prepare($sql)->execute(array(
                ':uid'      => $app->getUid(),
                ':id'       => $app->getId(),
                ':key'      => $app->getKey(),
                ':mode'     => $app->getMode(),
                ':data'     => @serialize($app->getApp()),
                ':run'      => $app->isRun(),
                ':over'     => $app->isOver(),
                ':saved'    => $app->isSaved(),
                ':players'  => @serialize($app->getPlayers()),
                ':nplayers' => $app->getRequiredPlayers(),
                ':ping'     => $app->getPing()
            ));
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query" . $e->getMessage(), 500);
        }

        return $app;

    }


    public function create(Entity $app)
    {

    }

    public function delete(Entity $app)
    {

        $sql = "DELETE FROM `GamesTmpApps` WHERE `Uid` = :uid;";

        try {

            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(
                ':uid' => $app->getUid(),
            ));

        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query: " . $e->getMessage(), 500);
        }

        return true;
    }

}
