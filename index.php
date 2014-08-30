<?php
    
error_reporting(E_ALL);
ini_set('display_errors', 1);

// load pathes 
require_once dirname(__FILE__) . '/pathes.php';
// load application class
require_once PATH_APPLICATION . 'Application.php';

// load configs
Application::import(PATH_SYSTEM . '*');
Application::import(PATH_CONFIGS . '*');