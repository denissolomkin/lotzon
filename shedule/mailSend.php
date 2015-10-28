<?php

require_once('init.php');

Application::import(PATH_APPLICATION . '/model/entities/MaillistTask.php');

$list = MaillistModel::instance()->getTaskList('waiting');
if ($list==array()) {
    echo date("Y-m-d H:i:s").' No tasks'.PHP_EOL;
    return false;
}
$i=0;
foreach ($list as $task) {
    if ($task->isDoStart()) {
        $i++;
        echo date("Y-m-d H:i:s").' Start sending task ['.$task->getId().']'.PHP_EOL;
        $task->send();
        echo date("Y-m-d H:i:s").' done sending task ['.$task->getId().']'.PHP_EOL;
    }
}
if ($i==0) {
    echo date("Y-m-d H:i:s").' Nothing to send'.PHP_EOL;
}
