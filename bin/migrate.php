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

class Migrate {

    private $indexOn = '';
    private $indexOff = '';
    private $length = 55;
    private $sqltime = 0;
    private $sql = 0;

    function __construct()
    {
        $this->indexOn = dirname(__FILE__).'/../index.html';
        $this->indexOff = $this->indexOn.'.off';
    }

    function run()
    {
        $storedMigrations = $this->getStoredMigrations();
        $newQueries = $this->getNewQueries($storedMigrations);
        $this->commitMigrations($newQueries);
    }

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

    function getNewQueries($migrations = array())
    {
        $queries = array();
        $path = dirname(__FILE__).'/../migrations';

        if (is_dir($path) && ($openDir = opendir($path))) {
            while (($file = readdir($openDir)) !== false) {
                if ($file != ".htaccess" && $file != "." && $file != "..") {
                    if (is_file($path.'/'.$file) && !in_array($file, $migrations)) {
                        $queries[$file] = file_get_contents ($path.'/'.$file);

                    }
                }
            }
        }

        return $queries;
    }

    function PDOQuery()
    {
        $this->startQuery();
        $sth = DB::Connect()->query($this->sql);
        while ($sth->nextRowset());
        $this->endQuery();
    }

    function directQuery()
    {

        $this->startQuery();
        //echo $this->sql;
        // $this->sql = trim(preg_replace(array("/\r\n/", "/\n/", "/\r\t/", "/  /"), " ", $this->sql));
        $conf = (object) \Config::instance()->dbConnectionProperties;
        $conf->dsn = preg_replace("/(.*)=/", "", explode(';', $conf->dsn));// 'mysql:host=127.0.0.1;dbname=lotzon_testbed',
        $mysqli = new mysqli($conf->dsn[0], $conf->user, $conf->password, $conf->dsn[1]);

        // $mysqli->multi_query("DROP PROCEDURE IF EXISTS AddColumnUnlessExists;CREATE PROCEDURE p(IN id_val INT) BEGIN INSERT INTO test(id) VALUES(id_val); END;DROP PROCEDURE IF EXISTS p;");
        // while ($mysqli->next_result());
        $mysqli->multi_query($this->sql);
        // while ($mysqli->next_result());


        //while ($mysqli->next_result()){
//            echo 1;
//        }

        //die;
        //$mysqli->multi_query($this->sql);
        //while ($mysqli->next_result());
        $this->endQuery();

    }


    function startQuery()
    {

        $this->sqltime = microtime();
        $echo = trim(preg_replace(array("/\r\n/", "/\n/", "/\r\t/", "/  /"), " ", $this->sql));

        echo "   " . (strlen($echo) > $this->length - 2 ? substr($echo, 0, $this->length - 5) . "..." : str_pad($echo . ' ', $this->length - 2, '.'));
    }

    function endQuery()
    {
        echo " [COMMIT] " . (round($this->sqltime - microtime(), 4)) . "s\n";
    }

    function storeMigration($file)
    {
        // DB::Connect()->prepare("INSERT INTO `DatabaseMigrations` (`File`) VALUES (:f)")->execute(array(':f' => $file));
    }


    function commitMigrations($queries=array())
    {
        $needle = array('function', 'procedure');

        if(!empty($queries)) {

            ksort($queries);

            try {

                echo "=========================== FILES TO PROCESS: " . count($queries) . " ==========================" . "\n";
                $time = microtime();
                $this->indexLock();

                foreach ($queries as $file => $query) {

                    $filetime = microtime();
                    echo "-> " . (strlen($file) > $this->length ? substr($file, 0, $this->length - 3) . "..." : $file) . ":\n";

                    if (preg_match('/(\b' . implode('\b|\b', $needle) . '\b)/i', $query)) {

                        $this->sql = $query;
                        $this->directQuery();

                    } else {

                        $query = array_filter(explode(';', $query));
                        foreach ($query as $sql) {

                            $this->sql = $sql;
                            $this->PDOQuery();

                        }
                    }

                    $this->storeMigration($file);
                    echo "<- File time: " . (round($filetime - microtime(), 4)) . "s\n";
                }

                $this->indexUnlock();
                echo "=========================== TOTAL TIME: " . (round($time - microtime(), 4)) . "s =========================" . "\n";

            } catch (PDOException $e) {

                $this->indexUnlock();
                die(" [ERROR] \n   MESSAGE: {$e->getMessage()}\n");
            }

        } else {

            echo "=============================== UP TO DATE ==============================" . "\n";
        }

    }

    function indexLock(){

        if(is_file($this->indexOff)){
            rename($this->indexOff, $this->indexOn);
        }
    }

    function indexUnlock(){

        if(is_file($this->indexOn)){
            rename($this->indexOn, $this->indexOff);
        }
    }
}

$migrate = new Migrate;
$migrate->run();
