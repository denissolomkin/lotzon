<?php
echo PHP_EOL.'************ '.date('Y-m-d H:i:s').' ************'.PHP_EOL;

require_once('lottery.inc.php');

if(@$_SERVER['argv'][1] == 'roll') {
    if(!file_exists($tmp = __DIR__.'/lottery.lock.tmp')) {
        file_put_contents($tmp, '');
        RollBack();
        if (file_exists($tmp = __DIR__ . '/lottery.lock.tmp')) {
            unlink($tmp);
        }
    }
    exit("rollback done");
}

if(timeToRunLottery()) {
    if(!file_exists($tmp = __DIR__.'/lottery.lock.tmp')) {
        file_put_contents($tmp, '');
        if (isset($gameSettings['lotteryId'])) {
            RollBack();
            $comb['id'] = $gameSettings['lotteryId'];
            ApplyLotteryCombinationAndCheck($comb);
        } else {
            HoldLottery(0, (is_array($gameSettings) ? $gameSettings['Balls'] : 43), (is_array($gameSettings) ? $gameSettings['Tries'] : 150), $gameSettings['Increments'], $gameSettings['GoldIncrements']);
        }
        if (file_exists($tmp = __DIR__ . '/lottery.lock.tmp'))
            unlink($tmp);
    } else {
        echo $tmp.' is exists'.PHP_EOL;
    }
} else {
    echo 'timeToRunLottery is false'.PHP_EOL;
}
