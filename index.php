<?php
    
// load configs
$confs = glob(dirname(__FILE__) . '/protected/configs/*.php');
foreach ($confs as $conf) {
    require_once($conf);
}

?>