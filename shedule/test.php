<?php

require_once('init.php');

Application::import(PATH_APPLICATION . '/model/entities/ChanceGame.php');

$game = new ChanceGame();
$game->setIdentifier('55');

visualise($game->generateGame());

function visualise($game) {
    for ($i = 1; $i <= count($game); ++$i) {
        for ($j = 1; $j <= count($game[$i]); ++$j) {
            echo $game[$i][$j] . " ";
        }

        echo PHP_EOL;
    }
}