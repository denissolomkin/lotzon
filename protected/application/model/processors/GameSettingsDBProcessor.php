<?php

class GameSettingsDBProcessor
{
    public function saveSettings(GameSettings $settings)
    {
        $cleanup = array(
            "TRUNCATE TABLE `GamesSettings`",
            "TRUNCATE TABLE `LotterySettings`",
        );

        $timesSql = "INSERT INTO `GamesSettings` (`StartTime`) VALUES %s";
        $parts = array();
        foreach ($settings->getGameTimes() as $time) {
            $parts[] = sprintf("(%s)", DB::Connect()->quote($time));
        }
        $timesSql = sprintf($timesSql, join(",", $parts));

        $prizesSql = "INSERT INTO `LotterySettings` (`BallsCount`, `CountryCode`, `Prize`, `Currency`, `SumTotal`, `JackPot`) VALUES %s";
        $parts = array();

        foreach ($settings->getPrizes() as $country => $prizes) {
            $totals = $settings->getTotalWinSum($country);

            foreach ($prizes as $ballsCount => $prize) {
                $parts[] = vsprintf(("(%s,%s,%s,%s,%s,%s)"), array(
                    DB::Connect()->quote($ballsCount),
                    DB::Connect()->quote($country),
                    DB::Connect()->quote($prize['sum']),
                    DB::Connect()->quote($prize['currency']),
                    DB::Connect()->quote($totals['sum']),
                    DB::Connect()->quote((int)$totals['isJackpot']),
                ));
            }
        }
        $prizesSql = sprintf($prizesSql, join(",", $parts));

        DB::Connect()->beginTransaction();
        try {
            foreach ($cleanup as $query) {
                DB::Connect()->query($query);
            }

            DB::Connect()->query($timesSql);
            DB::Connect()->query($prizesSql);
        } catch (PDOException $e) {
            DB::Connect()->rollback();

            throw new ModelException($e->getMessage() . "Unable to process storage query", 500);
        }

        return $settings;
    }

    public function loadSettings()
    {
        $settings = new GameSettings();

        $sql = "SELECT * FROM `GamesSettings` ORDER BY `StartTime` ASC";
        $lotsql = "SELECT * FROM `LotterySettings`";

        try {
            $times = DB::Connect()->query($sql);
            $lots = DB::Connect()->query($lotsql);
        } catch (PDOException $e) {
            throw new ModelException("Unable to process storage query", 500);
        }

        $times = $times->fetchAll();
        foreach ($times as $time) {
            $settings->addGameTime($time['StartTime']);
        }

        $lots = $lots->fetchAll();
        $prizes = array();
        foreach ($lots as $lottery) {
            if (!$settings->getTotalWinSum($lottery['CountryCode'])) {
                $settings->setTotalWinSum($lottery['CountryCode'], $lottery['SumTotal'], $lottery['JackPot']);
            }            
            if (!isset($prizes[$lottery['CountryCode']])) {
                $prizes[$lottery['CountryCode']] = array();
            }
            $prizes[$lottery['CountryCode']][$lottery['BallsCount']] = array(
                'sum' => $lottery['Prize'],
                'currency' => $lottery['Currency']
            );
        }

        foreach ($prizes as $country => $prize) {
            $settings->setPrizes($country, $prize);
        }

        return $settings;
    }
}