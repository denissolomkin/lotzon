<?php

Application::import(PATH_INTERFACES . 'IProcessor.php');

class GameAppsDBProcessor implements IProcessor
{

    /* GamesTmpApps Table */

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
            throw new ModelException("Error processing storage query: " . $e->getMessage(), 500);
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

    public function deleteApps()
    {
        $sql = "DELETE FROM `GamesTmpApps`";

        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute();

        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query: " . $e->getMessage(), 500);
        }

        return true;

    }

    public function getApp($uid)
    {
        $sql = "SELECT * FROM `GamesTmpApps`
                WHERE Uid = :uid";

        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(
                ':uid' => $uid,
            ));

        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query: " . $e->getMessage(), 500);
        }

        if (!$sth->rowCount()) {
            return false;
        } elseif ($sth->rowCount() > 1) {
            throw new ModelException("Found more than one app", 400);
        }

        $app = $sth->fetch();
        return unserialize($app['AppData']);

    }

    public function countApps($key = null, $status = null)
    {
        $where = array();
        $sql = "SELECT COUNT(*) FROM `GamesTmpApps`";

        if(isset($key)){
            $where[] =  '`'.(is_numeric($key) ? 'Id' : 'Key' ) . "` = '$key'";
        }

        if(isset($status)){
            $where[] =  '`isRun` = '.(int)$status;
            $where[] =  '`isOver` = 0';
        }

        if(count($where))
            $sql.=' WHERE ' . implode(' AND ', $where);

        try {
            $res = DB::Connect()->prepare($sql);
            $res->execute();
        } catch (PDOException $e) {
            echo $e->getMessage();
            throw new ModelException("Error processing storage query: ". $e->getMessage() , 500);
        }

        return $res->fetchColumn(0);
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
            throw new ModelException("Error processing storage query: " . $e->getMessage(), 500);
        }

        $apps = array();
        foreach ($res->fetchAll() as $appData) {
            $app = new \GameApp;
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

    public function create(Entity $app)
    {
        return $this->update($app);

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
                ':data'     => @serialize(array()/*$app*/),
                ':run'      => $app->isRun(),
                ':over'     => $app->isOver(),
                ':saved'    => $app->isSaved(),
                ':players'  => @serialize(array_values(array_map(function($a) { return is_object($a) ? $a->name: $a['name']; }, $app->getClients()))),
                ':nplayers' => $app->getNumberPlayers(),
                ':ping'     => $app->getPing()
            ));
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query: " . $e->getMessage(), 500);
        }

        return $app;

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

    /* PlayerGames Table */

    public function saveResults(\Game $app)
    {

        $sql_results    = $sql_transactions = $sql_transactions_players = array();
        $players        = $app->getPlayers();
        $playersBalance = array();
        $month          = mktime(0, 0, 0, date("n"), 1);
        foreach ($players as $player) {

            /* prepare results */
            array_push($sql_results,
                $player['pid'],
                $app->getId(),
                $app->getUid(),
                $app->getTime(),
                $month,
                ($player['result'] == 1 ? 1 : 0),  // win
                ($player['result'] == -1 ? 1 : 0), // lose
                ($player['result'] == 0 ? 1 : 0),  // draw
                $player['result'],
                isset($player['win']) ? $player['win'] : $player['result'] * $app->getPrice(),
                $app->getPrice() ? 1 : 0,
                $app->getCurrency(),
                $app->getPrice());

            if ($app->getPrice() AND $player['result'] != 0) {

                $currency = $app->getCurrency() == 'MONEY' ? 'Money' : 'Points';
                $win      = isset($player['win']) ? $player['win'] : $player['result'] * $app->getPrice();

                if ($currency == 'Money')
                    $win *= CountriesModel::instance()->getCountry($player['currency'])->loadCurrency()->getCoefficient();

                if ($win == 0)
                    continue;

                $sql_transactions_players[] = '(?,?,?,?,?,?,?,?,?,?,?)';

                /* update balance after game */
                $sql = "UPDATE Players p
                        LEFT JOIN `OnlineGamesTop` t
                          ON t.`Month` = :month AND t.GameId = :gid AND t.PlayerId = p.Id AND t.Currency = :cur
                        SET p." . $currency . " = p." . $currency . " + :win,
                            t.Rating = t.Rating + IF(:win < 0, 1, 26)
                        WHERE p.Id = :id";

                try {
                    $sth = DB::Connect()->prepare($sql);
                    $sth->execute(array(
                        ':id'    => $player['pid'],
                        ':month' => $month,
                        ':win'   => $win,
                        ':cur'   => $app->getCurrency(),
                        ':gid'   => $app->getId()
                    ));
                    $sth = null;
                } catch (\Exception $e) {
                    echo '[ERROR] ' . $e->getMessage();
                }

                /* select balance for transaction */
                $sql = "SELECT 
                            `Players`.Points,
                            `Players`.Money,
                            `MUICountries`.`Currency`,
                            `MUICurrency`.`Coefficient`,
                            `MUICurrency`.`Rate`
                        FROM `Players`
                        LEFT JOIN `MUICountries` ON `MUICountries`.`Code` = `Players`.`Currency`
                        LEFT JOIN `MUICurrency` ON `MUICurrency`.`Id` = `MUICountries`.`Currency`
                        WHERE `Players`.`Id`=:id LIMIT 1";

                try {
                    $sth = DB::Connect()->prepare($sql);
                    $sth->execute(array(':id' => $player['pid']));
                } catch (PDOException $e) {
                    echo $this->time(0,'ERROR')." Error processing storage query в таблице Players при получении баланса: {$e->getMessage()}\n";
                }

                if (!$sth->rowCount()) {
                    echo $this->time(0,'ERROR')." player #{$player['pid']} не найден в таблице Players при получении баланса\n";
                }

                if($balance = $sth->fetch()) {
                    $playersBalance[$player['pid']] = $balance;
                }

                /* prepare transactions */
                array_push($sql_transactions,
                    $player['pid'],
                    $app->getCurrency(),
                    isset($balance) && isset($balance['Currency']) ? ($currency == 'Money' ? $balance['Currency'] : 0) : 0,
                    isset($balance) && isset($balance['Coefficient']) ? ($currency == 'Money' ? $win / $balance['Coefficient'] : $win / ($balance['Rate'] * $balance['Coefficient'])) : 0,
                    $win,
                    (isset($balance) ? $balance[$currency] : null),
                    'OnlineGame',
                    $app->getId(),
                    $app->getUid(),
                    $app->getTitle($player['lang']),
                    $app->getTime()
                );
            }
        }

        try {
            $sql = "INSERT INTO `PlayerGames` (`PlayerId`, `GameId`, `GameUid`, `Date`, `Month`, `Win`, `Lose`, `Draw`, `Result`, `Prize`, `IsFee`, `Currency`, `Price`)
                    VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)" . str_repeat(',(?,?,?,?,?,?,?,?,?,?,?,?,?)', count($app->getPlayers()) - 1);
            DB::Connect()
                ->prepare($sql)
                ->execute($sql_results);
        } catch (PDOException $e) {
            echo '[ERROR] ' . $e->getMessage();
        }

        if ($app->getPrice() && count($sql_transactions_players)) {
            try {
                $sql = "INSERT INTO `Transactions` (`PlayerId`, `Currency`, `CurrencyId`, `Equivalent`, `Sum`, `Balance`, `ObjectType`, `ObjectId`,  `ObjectUid`, `Description`, `Date`) VALUES " . implode(",", $sql_transactions_players);
                DB::Connect()
                    ->prepare($sql)
                    ->execute($sql_transactions);
            } catch (PDOException $e) {
                echo '[ERROR] ' . $e->getMessage();
            }
        }

        return $playersBalance;
    }

    public function getFund($gameId = null)
    {

        $month = mktime(0, 0, 0, date("n"), 1);

        $sql = "SELECT SUM(Price) Total, Currency, GameId
                FROM (
                  SELECT DISTINCT GameId, GameUid, Date, Currency, Price
                  FROM `PlayerGames` WHERE `Month` = :month AND `IsFee` = 1
                  ) a
                GROUP BY GameId, Currency";

        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(
                array(
                    ':month' => $month
                ));
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query", 500);
        }

        $fund = array();

        foreach ($sth->fetchAll() as $row) {

            $fund[$row['GameId']][$row['Currency']] = $row['Total'];

        }

        return $fund;
    }

    public function getRating($gameId=null)
    {
        $month = mktime(0, 0, 0, date("n"), 1);

        /* Rating For All Games And Players */

        $sql = "(SELECT g.GameId, g.Currency, p.Nicname N,  p.Avatar A, p.Id I, (sum(g.Win)*25+count(g.Id)) R, 0 Top
                                FROM `PlayerGames` g
                                JOIN Players p On p.Id=g.PlayerId
                                WHERE g.`Month`=:month AND g.`IsFee` = 1 ". ($gameId?' AND g.`GameId` = '.$gameId:'') ."
                                group by g.GameId, g.Currency, g.PlayerId)

                    UNION ALL

                    (SELECT t.GameId, t.Currency, p.Nicname N,  p.Avatar A, p.Id I, t.Rating R, 1 Top
                                FROM `OnlineGamesTop` t
                                JOIN Players p On p.Id=t.PlayerId
                                WHERE t.`Month`=:month ". ($gameId?' AND t.`GameId` = '.$gameId:'') ."
                                )

                                order by Currency, R DESC
                                ";

        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(
                array(
                    ':month' => $month
                ));
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query", 500);

        }


        $rating = array();

        foreach ($sth->fetchAll() as $row) {

            $cur = $row['Currency'];
            $gid = $row['GameId'];
            $top = $row['Top'];

            unset($row['Currency'],$row['GameId'], $row['Top']);

            if(!isset($rating[$gid][$cur]['#'.$row['I']]))
                $rating[$gid][$cur]['#'.$row['I']] = $row;

        }

        return $rating;

    }

    public function getPlayerRating($gameId=null,$playerId=null)
    {
        $month = mktime(0, 0, 0, date("n"), 1);

        $sql = "SELECT Currency, (sum(Win)*25+count(Id)) R
                FROM(
                  SELECT Win, Id, Currency
                  FROM `PlayerGames` g
                  WHERE g.`Month`=:month AND g.`IsFee` = 1 AND g.`GameId` = :gameid AND g.`PlayerId` = :playerid
                ) t
                group by Currency";

        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(
                array(
                    ':month' => $month,
                    ':gameid' => $gameId,
                    ':playerid' => $playerId,
                ));
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query", 500);
        }

        $rating = array();

        foreach ($sth->fetchAll() as $row) {
            $rating[$row['Currency']] = $row['R'];
        }

        return $rating;

    }

    public function recacheRatingAndFund()
    {
        return false;
    }

    /* OnlineGamesTop Table */

    public function createGameTop($data)
    {
        $sql = "INSERT INTO `OnlineGamesTop`
                (`Id`, `PlayerId`, `GameId`, `Month`, `Currency`, `Rating`, `Increment`, `Period`, `Start`, `End`)
                VALUES
                (:id, :pid, :gid, :mon, :cur, :rat, :inc, :per, :str, :end)";

        try {
            DB::Connect()->prepare($sql)->execute(array(
                ':id'   => $data['Id'],
                ':pid'  => $data['PlayerId'],
                ':gid'  => $data['GameId'],
                ':mon'  => $data['Month'],
                ':cur'  => $data['Currency'],
                ':rat'  => $data['Rating'],
                ':inc'  => $data['Increment'],
                ':per'  => $data['Period'],
                ':str'  => strtotime($data['Start'],0),
                ':end'  => strtotime($data['End'],0),
            ));
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query" . $e->getMessage(), 500);
        }

        $data['Id'] = DB::Connect()->lastInsertId();
        return $data;
    }

    public function updateGameTop($data)
    {
        $sql = "UPDATE `OnlineGamesTop`
                SET `PlayerId` = :pid, `GameId` = :gid, `Currency` = :cur, `Increment` = :inc, `Period` = :per, `Start` = :str, `End` = :end
                WHERE `Id` = :id";

        try {
            DB::Connect()->prepare($sql)->execute(array(
                ':id'   => $data['Id'],
                ':pid'  => $data['PlayerId'],
                ':gid'  => $data['GameId'],
                ':cur'  => $data['Currency'],
                ':inc'  => $data['Increment'],
                ':per'  => $data['Period'],
                ':str'  => strtotime($data['Start'],0),
                ':end'  => strtotime($data['End'],0),
            ));
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query" . $e->getMessage(), 500);
        }

        $data['Id'] = DB::Connect()->lastInsertId();
        return $data;
    }

    public function getGameTop($month=null)
    {
        $month = $month ? : mktime(0, 0, 0, date("n"), 1);

        $sql = "SELECT g.*, p.Avatar, p.Nicname
                  FROM `OnlineGamesTop` g
                  LEFT JOIN `Players` p ON p.Id = g.PlayerId
                  WHERE g.`Month`=:month
                  ORDER BY g.GameId, g.Currency, g.Rating
                ";


        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(
                array(
                    ':month' => $month,
                ));
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query", 500);
        }

        $top = array();

        foreach ($sth->fetchAll() as $row) {

            $row['Start'] = date("H:i",$row['Start']);
            $row['End'] = date("H:i",$row['End']);
            $top[] = $row;
        }

        return $top;

    }

    public function incrementGameTop()
    {

        $month = mktime(0, 0, 0, date("n"), 1);
        $time  = strtotime(date("H:i"), 0);
        $now   = time();
        $rand  = rand(1,3);

        $sql = "UPDATE `OnlineGamesTop` t
                LEFT JOIN `Players` p ON p.Id = t.PlayerId
                  SET
                  t.`Rating` = IFNULL(t.Rating, 0) + IF( :rand = 1, 1, 26 ),
                  t.`LastUpdate` = :now,
                  p.`Money` = p.`Money` +
                    IF(
                      t.`Currency` = 'MONEY',
                      IF( :rand = 1, -0.25, 0.22 ) *
                        IFNULL(
                          (SELECT MUICurrency.Coefficient FROM MUICountries LEFT JOIN MUICurrency ON MUICurrency.Id = MUICountries.Currency WHERE MUICountries.Code = p.Currency),
                          (SELECT MUICurrency.Coefficient FROM MUICountries LEFT JOIN MUICurrency ON MUICurrency.Id = MUICountries.Currency LIMIT 1)
                        ),
                      0),
                  p.`Points` = p.`Points` + IF( t.`Currency` = 'POINT', IF( :rand = 1, -25, 22.5 ), 0)
                  WHERE t.`Month` = :month
                        AND t.`Increment` >= ROUND(RAND() * 100)
                        AND t.`Start` <= :time
                        AND t.`End` >= :time
                        AND t.`Period` > 0
                        AND t.`LastUpdate` <= :now - Period * 60
                ";

        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(
                array(
                    ':month' => $month,
                    ':time' => $time,
                    ':now' => $now,
                    ':rand' => $rand,
                ));
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query: ".$e->getMessage(), 500);
        }

        $sql = "REPLACE INTO `PlayerPing`
                  (PlayerId, Ping)
                  (SELECT PlayerId, :now - PlayerId%60
                  FROM `OnlineGamesTop`
                  WHERE `Month` = :month
                        AND `Start` <= :time
                        AND `End` >= :time)
                ";

        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(
                array(
                    ':month' => $month,
                    ':time' => $time,
                    ':now' => $now,
                ));
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query: ".$e->getMessage(), 500);
        }
    }

    public function deleteGameTop($id)
    {

        $sql = "DELETE FROM `OnlineGamesTop` WHERE `Id` = :id";

        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute(array(
                ':id' => $id
            ));

        } catch (PDOExeption $e) {
            throw new ModelException("Unable to process delete query", 500);
        }

        return true;

    }

    private function time($spaces = 0, $str = null)
    {
        return str_repeat(' ', $spaces) . date('H:i:s', time()) . ($str ? ' [' . str_pad($str, 7, '.') . '] ' : '');
    }

}
