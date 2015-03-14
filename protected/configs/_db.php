<?php

Config::instance()->dbConnectionProperties = array(

    // testbed
    'dsn' => 'mysql:host=127.0.0.1;dbname=lotzon_testbed',
    'user' => 'testbed',
    'password' => '2p9G808CVn17P',

    // public
    'dsn' => 'mysql:host=127.0.0.1;dbname=lotzone',
    'user' => 'lotzone_user',
    'password' => '63{_Tc252!#UoQq',

    // local
    'dsn' => 'mysql:host=localhost;dbname=lotzone',
    'user' => 'root',
    'password' => '1234',

    'options' => array(
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\'',
    ),
);

Config::instance()->wsPort = 8080;