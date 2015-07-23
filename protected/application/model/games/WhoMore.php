<?php

Application::import(PATH_APPLICATION . 'model/Game.php');

class WhoMore extends Game {
    protected $_gameVariation = array('field'=>'7x7');
}