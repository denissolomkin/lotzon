<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

// load pathes
require_once dirname(__FILE__) . '/../pathes.php';

// load application class
require_once PATH_APPLICATION . 'Application.php';

require dirname(__FILE__) . '/../vendor/autoload.php';

// load configs
Application::import(PATH_SYSTEM . 'Config.php');
Application::import(PATH_SYSTEM . '*');
Application::import(PATH_CONFIGS . '*');
Application::import(PATH_CONTROLLERS . '*');

function getStoredMigrations()
{
    $migrations = array();
    if (DB::Connect()->query("SHOW TABLES LIKE 'DatabaseMigrations'")->rowCount() > 0) {

        $sql = "SELECT * FROM `DatabaseMigrations`";

        try {
            $sth = DB::Connect()->prepare($sql);
            $sth->execute();
        } catch (PDOException $e) {
            throw new ModelException("Error processing storage query", 500);
        }

        $migrationsData = $sth->fetchAll();

        foreach ($migrationsData as $data) {
            $migrations[$data['Id']] = $data['File'];
        }

    }
    return $migrations;
}

function commitMigrations($migrations = array())
{
    $queries = array();
    $path = dirname(__FILE__).'/../migrations';
    if (is_dir($path) && ($openDir = opendir($path))) {
        while (($file = readdir($openDir)) !== false) {
            if ($file != "." && $file != "..") {
                if (is_file($path.'/'.$file) && !in_array($file, $migrations)) {

                    $queries[$file] = file_get_contents ($path.'/'.$file);

                }
            }
        }

        if(!empty($queries)){

            ksort($queries);

            try {

                foreach($queries as $file => $sql) {

                    DB::Connect()->prepare($sql)->execute();
                    DB::Connect()->prepare("INSERT INTO `DatabaseMigrations` (`File`) VALUES (:f)")->execute(array(':f' => $file));

                    echo "\t$file [COMMIT]\n";

                }

            } catch (PDOException $e) {

                // throw new ModelException("Error processing storage query: ".$e->getMessage(), 500);
                die(
                    "\t$file [ERROR]\n
                    \tRollBack: {$e->getMessage()}\n"
                );
            }


        }

    }
}

function indexLock(){

    $indexOff = dirname(__FILE__).'/../index.html.off';
    $indexOn = str_replace('.off','',$indexOff);

    if(is_file($indexOff)){
        rename($indexOff, $indexOn);
    }
}

function indexUnlock(){

    $indexOff = dirname(__FILE__).'/../index.html.off';
    $indexOn = str_replace('.off','',$indexOff);

    if(is_file($indexOff)){
        rename($indexOff, $indexOn);
    }

    if(is_file($indexOn)){
        rename($indexOn, $indexOff);
    }
}


$storedMigrations = getStoredMigrations();
commitMigrations($storedMigrations);