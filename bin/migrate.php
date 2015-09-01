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
    $length = 55;
    $i = 0;
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

                echo "=========================== FILES TO PROCESS: ".count($queries)." =========================="."\n";
                $time = microtime();
                indexLock();

                foreach($queries as $file => $query) {

                    $filetime = microtime();

                    echo "-> ".(strlen($file)>$length ? substr($file,0,$length-3)."..." : $file ).":\n";

                    $query = array_filter(explode(';',$query));
                    foreach($query as $sql){

                        $sqltime = microtime();

                        $sql = str_replace('%%',';',$sql);
                        $echo = trim(preg_replace(array("/\r\n/","/\n/","/\r\t/","/  /")," ",$sql));

                        echo "   ".(strlen($echo)>$length-2 ? substr($echo,0,$length-5)."..." : str_pad($echo.' ',$length-2,'.'));

                        DB::Connect()->prepare($sql)->execute();


                        echo " [COMMIT] ".(round($sqltime-microtime(),4))."s";
                        echo "\n";
                    }

                    DB::Connect()->prepare("INSERT INTO `DatabaseMigrations` (`File`) VALUES (:f)")->execute(array(':f' => $file));

                    echo "<- File time: ".(round($filetime-microtime(),4))."s\n";


                }

                indexUnlock();
                echo "=========================== TOTAL TIME: ".(round($time-microtime(),4))."s ========================="."\n";

            } catch (PDOException $e) {

                indexUnlock();
                die(" [ERROR] \n   MESSAGE: {$e->getMessage()}\n");

            }

        } else {

            echo "=============================== UP TO DATE =============================="."\n";
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