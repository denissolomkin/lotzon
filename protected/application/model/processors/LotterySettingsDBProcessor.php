<?php

class LotterySettingsDBProcessor
{
    public function saveSettings(LotterySettings $settings)
    {
        $cleanup = array(
            "TRUNCATE TABLE `LotteryScheduler`",
            "TRUNCATE TABLE `LotterySettings`",
        );

        $timesSql = "INSERT INTO `LotteryScheduler` (`StartTime`,`Tries`,`Balls`,`Increments`) VALUES %s";
        $parts = array();
        /*foreach ($settings->getGameTimes() as $time) {
            $parts[] = sprintf("(%s,%s,%s)", array(DB::Connect()->quote($time),DB::Connect()->quote($time),DB::Connect()->quote($time)));
        }*/
        foreach ($settings->getLotterySettings() as $time) {
            $parts[] = vsprintf("(%s,%s,%s,%s)", array(
                DB::Connect()->quote($time['StartTime']),
                DB::Connect()->quote($time['Tries']),
                DB::Connect()->quote($time['Balls']),
                DB::Connect()->quote(serialize($settings->getGameIncrements()))));
        }
        $timesSql = sprintf($timesSql, join(",", $parts));

        $prizesSql = "INSERT INTO `LotterySettings` (`BallsCount`, `CountryCode`, `Prize`, `Currency`) VALUES %s";
        $parts = array();

        foreach ($settings->getPrizes() as $country => $prizes) {
            foreach ($prizes as $ballsCount => $prize) {
                $parts[] = vsprintf(("(%s,%s,%s,%s)"), array(
                    DB::Connect()->quote($ballsCount),
                    DB::Connect()->quote($country),
                    DB::Connect()->quote($prize['sum']),
                    DB::Connect()->quote($prize['currency']),
                ));
            }
        }
        $prizesSql = sprintf($prizesSql, join(",", $parts));

        $goldPrizesSql = "INSERT INTO `LotterySettings` (`BallsCount`, `CountryCode`, `Prize`, `Currency`, `Gold`) VALUES %s";
        $parts = array();

        foreach ($settings->getGoldPrizes() as $country => $prizes) {
            foreach ($prizes as $ballsCount => $prize) {
                $parts[] = vsprintf(("(%s,%s,%s,%s,%s)"), array(
                    DB::Connect()->quote($ballsCount),
                    DB::Connect()->quote($country),
                    DB::Connect()->quote($prize['sum']),
                    DB::Connect()->quote($prize['currency']),
                    DB::Connect()->quote(1),
                ));
            }
        }
        $goldPrizesSql = sprintf($goldPrizesSql, join(",", $parts));
        DB::Connect()->beginTransaction();
        try {
            foreach ($cleanup as $query) {
                DB::Connect()->query($query);
            }

            DB::Connect()->query($timesSql);
            DB::Connect()->query($prizesSql);
            DB::Connect()->query($goldPrizesSql);
            DB::Connect()->commit();
        } catch (PDOException $e) {
            DB::Connect()->rollBack();

            throw new ModelException($e->getMessage() . "Unable to process storage query", 500);
        }

        return $settings;
    }

    public function loadSettings()
    {
        $settings = new LotterySettings();

        $sql = "SELECT * FROM `LotteryScheduler` ORDER BY `StartTime` ASC";
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
              $settings->addLotterySettings($time);
              $settings->setGameIncrements(unserialize($time['Increments']));
        }

        $lots = $lots->fetchAll();

        $prizes     = array();
        $goldPrizes = array();
        foreach ($lots as $lottery) {
            if($lottery['Gold']) {
                if (!isset($goldPrizes[$lottery['CountryCode']])) {
                    $goldPrizes[$lottery['CountryCode']] = array();
                }
                $goldPrizes[$lottery['CountryCode']][$lottery['BallsCount']] = array(
                    'sum'      => $lottery['Prize'],
                    'currency' => $lottery['Currency']
                );
            } else {
                if (!isset($prizes[$lottery['CountryCode']])) {
                    $prizes[$lottery['CountryCode']] = array();
                }
                $prizes[$lottery['CountryCode']][$lottery['BallsCount']] = array(
                    'sum'      => $lottery['Prize'],
                    'currency' => $lottery['Currency']
                );
            }
        }

        foreach ($prizes as $country => $prize) {
            $settings->setPrizes($country, $prize);
        }
        foreach ($goldPrizes as $country => $prize) {
            $settings->setGoldPrizes($country, $prize);
        }

        return $settings;
    }
}