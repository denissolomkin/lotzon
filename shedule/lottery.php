<?php
echo PHP_EOL.'************ '.date('Y-m-d H:i:s').' ************'.PHP_EOL;

require_once('lottery.inc.php');

if(@$_SERVER['argv'][1] == 'roll') {
    RollBack('');
}

if(timeToRunLottery()) {
    if(!file_exists($tmp = __DIR__.'/lottery.lock.tmp')) {
        file_put_contents($tmp, '');
        $data = HoldLottery(0, (is_array($gameSettings)?$gameSettings['Balls']:43), (is_array($gameSettings)?$gameSettings['Tries']:150));

        if (empty($data)
            || current(DB::Connect()->query("SELECT Ready FROM Lotteries WHERE Id = {$data['id']}")->fetch())
        ) {
            if (file_exists($tmp = __DIR__ . '/lottery.lock.tmp'))
                unlink($tmp);
        } else {
            echo 'HoldLottery is not ready'.PHP_EOL;
        }
    } else {
        echo $tmp.' is exists'.PHP_EOL;
    }
} else {
    echo 'timeToRunLottery is false'.PHP_EOL;
}
