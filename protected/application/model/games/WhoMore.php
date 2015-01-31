<?php

Application::import(PATH_APPLICATION . 'model/Game.php');

class WhoMore extends Game
{
    const   STACK_PLAYERS = 2;
    const   GAME_PLAYERS = 2;
    const   TIME_OUT = 20;
    const   FIELD_SIZE_X = 7;
    const   FIELD_SIZE_Y = 7;
    const   GAME_MOVES = 6;

    protected $_gameid = 1;
    protected $_gameTitle = '"Кто больше"';
}