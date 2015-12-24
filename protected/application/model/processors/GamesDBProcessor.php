<?php
use \GamePlayer;

class GamesDBProcessor
{
    public function update(Entity $game)
    {
        $sql = "REPLACE INTO `GameSettings` (`Key`, `Title`, `Options`, `Games`) VALUES (:key, :gt, :opt, :gms)";

        try {
            DB::Connect()->prepare($sql)->execute(array(
                ':key' => $game->getKey(),
                ':gt'  => $game->getTitle(),
                ':opt' => @serialize($game->getOptions()),
                ':gms' => @serialize($game->getGames()),
            ));
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query" . $e->getMessage(), 500);
        }

        return $game;
    }

    public function getList()
    {
        $sql = "SELECT * FROM `GamesTmpApps`";

        try {
            $sth = DB::Connect()
                ->prepare($sql)
                ->execute();

        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query", 500);
        }

        $games = array();
        $data  = $sth->fetchAll();

        foreach ($data as $gameData) {

            $game = new GameSettings();
            $game->formatFrom('DB', $gameData);
            $games[$game->getKey()] = $game;
        }

        return $games;
    }

    public function saveResults(\Game $app)
    {

        $sql_results = $sql_transactions = $sql_transactions_players = array();
        $players     = $app->getPlayers();
        $month       = mktime(0, 0, 0, date("n"), 1);

        foreach ($players as $player) {

            /* prepare results */
            array_push($sql_results,
                $player['pid'],
                $app->getId(),
                $app->getIdentifier(),
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

            if ($app->getPrice() AND $player['result'] != 0 AND !isset($app->getClients()[$player['pid']]->bot)) {

                $currency = $app->getCurrency() == 'MONEY' ? 'Money' : 'Points';
                $win      = isset($player['win']) ? $player['win'] : $player['result'] * $app->getPrice();

                if ($currency == 'Money')
                    $win *= CountriesModel::instance()->getCountry($player['сountry'])->loadCurrency()->getCoefficient();

                if ($win == 0)
                    continue;

                $sql_transactions_players[] = '(?,?,?,?,?,?)';

                /* update balance after game */
                $sql = "UPDATE Players SET " . $currency . " = " . $currency . ($win < 0 ? '' : '+') . ($win) . " WHERE Id=" . $player['pid'];

                try {
                    DB::Connect()->query($sql);
                } catch (\Exception $e) {
                    echo '[ERROR] ' . $e->getMessage();
                }

                /* prepare transactions */
                array_push($sql_transactions,
                    $player['pid'],
                    $app->getCurrency(),
                    $win,
                    (isset($balance) ? $balance[$currency] : null),
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
                $sql = "INSERT INTO `Transactions` (`PlayerId`, `Currency`, `Sum`, `Balance`, `Description`, `Date`) VALUES " . implode(",", $sql_transactions_players);
                DB::Connect()
                    ->prepare($sql)
                    ->execute($sql_transactions);
            } catch (PDOException $e) {
                echo '[ERROR] ' . $e->getMessage();
            }
        }

        return true;
    }


}
