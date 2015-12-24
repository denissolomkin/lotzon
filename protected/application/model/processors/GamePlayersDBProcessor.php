<?php

Application::import(PATH_INTERFACES . 'IProcessor.php');

class GamePlayersDBProcessor implements IProcessor
{

    public function fetch(Entity $player)
    {
        $sql = "SELECT * FROM `GamesTmpPlayers`
                WHERE PlayerId = :pid";

        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(
                ':pid' => $player->getId(),
            ));

        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query", 500);
        }

        if (!$sth->rowCount()) {
            throw new ModelException("Player not found", 404);
        } elseif ($sth->rowCount() > 1) {
            throw new ModelException("Found more than one player", 400);
        }

        $playerData = $sth->fetch();
        $player->formatFrom('DB', $playerData);

        return $player;

    }

    public function update(Entity $player)
    {

        $sql = "REPLACE INTO `GamesTmpPlayers` (`PlayerId`, `Lang`, `Country`, `Name`, `Avatar`, `Admin`, `Bot`, `AppId`, `AppUid`, `AppName`, `AppMode`, `Ping`)
                VALUES (:pid, :lang, :cc, :name, :avatar, :admin, :bot, :appid, :appuid, :appname, :appmode, :ping)";

        try {
            DB::Connect()->prepare($sql)->execute(array(
                ':pid'     => $player->getId(),
                ':lang'    => $player->getLang(),
                ':cc'      => $player->getCountry(),
                ':name'    => $player->getName(),
                ':avatar'  => $player->getAvatar(),
                ':admin'   => $player->isAdmin(),
                ':bot'     => $player->isBot(),
                ':appid'   => $player->getAppId(),
                ':appuid'  => $player->getAppUid(),
                ':appname' => $player->getAppName(),
                ':appmode' => $player->getAppMode(),
                ':ping'    => $player->getPing()
            ));
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query" . $e->getMessage(), 500);
        }

        return $player;

    }


    public function create(Entity $player)
    {

    }

    public function delete(Entity $player)
    {

        $sql = "DELETE FROM `GamesTmpPlayers`
                WHERE `PlayerId` = :id";

        try {

            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(
                ':id' => $player->getId(),
            ));

        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query: " . $e->getMessage(), 500);
        }

        return true;
    }

    public function getList()
    {
        $sql = "SELECT * FROM `GamesTmpPlayers`";

        try {
            $res = DB::Connect()->prepare($sql);
            $res->execute();
        } catch (PDOException $e) {
            echo $e->getMessage();
            throw new ModelException("Error processing storage query", 500);
        }

        $players = array();
        foreach ($res->fetchAll() as $playerData) {
            $player = new GamePlayer();
            $player->formatFrom('DB', $playerData);
            $players[] = $player;
        }

        return $players;
    }

    public function getOnline($gameId)
    {
        $sql = "SELECT COUNT(*) FROM `GamesTmpPlayers`
                WHERE AppId = :id";

        try {
            $res = DB::Connect()->prepare($sql);
            $res->execute(array(':id'=>$gameId));
        } catch (PDOException $e) {
            echo $e->getMessage();
            throw new ModelException("Error processing storage query", 500);
        }

        return $res->fetchColumn(0);
    }

    public function getStack($key = null, $mode = null)
    {
        $sql = "SELECT * FROM `GamesTmpPlayers`
            WHERE `AppMode` IS NOT NULL
            AND `AppUid` IS NULL";

        if (isset($key)) {

            if (is_numeric($key)) {
                $sql .= " AND `AppId` = '" . $key . "'";
            } else {
                $sql .= " AND `AppName` = '" . $key . "'";
            }

            if (isset($mode)){
                $sql .= " AND `AppMode` = '" . $mode . "'";
            }

        } else {
            $sql .= ' AND `Bot` IS NULL';
        }

        try {
            $res = DB::Connect()->prepare($sql);
            $res->execute();
        } catch (PDOException $e) {
            echo $e->getMessage();
            throw new ModelException("Error processing storage query", 500);
        }

        $stack = array();
        foreach ($res->fetchAll() as $playerData) {

            if (!isset($stack[$playerData[(isset($key) && is_numeric($key) ? 'AppId' : 'AppName')]]))
                $stack[$playerData[(isset($key) && is_numeric($key) ? 'AppId' : 'AppName')]] = array();

            if (!isset($stack[$playerData[(isset($key) && is_numeric($key) ? 'AppId' : 'AppName')]][$playerData['AppMode']]))
                $stack[$playerData[(isset($key) && is_numeric($key) ? 'AppId' : 'AppName')]][$playerData['AppMode']] = array();

            $player = new GamePlayer();
            $player->formatFrom('DB', $playerData);
            $stack[$playerData[(isset($key) && is_numeric($key) ? 'AppId' : 'AppName')]][$playerData['AppMode']][$playerData['PlayerId']] = $player;
        }

        if ($key) {
            if ($mode) {
                return isset($stack[$key]) && isset($stack[$key][$mode]) ? $stack[$key][$mode] : array();
            } else
                return isset($stack[$key]) ? $stack[$key] : array();
        } else
            return $stack;
    }

}
