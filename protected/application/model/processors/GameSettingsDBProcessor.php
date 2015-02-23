<?php

class GameSettingsDBProcessor
{
    public function saveSettings(GameSettings $settings)
    {
        $cleanup = array(
            "TRUNCATE TABLE `GamesSettings`",
            "TRUNCATE TABLE `LotterySettings`",
        );

        $timesSql = "INSERT INTO `GamesSettings` (`StartTime`,`Tries`,`Balls`) VALUES %s";
        $parts = array();
        /*foreach ($settings->getGameTimes() as $time) {
            $parts[] = sprintf("(%s,%s,%s)", array(DB::Connect()->quote($time),DB::Connect()->quote($time),DB::Connect()->quote($time)));
        }*/
        foreach ($settings->getGameSettings() as $time) {
            $parts[] = vsprintf("(%s,%s,%s)", array(DB::Connect()->quote($time['StartTime']),DB::Connect()->quote($time['Tries']),DB::Connect()->quote($time['Balls'])));
        }
        $timesSql = sprintf($timesSql, join(",", $parts));

        $prizesSql = "INSERT INTO `LotterySettings` (`BallsCount`, `CountryCode`, `Prize`, `Currency`, `SumTotal`, `Coefficient`, `Rate`, `JackPot`) VALUES %s";
        $parts = array();

        foreach ($settings->getPrizes() as $country => $prizes) {
            $coeficient = $settings->getCountryCoefficient($country);
            $rate = $settings->getCountryRate($country);
            foreach ($prizes as $ballsCount => $prize) {
                $parts[] = vsprintf(("(%s,%s,%s,%s,%s,%s,%s,%s)"), array(
                    DB::Connect()->quote($ballsCount),
                    DB::Connect()->quote($country),
                    DB::Connect()->quote($prize['sum']),
                    DB::Connect()->quote($prize['currency']),
                    DB::Connect()->quote($settings->getTotalWinSum()),
                    DB::Connect()->quote($coeficient),
                    DB::Connect()->quote($rate),
                    DB::Connect()->quote($settings->getJackpot()),
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
            DB::Connect()->commit();
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
              $settings->addGameSettings($time);
        }


        $lots = $lots->fetchAll();
        $prizes = array();
        foreach ($lots as $lottery) {
            if (!$settings->getTotalWinSum()) {
                $settings->setTotalWinSum($lottery['SumTotal']);
            }
            if (!$settings->getCountryCoefficient($lottery['CountryCode'])) {
                $settings->setCountryCoefficient($lottery['CountryCode'], $lottery['Coefficient']);
            }
            if (!$settings->getCountryRate($lottery['CountryCode'])) {
                $settings->setCountryRate($lottery['CountryCode'], $lottery['Rate']);
            }
            $settings->setJackpot($lottery['JackPot']);
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