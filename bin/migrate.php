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
    $length = 56;
    $path = dirname(__FILE__).'/../migrations';
    if (is_dir($path) && ($openDir = opendir($path))) {
        while (($file = readdir($openDir)) !== false) {
            if ($file != ".htaccess" && $file != "." && $file != "..") {
                if (is_file($path.'/'.$file) && !in_array($file, $migrations)) {
                    $queries[$file] = file_get_contents ($path.'/'.$file);

                }
            }
        }

        if(!empty($queries)){

            ksort($queries);

            try {

                echo "=========================== ".count($queries)." FILES TO PROCESS =========================="."\n";
                $time = microtime();
                indexLock();

                foreach($queries as $file => $sql) {

                    $microtime = microtime();
                    if(strlen($file)>$length)
                        echo substr($file,0,$length-3)."...";
                    else
                        echo "$file";

                    $sth = DB::Connect()->prepare($sql);
                    $sth->execute();
                    while ($sth->nextRowset());

                    DB::Connect()->prepare("INSERT INTO `DatabaseMigrations` (`File`) VALUES (:f)")->execute(array(':f' => $file));

                    echo " [COMMIT] ".(round($microtime-microtime(),4))."s\n";


                }

                indexUnlock();
                echo "=========================== TOTAL TIME ".(round($time-microtime(),4))."s =========================="."\n";

            } catch (PDOException $e) {

                indexUnlock();
                die("\t[ERROR] \n\tMESSAGE: {$e->getMessage()}\n");

            }

        }

    }
}

function indexLock(){

    $indexOn = dirname(__FILE__).'/../index.html';
    $indexOff = $indexOn.'.off';

    if(is_file($indexOff)){
        rename($indexOff, $indexOn);
    }
}

function indexUnlock(){

    $indexOn = dirname(__FILE__).'/../index.html';
    $indexOff = $indexOn.'.off';

    if(is_file($indexOn)){
        rename($indexOn, $indexOff);
    }
}


$storedMigrations = getStoredMigrations();
commitMigrations($storedMigrations);