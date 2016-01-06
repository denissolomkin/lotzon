<?php namespace controllers\production;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Ratchet\Wamp\Exception;
use \Cache, \DB, \Config, \Application;
use \Player, \GamePlayer, \GamePlayersModel, \GameApp, \GameAppsModel;
use \SettingsModel, \GamesModel, \GameConstructorModel, \GameConstructorOnline, \OnlineGamesModel;


Application::import(PATH_APPLICATION . '/model/Game.php');
Application::import(PATH_APPLICATION . '/model/entities/Player.php');
Application::import(PATH_APPLICATION . '/model/entities/GamePlayer.php');
Application::import(PATH_APPLICATION . '/model/entities/GameApp.php');
Application::import(PATH_APPLICATION . '/model/entities/GameConstructorOnline.php');
Application::import(PATH_GAMES . '*');

class WebSocketController implements MessageComponentInterface
{

    const   MIN_WAIT_TIME    = 15;//15;
    const   MAX_WAIT_TIME    = 600;//600;
    const   PERIODIC_TIMER   = 2;//2
    const   CONNECTION_TIMER = 1800;
    const   DEFAULT_MODE     = 'POINT-0-2';
    const   EMULATION        = false; //false;

    private $_reload = true;
    private $_class  = null;
    private $_loop   = null;

    private $_clients = array();
    private $_apps    = array();

    private $memcache;

    public function __construct($loop)
    {

        error_reporting(E_ALL);
        ini_set('display_errors', 1);

        echo $this->time(0, 'START') . " " . "Server have started\n";

        $this->_loop = $loop;
        $this->_loop->addPeriodicTimer(self::PERIODIC_TIMER, function () {
            $this->periodicTimer();
        });
        $this->_loop->addPeriodicTimer(self::CONNECTION_TIMER, function () {
            $this->checkConnections();
        });

        $this->memcache = new \Memcache;
        $this->memcache->connect('localhost', 11211);
        OnlineGamesModel::instance()->recacheRatingAndFund();
    }

    /***********************
     **      Periodic
     ***********************/

    public function checkConnections()
    {
        foreach (GamePlayersModel::instance()->getList() as $gamePlayer)
            if ($gamePlayer->getPing() < time() - self::CONNECTION_TIMER) {
                echo "checkConnections:" . $gamePlayer->getId();

                echo $this->time() . " " . "#{$gamePlayer->getId()} ping timeout\n";

                /* если отсутствует более положенного времени, сигнализирует об уходе */
                $this->quitPlayer($gamePlayer->getId());
                $gamePlayer->delete();

                if (($client = $this->clients($gamePlayer->getId())) && $client instanceof ConnectionInterface) {
                    echo $this->time(0, 'CLOSE') . " #{$gamePlayer->getId()} {$client->Session->getId()} \n";
                    $client->close();
                } else {
                    echo $this->time(0, 'ERROR') . " client #{$gamePlayer->getId()} не найден в коллекции\n";
                }
            }
    }

    public function periodicTimer()
    {
        $this->periodicStack();
        $this->periodicApps();
        $this->periodicReload();
    }

    public function periodicStack()
    {

        $gameConstructor = new GameConstructorOnline();

        foreach (GamePlayersModel::instance()->getStack() as $key => $modes) {

            $gameConstructor
                ->setType('online')
                ->setKey($key)
                ->fetch();

            foreach ($modes as $mode => $stacks) {
                foreach ($stacks as $id => $gamePlayer) {

                    echo "periodicStack:" . $id . "\n";

                    if ($this->_reload)
                        $this->_reload = false;

                    if ($gamePlayer->getPing() + self::MIN_WAIT_TIME < time() && $gameConstructor->getOptions('b')) {
                        $clients      = array();
                        $clients[$id] = $gamePlayer->export('player');

                        while (count($clients) < $gameConstructor->getOptions('p')) {
                            do {
                                $bot        = (object)SettingsModel::instance()->getSettings('gameBots')->getValue()[array_rand(SettingsModel::instance()->getSettings('gameBots')->getValue())];
                                $gamePlayer = new GamePlayer();
                                $gamePlayer
                                    ->setId($bot->id)
                                    ->fetch();
                            } while (array_key_exists($bot->id, $clients) || $gamePlayer->getApp('Uid'));
                            $clients[$bot->id] = $bot;
                            $gamePlayer->formatFrom('bot', $bot)->update();
                        }

                        $appMode = array(
                            'currency' => null,
                            'price'    => null,
                            'number'   => null
                        );

                        list($appMode['currency'], $appMode['price'], $appMode['number'], $appVariation) = array_pad(explode("-", $mode), 4, null);

                        $appMode['mode'] = implode('-', $appMode) . '-' . $appVariation;

                        if ($appVariation)
                            parse_str($appVariation, $appVariation);

                        $this->initGame($clients, $key, $appMode, $gameConstructor->initVariation($appVariation), $id);

                    } elseif ($gamePlayer->getPing() + self::MAX_WAIT_TIME < time()) {

                        echo $this->time(0) . " $key " . "Игрок {$gamePlayer->getId()} удален из стека {$gamePlayer->getApp('Mode')} по таймауту\n";

                        $gamePlayer
                            ->setAppMode(null)
                            ->update();

                        $this->clients($gamePlayer->getId())->send(json_encode(array('path' => 'cancel')));
                    }
                }
            }
        }
    }

    public function periodicApps()
    {

        foreach ($this->apps() as $appName => $apps) {
            if (is_numeric($appName))
                continue;
            echo 'periodicAppName: ' . $appName . "\n";
            foreach ($apps as $id => $app) {

                $app = $app->getApp();
                echo 'periodicAppUid: ' . $app->getUid() . "\n";

                if ($this->_reload)
                    $this->_reload = false;

                if ($app->isOver() && !empty($app->_bot)) {
                    foreach ($app->_bot as $bot) {
                        if ($app->currentPlayer()['timeout'] - $app->getOptions('t') + 10 < time()) {
                            #echo " -- таймер на выход после 10 сек \n";
                            $this->runGame($appName, $app->getUid(), 'quitAction', $bot);
                        } elseif (!in_array($bot, $app->_botReplay) && $app->currentPlayer()['timeout'] + rand(2, 4) - $app->getOptions('t') < time()) {
                            if (rand(1, 5) == 1) {
                                #echo " -- таймер на случайный выход\n";
                                $this->runGame($appName, $app->getUid(), 'quitAction', $bot);
                            } else {
                                #echo " -- таймер на повтор \n";
                                $this->runGame($appName, $app->getUid(), 'replayAction', $bot);
                            }
                        }
                    }

                } elseif (!$app->isOver() && $app->isRun() && $app->getTime() + $app->getOptions('t') < time() && $app->currentPlayer()['timeout'] < time() && $app->currentPlayer()['pid']) {
                    echo " -- таймер на таймаут \n";
                    $this->runGame($appName, $app->getUid(), 'timeoutAction', $app->currentPlayer()['pid']);

                } elseif (!$app->isOver() && !$app->isRun() && $app->getOptions('b') && $app->getNumberPlayers() > count($app->getClients())) {

                    echo "bot fetch ";

                    do {
                        $bot     = (object)SettingsModel::instance()->getSettings('gameBots')->getValue()[array_rand(SettingsModel::instance()->getSettings('gameBots')->getValue())];
                        $gameBot = new GamePlayer();
                        $gameBot
                            ->setId($bot->id)
                            ->fetch();
                        echo "array_rand bot$bot->id";
                        echo " test: " . array_key_exists($bot->id, $app->getClients()) . "/" . $gameBot->getApp('Uid') . "\r\n";
                    } while (array_key_exists($bot->id, $app->getClients()) || $gameBot->getApp('Uid'));

                    $gameBot
                        ->formatFrom('bot', $bot)
                        ->setAppId($app->getId())
                        ->setAppUid($app->getUid())
                        ->setAppName($app->getKey())
                        ->setAppMode($app->getMode())
                        ->update();

                    $bot->time = time();
                    $app->addClient(array($bot->id => $bot));

                    /* todo $app->update() */
                    $this->apps($app->getKey(), $app->getUid(), $app);

                    $this->runGame($appName, $app->getUid(), 'startAction', $bot->id);
                    #echo " -- таймер на добавление бота в игру \n";

                } elseif (!$app->isRun()
                    && ((isset($app->currentPlayer()['timeout']) && $app->currentPlayer()['timeout'] + 60 < time())
                        || (!isset($app->currentPlayer()['timeout']) && $app->getTime() + 120 < time()))
                ) {

                    foreach ($app->getPlayers() as $player) {
                        echo " -- таймер на выход игрока №{$player['pid']}\n";
                        $this->quitPlayer($player['pid']);
                    }
                }

            }
        }
    }

    public function periodicReload()
    {

        if ($this->_reload) {
            $seo = \SEOModel::instance()->getSEOSettings();
            if ($seo['WebSocketReload']) {
                echo $this->time() . " ПЕРЕЗАГРУЗКА \n";
                $seo['WebSocketReload'] = 0;
                \SEOModel::instance()->updateSEO($seo);
                die;
            }
        } else {
            $this->_reload = true;
        }
    }


    /***********************
     **        Game
     ***********************/

    public function initGame($clients, $appName, $appMode, $appVariation, $clientId)
    {
        $this->_class = $class = '\\' . $appName;

        $app = new $class(
            GameConstructorModel::instance()->getGame($appName),
            $appVariation);

        echo $this->time() . " $appName инициируем приложение " . $appMode['mode'] . ": №" . implode(', №', array_keys($clients)) . "\n";

        #echo $this->time()." чистим стек\n";
        foreach (array_keys($clients) as $playerId) {

            $gamePlayer = new GamePlayer;
            $gamePlayer
                ->setId($playerId)
                ->fetch();

            $gamePlayer
                ->setAppId($app->getId())
                ->setAppName($app->getKey())
                ->setAppUid($app->getUid())
                ->setAppMode($appMode['mode'])
                ->update();
        }

        #echo $this->time()." запускаем и кешируем приложение\n";
        $app->setClients($clients)
            ->setClient($clientId)
            ->setNumberPlayers($appMode['number'])
            ->setCurrency($appMode['currency'])
            ->setPrice((float)$appMode['price']);

        /* todo $app->create() */
        $this->apps($appName, $app->getUid(), $app);
        $this->runGame($appName, $app->getUid(), 'startAction', $clientId);
    }

    public function runGame($appName, $appId, $action, $playerId = null, $data = null)
    {

        $gamePlayer = new GamePlayer;
        $gamePlayer
            ->setId($playerId)
            ->fetch();

        if ($app = $this->apps($appName, $appId)) {
            $this->_class = $class = '\\' . $appName;
            echo $this->time() . " " . "$appName $appId $action " . (empty($app->_bot) || !in_array($playerId, $app->_bot) ? "игрок №" : 'бот №') . $playerId . ($action != 'startAction' ? ' (текущий №' . implode(',', $app->currentPlayers()) . ")" : '') . " \n";

            if ($_client = $this->clients($playerId)) {
                $player = $_client->Session->get(Player::IDENTITY);
            }

            if (isset($playerId) && $app->getClients($playerId) && !isset($app->getClients($playerId)->bot)
                && (in_array($action, array('replayAction', 'startAction', 'readyAction'))
                    && (!$player->checkBalance($app->getCurrency(), $app->getPrice())))
            ) {
                if ($_client) {
                    $_client->send(json_encode(array('error' => 'INSUFFICIENT_FUNDS')));
                    #echo $this->time() . " " . "Игрок {$from->resourceId} - недостаточно средств для игры\n";
                }
            } else {

                #echo $this->time() . " " . "прошли проверку, устанавливаем клиента \n";
                if (isset($playerId)) {
                    $app->setClient($playerId);
                }

                #echo $this->time() . " " . "пробуем вызвать экшн \n";
                if ($app->getClient() && ($app->isRun() || $action != 'moveAction')) {
                    call_user_func(array($app, $action), $data);

                    #echo $this->time() . " " . "рассылаем игрокам результат обработки \n";
                    $this->sendCallback($app->getResponse(), $app->getCallback());
                }


                if (($action == 'timeoutAction' || $action == 'quitAction') && !array_key_exists($playerId, $app->getClients())) {

                    if ($_client) {
                        $_client->send(json_encode(array('path' => 'quit')));
                        echo $this->time(1) . " отправляем клиенту quit \n";
                    }

                    echo $this->time(1) . " " . $appName . ' ' . $appId . " удаление appId у игрока №{$playerId}\n";

                    $gamePlayer
                        ->setAppUid(null)
                        ->setAppMode(null)
                        ->update();
                }

                $this->checkGame($app);

            }
        } else {

            $gamePlayer->setAppUid(null)
                ->setAppMode(null)
                ->update();

            echo $this->time(0, 'WARNING') . " $appName $appId для запуска не найден\n";
        }
    }

    public function checkGame($app)
    {

        $appName = $app->getKey();
        $appId   = $app->getUid();

        if ($app->isRun() && !$app->isOver()) {
            foreach ($app->_botTimer as $bot => $timer) {
                unset($app->_botTimer[$bot]);
                if (in_array($bot, $app->currentPlayers())) {
                    $this->_loop->addTimer($timer, function () use ($appName, $appId, $bot) {
                        $this->runGame($appName, $appId, 'moveAction', $bot);
                        //echo $this->time() . " " . "$appName {$this->_apps[$appName][$appId]->getUid()} moveAction Бот \n";
                    });
                }
            }
        }

        echo "Приложение сохранено??? {$app->isSaved()} Приложение завершено??? {$app->isOver()} Приложение запущено??? {$app->isRun()}  \n";
        if ($app->isOver() && count($app->getClients()) && !$app->isSaved()) {

            echo $this->time(1) . " {$app->getKey()} {$app->getUid()} приложение завершилось, записываем данные\n";
            /* todo $app->saveResult() */
            $app->_isSaved = $this->saveGame($app);

        }

        if (($app->getOptions('b') && count($app->getClients()) == count($app->_bot)) || (count($app->getClients()) < $app->getOptions('p') && !$app->getOptions('f')) || !count($app->getClients())) {

            if (count($app->getClients())) {
                $gamePlayer = new GamePlayer;
                foreach ($app->getClients() as $id => $client) {
                    $gamePlayer
                        ->setId($id)
                        ->fetch();
                    $gamePlayer
                        ->setAppUid(null)
                        ->setAppMode(null)
                        ->update();
                }
            }

            echo $this->time(1) . " {$app->getKey()} {$app->getUid()} удаляем приложение \n\n";
            /* todo $app->delete() */
            $this->apps('unset', $app->getKey(), $app->getUid());

        } else {

            echo $this->time(1) . " {$app->getKey()} {$app->getUid()} сохраняем приложение \n\n";
            /* todo $app->update() */
            $this->apps($app->getKey(), $app->getUid(), $app);

        }
    }

    public function quitPlayer($playerId)
    {

        /*
         * если есть игрок и у него есть маячок игры, устанавливаем значения переменным, а также удаляем appId
         */

        $gamePlayer = new GamePlayer;
        $gamePlayer
            ->setId($playerId)
            ->fetch();

        if ($appName = $gamePlayer->getApp('Name')) {
            $appMode = $gamePlayer->getApp('Mode');
            $appId   = $gamePlayer->getApp('Uid');
        }

        if (isset($appName)) {

            /*
             * если игрок уходит, но у него есть маячок с игрой, то проверяем его наличие в стеке и удаляем из него
             */

            if ($gamePlayer->getAppMode()) {

                $gamePlayer
                    ->setAppMode(null)
                    ->setAppUid(null)
                    ->update();

                echo $this->time(1) . " " . "$appName Удаление игрока №$playerId из игрового стека ожидающих \n";
            }

            /*
             * если у игрока был appId, то проверяем, есть ли он среди игроков и сдаемся
             */

            if (isset($appId) AND $app = $this->apps($appName, $appId)) {
                if (array_key_exists($playerId, $app->getClients())) {

                    // если есть игра - сдаемся
                    $app->setClient($playerId);

                    if (!$app->isOver() && $app->isRun()) {
                        echo $this->time(1) . " " . "$appName $appId Игра активная - сдаемся\n";
                        $app->surrenderAction();
                        $this->sendCallback($app->getResponse(), $app->getCallback());
                    }

                    $this->runGame($appName, $appId, 'quitAction', $playerId);

                }
            }

        }

    }

    function saveGame($app)
    {

        echo $this->time(1) . " " . $app->getKey() . ' ' . $app->getUid() . " - Сохраняем игру: \n";
        print_r($app->getPlayers());

        return GamesModel::instance()->saveResults($app);

    }

    /***********************
     ** ConnectionInterface
     ***********************/

    public function onOpen(ConnectionInterface $conn)
    {

        if ($player = $conn->Session->get(Player::IDENTITY)) {

            $gamePlayer = new GamePlayer;

            $gamePlayer
                ->setId($player->getId())
                ->fetch();

            $gamePlayer
                ->formatFrom('player', $player)
                ->update();

            $conn->resourceId = $player->getId();
            $this->clients($player->getId(), $conn);

            echo $this->time(0, 'OPEN') . " " . "#{$conn->resourceId} " . $conn->Session->getId() . "\n";

            if ($gamePlayer->getApp('Uid') && $gamePlayer->getApp('Name')) {
                $gameConstructor = new GameConstructorOnline;
                $gameConstructor->setKey($gamePlayer->getApp('Name'))->fetch();
            }

            $balance = $player->getBalance();
            $conn->send(json_encode(
                array('path' => 'update',
                      'res'  => array(
                          'money'  => $balance['Money'],
                          'points' => $balance['Points'],
                          'audio'  => isset($gameConstructor) ? $gameConstructor->getAudio() : null
                      ))));

            if ($gamePlayer->getApp('Uid')) {
                if ($gamePlayer->getApp('Name'))
                    $this->runGame($gamePlayer->getApp('Name'), $gamePlayer->getApp('Uid'), 'startAction', $gamePlayer->getId());
                else {
                    $gamePlayer->setAppUid(null)
                        ->update();
                    echo $this->time(0, 'WARNING') . " у игрока {$player->getId()} отсутствует appName\n";
                }
            }

            /*
            // EMULATION GAME
            if (self::EMULATION) {
                $mode = 'POINT-0-2';
                //$mode=array('currency'=>'POINT','price'=>0,'players'=>2);
                $key = 'Durak';

                // $this->apps('unset',$name);

                $clients                   = array();
                $clients[$player->getId()] = (object)array(
                    'time'   => time(),
                    'id'     => $player->getId(),
                    'avatar' => $player->getAvatar(),
                    'name'   => $player->getNicName());
                $bot                       = (object)SettingsModel::instance()->getSettings('gameBots')->getValue()[array_rand(SettingsModel::instance()->getSettings('gameBots')->getValue())];
                $clients[$bot->id]         = $bot;
                $this->initGame($clients, $key, $mode, OnlineGamesModel::instance()->getGame($key)->initVariation(), $player->getId());

            }
            */

        } else
            echo $this->time(0, 'ERROR') . " onOpen: #{$conn->resourceId} " . $conn->Session->getId() . " без Entity Player \n";

    }

    /**
     * @param ConnectionInterface $from
     * @param string              $msg
     *
     * @throws ModelException
     */

    public function onMessage(ConnectionInterface $from, $msg)
    {

        if ($player = $from->Session->get(Player::IDENTITY))
            if ($player instanceof Player) {


                $gamePlayer = new GamePlayer;

                $gamePlayer
                    ->setId($player->getId())
                    ->fetch();

                $data = json_decode($msg);
                list($type, $appName, $appId) = array_pad(explode("/", $data->path), 3, 0);

                echo "###################################################\n"
                    . $this->time(0, 'MESSAGE') . " #{$from->resourceId}: " . $data->path . (isset($data->data->action) ? " - " . $data->data->action : '') . " \n";

                $this->_class = $class = '\\' . $appName;

                if (isset($data->data))
                    $data = $data->data;

                if ($appName) {

                    if (!class_exists($class)) {
                        $from->send(json_encode(array('error' => 'WRONG_APPLICATION_TYPE')));

                        return false;
                    }

                    $game = new GameConstructorOnline;
                    $game
                        ->setType('online')
                        ->setKey($appName);

                    if ($game->fetch()) {
                        $gamePlayer
                            ->setAppId($game->getId())
                            ->setAppName($game->getKey())
                            ->update();

                    } else {
                        $from->send(json_encode(array('error' => 'WRONG_APPLICATION_TYPE')));
                        return false;
                    }

                } else if (!isset($data->message)) {
                    $from->send(json_encode(array('error' => 'EMPTY_MESSAGE')));
                    return false;
                }

                if ($action = (isset($data->action) ? $data->action . 'Action' : null)) {

                    // $appMode = (isset($data->mode) && $game->checkMode($data->mode) ? $data->mode : self::DEFAULT_MODE);
                    $appMode = array('currency' => null, 'price' => null, 'number' => null, 'variation' => null);
                    list($appMode['currency'], $appMode['price'], $appMode['number'], $appMode['variation'])
                        = array_pad(explode("-", (
                        isset($data->mode) && $game->checkMode($data->mode) ? $data->mode : self::DEFAULT_MODE)
                    ), 4, null);

                    if (!$appMode['number'])
                        $appMode['number'] = 2;

                    if ($appMode['variation'])
                        parse_str($appMode['variation'], $appMode['variation']);

                    $appVariation = $game->initVariation(
                        isset($data->variation) ? (array)$data->variation : ($appMode['variation'] ?: array())
                    );

                    $appMode['variation'] = ($appVariation ? http_build_query($appVariation) : null);
                    $appMode['mode']      = implode('-', $appMode);
                }

                if (!($client = $this->clients($player->getId())) || !($client instanceof ConnectionInterface)) {
                    echo $this->time(0, 'WARNING') . "  соединение #{$player->getId()} {$from->Session->getId()} не найдено в коллекции клиентов \n";
                    $this->clients($player->getId(), $from);
                }

                switch ($type) {
                    case 'app':
                        try {

                            if ($action) {

                                // нет запущенного приложения, пробуем создать новое или просто записаться в очередь
                                if (!$appId) {
                                    #echo $this->time() . " " . "id приложения нет \n";

                                    if ($gamePlayer->getApp('Name') && $gamePlayer->getApp('Uid') && $this->apps($gamePlayer->getApp('Name'), $gamePlayer->getApp('Uid'))) {

                                        echo $this->time(0, 'DANGER') . " {$gamePlayer->getApp('Name')}" . " Игрок {$from->resourceId} отправил запрос без ID при активной игре {$gamePlayer->getApp('Uid')}\n";
                                        $this->runGame($gamePlayer->getApp('Name'), $gamePlayer->getApp('Uid'), 'startAction', $gamePlayer->getId());

                                    } elseif (in_array($action, array('cancelAction', 'quitAction', 'backAction'))) {

                                        if ($gamePlayer->getApp('Mode')) {

                                            echo $this->time(1) . " {$gamePlayer->getApp('Name')}" . " Игрок {$from->resourceId} отказался ждать в стеке {$gamePlayer->getApp('Mode')}\n";
                                            $gamePlayer
                                                ->setAppMode(null)
                                                ->update();
                                        }

                                        $from->send(json_encode(array('path' => $data->action)));

                                    } elseif ($action == 'startAction') {

                                        // list($currency, $price, $number) = explode("-", $appMode);

                                        if ($player->checkBalance($appMode['currency'], $appMode['price'])) {

                                            if ($gamePlayer->getApp('Name') && $gamePlayer->getApp('Uid')
                                                && ($app = $this->apps($gamePlayer->getApp('Name'), $gamePlayer->getApp('Uid'))) && !$app->_isOver
                                            ) {

                                                echo $this->time(0, 'ERROR') . " " . "{$gamePlayer->getApp('Name')} Запуск игроком {$from->resourceId} новой игры при незавершенной {$gamePlayer->getApp('Uid')}\n";

                                                $this->runGame($gamePlayer->getApp('Name'), $gamePlayer->getApp('Uid'), 'startAction', $gamePlayer->getId());

                                                return false;

                                            }

                                            if ($gamePlayer->getApp('Mode')) {

                                                echo $this->time() . " " . "{$gamePlayer->getApp('Name')} Игрок {$from->resourceId} выписался из стека {$gamePlayer->getApp('Mode')}\n";
                                                $gamePlayer
                                                    ->setAppMode('null')
                                                    ->update();
                                            }

                                            if ($game->getOptions('f')) {

                                                $clients[$gamePlayer->getId()] = $gamePlayer->export('player');
                                                $success                       = true;

                                            } else {

                                                echo $this->time() . " " . "$appName Игрок {$from->resourceId} записался в стек " . $appMode['mode'] . "\n";

                                                $gamePlayer
                                                    ->setAppId($game->getId())
                                                    ->setAppName($game->getKey())
                                                    ->setAppMode($appMode['mode'])
                                                    ->update();

                                                $success = false;

                                                $stack = GamePlayersModel::instance()->getStack($appName, $appMode['mode']);
                                                // если насобирали минимальную очередь
                                                if ((count($stack) >= $game->getOptions('s') AND count($stack) >= $appMode['number']) || $game->getOptions('f')) {

                                                    // перемешали игроков
                                                    $keys = array_keys($stack);
                                                    shuffle($keys);

                                                    // начали проверять стек на игру, так как могут быть те, кто не желает играть друг с другом
                                                    foreach ($keys as $key) {
                                                        $clients[$key] = $stack[$key]->export('player');
                                                        // дошли до необходимого числа и прервали
                                                        if (count($clients) == $appMode['number'] || $game->getOptions('f')) {
                                                            $success = true;
                                                            break;
                                                        }
                                                    }
                                                }
                                            }

                                            if ($success) {

                                                $this->initGame($clients, $appName, $appMode, $appVariation, $player->getId());

                                            } else {

                                                $from->send(json_encode(
                                                    array('path' => 'stack',
                                                          'res'  => array(
                                                              'stack' => count($stack),
                                                              'mode'  => $appMode['mode'])
                                                    )));

                                            }
                                        } else {

                                            echo $this->time(1) . " {$gamePlayer->getApp('Name')}" . " У игрока {$gamePlayer->getId()} недостаточно денег {$gamePlayer->getApp('Mode')}\n";
                                            $client->send(json_encode(array('error' => 'INSUFFICIENT_FUNDS')));

                                            return false;
                                        }

                                    } else {
                                        $from->send(json_encode(array('path' => 'quit')));
                                    }

                                    // пробуем загрузить приложение, проверяем наличие
                                    // если нет, сообщаем об ошибке
                                } elseif (!($app = $this->apps($appName, $appId))) {

                                    if ($action == 'replayAction' || $action == 'quitAction') {
                                        echo $this->time() . " " . "id есть, но приложения $appName $appId нет, {$from->resourceId} $action заглушка\n";
                                        $from->send(json_encode(array('path' => 'quit')));

                                    } else {

                                        echo $this->time() . " " . "id есть, но приложения $appName $appId нет, сообщаем об ошибке, удаляем из активных игроков \n";
                                        $this->sendCallback(array($from->resourceId), array(
                                            'action' => 'error',
                                            'error'  => 'APPLICATION_DOESNT_EXISTS',
                                            'appId'  => 0));

                                        $this->quitPlayer($player->getId());
                                        $from->send(json_encode(array('path' => 'quit')));
                                    }

                                    // если есть, загружаем и удаляем игрока из стека
                                } else {

                                    if ($gamePlayer->getApp('Uid') && $gamePlayer->getApp('Uid') !== $appId) {

                                        if ($this->apps($gamePlayer->getApp('Name'), $gamePlayer->getApp('Uid'))) {

                                            echo $this->time(0, 'DANGER') . " {$gamePlayer->getApp('Name')}" . " Игрок {$gamePlayer->getId()} отправил запрос на новую при активной игре {$gamePlayer->getApp('Uid')}\n";

                                            $this->runGame($gamePlayer->getApp('Name'), $gamePlayer->getApp('Uid'),
                                                $gamePlayer->getApp('Uid') == $app->getUid() && in_array($action, array('timeoutAction', 'quitAction')) ? $action : 'startAction',
                                                $gamePlayer->getId());

                                            return false;

                                        } else {

                                            echo $this->time(0, 'DANGER') . " {$gamePlayer->getApp('Name')}" . " Игрок {$gamePlayer->getId()} отправил запрос на новую при отсутствующей старой запущенной игры {$gamePlayer->getApp('Uid')}\n";
                                            $gamePlayer
                                                ->setAppMode(null)
                                                ->setAppUid(null)
                                                ->update();

                                        }

                                    }

                                    if (!in_array($player->getId(), array_keys($app->getClients()))) {

                                        if (!$app->getOptions('v') && count($app->getClients()) == $app->getNumberPlayers()) {

                                            echo $this->time(1) . " {$gamePlayer->getApp('Name')}" . " Игра {$app->getUid()} переполнена при попытке входа {$gamePlayer->getId()}\n";
                                            $client->send(json_encode(array('error' => 'GAME_IS_FULL')));

                                            return false;

                                        } else if ($gamePlayer->getApp('Name') && $gamePlayer->getApp('Mode')) {

                                            $gamePlayer
                                                ->setAppMode(null)
                                                ->update();

                                            echo $this->time(1) . " {$gamePlayer->getApp('Name')}" . " Игрок {$player->getId()} запускает другую игру, пребывая в стеке {$gamePlayer->getApp('Mode')}\n";

                                        } else if (!$player->checkBalance($app->getCurrency(), $app->getPrice())) {

                                            echo $this->time(1) . " {$gamePlayer->getApp('Name')}" . " У игрока {$gamePlayer->getId()} недостаточно денег {$gamePlayer->getApp('Mode')}\n";
                                            $client->send(json_encode(array('error' => 'INSUFFICIENT_FUNDS')));

                                            return false;

                                        }

                                        $gamePlayer
                                            ->setAppId($app->getId())
                                            ->setAppName($app->getKey())
                                            ->setAppMode($app->getMode())
                                            ->setAppUid($app->getUid())
                                            ->update();

                                        $app->addClient(array(
                                            $gamePlayer->getId() => $gamePlayer->export('player')
                                        ));
                                    }

                                    #echo $this->time() . " " . "приложение нашли $appName  $appId\n";
                                    $this->runGame($appName, $appId, $action, $player->getId(), $data);

                                }

                                // если нет действия
                            } else {
                                $from->send(json_encode(array('error' => 'ACTION_EMPTY')));
                            }

                        } catch (Exception $e) {
                            $from->send($e->getMessage());
                        }
                        break;

                    case 'url':
                        break;

                    case 'update':
                        if (isset($game) && $game->getId()) {

                            #echo $this->time() . " " . "Призовой фонд \n";
                            $fund = OnlineGamesModel::instance()->getFund($game->getId());
                            try {
                                $comission = $game->getOptions('r') ? $game->getOptions('r') / 100 : 0;
                            } catch (\EntityException $e) {
                                echo $e->getMessage();
                            }

                            if (!empty($fund)) {
                                foreach ($fund as $currency => &$total) {
                                    $total = ($currency == 'POINT') ? ceil($total * $comission) : ceil($total * $comission * 100) / 100;
                                }
                            }

                            #echo $this->time() . " " . "Рейтинг текущего игрока по этой игре \n";
                            $rating = OnlineGamesModel::instance()->getPlayerRating($game->getId(), $from->resourceId);

                            #echo $this->time() . " " . "Список текущих игр \n";
                            $modes = $game->getModes();

                            $res = array(
                                'key'        => $game->getKey(),
                                'audio'      => array_filter($game->getAudio()),
                                'modes'      => (is_array($modes) && array_walk($modes, function (&$value, $index) {
                                    $value = array_keys($value);
                                }) ? $modes : null),
                                'variations' => $game->getVariations($gamePlayer->getLang()),
                                'fund'       => $fund,
                                'rating'     => $rating,
                                'maxPlayers' => $game->getOptions('p'),
                                'create'     => $game->getOptions('f'),
                            );

                            $from->send(json_encode(array(
                                'path' => $type, // 'update'
                                'res'  => $res)));
                        }
                        break;

                    case 'now':

                        if (isset($game) && $game->getId()) {

                            $games = array();

                            foreach (GameAppsModel::instance()->getList($appName) as $id => $game) {

                                $players = array();

                                foreach ($game->getPlayers() as $player) {
                                    $players[] = $player->name;
                                }

                                $games[] = array(
                                    'id'        => $id,
                                    'mode'      => $game->getApp()->getCurrency() . '-' . $game->getApp()->getPrice() . '-' . $game->getApp()->getNumberPlayers(),
                                    'variation' => $game->getApp()->getVariation(),
                                    'players'   => $players
                                );
                            }


                            foreach (GamePlayersModel::instance()->getStack($appName) as $mode => $clients) {
                                foreach ($clients as $id => $client) {
                                    $games[] = array(
                                        'id'      => 0,
                                        'mode'    => $mode,
                                        'players' => array($client->getName())
                                    );
                                }
                            }

                            $res = array(
                                'key' => $game->getKey(),
                                'now' => $games,
                            );

                            $from->send(json_encode(array(
                                'path' => $type, // 'now'
                                'res'  => $res)));
                        }

                        break;

                    case 'rating':

                        if (isset($game) && $game->getId()) {

                            $from->send(json_encode(array(
                                'path' => $type, // 'update'
                                'res'  => array(
                                    'top' => OnlineGamesModel::instance()->getRating($game->getId()),
                                ))));
                        }

                        break;

                    default:

                        if (isset($data->message)) {

                            if ($data->message == 'stop') {
                                //die;
                            } elseif ($data->message == 'online') {

                                $from->send(json_encode(
                                    array(
                                        'path' => 'appchat',
                                        'res'  => array(
                                            'user'    => 'system',
                                            'message' => 'Игроков онлайн - ' . count($this->clients()))
                                    )
                                ));

                            } elseif ($data->message == 'players') {

                                foreach ($this->clients() as $client)
                                    $names[] = $client->Session->get(Player::IDENTITY)->getNicName();
                                $from->send(json_encode(
                                    array(
                                        'path' => 'appchat',
                                        'res'  => array(
                                            'user'    => 'system',
                                            'message' => 'Игроки онлайн - ' . count($this->clients()) . ': ' . implode(', ', $names))
                                    )
                                ));
                            } elseif ($data->message == 'stats') {
                                $count = 0;
                                foreach ($this->apps() as $apps_class)
                                    $count += count($apps_class);

                                $from->send(json_encode(
                                    array(
                                        'path' => 'appchat',
                                        'res'  => array(
                                            'user'    => 'system',
                                            'message' => array('games' => $count, 'players' => count($this->clients()))
                                        )
                                    )
                                ));


                            } elseif ($data->message == 'games') {
                                $games = '';
                                $count = 0;
                                foreach ($this->apps() as $app_title => $apps_class) {
                                    $games .= $app_title . ' (' . count($apps_class) . '):<br>';
                                    foreach ($apps_class as $app) {
                                        $count++;
                                        $games .= $app->getUid() . ' [' . $app->getCurrency() . '-' . $app->getPrice() . '] ' . (time() - $app->getTime()) . 's ';
                                        $names   = array();
                                        $players = $app->getPlayers();
                                        foreach ($players as $player)
                                            $names[] = $player['pid'];
                                        $games .= (!empty($names) ? implode(':', $names) . '<br>' : '');
                                    }
                                }

                                $from->send(json_encode(
                                    array(
                                        'path' => 'appchat',
                                        'res'  => array(
                                            'user'    => 'system',
                                            'message' => 'Игр онлайн - ' . $count . ($count > 0 ? '<br>' . $games : '')
                                        )
                                    )
                                ));

                            } elseif ($data->message == 'stack') {
                                $stack = '';
                                $count = 0;

                                foreach (GamePlayersModel::instance()->getStack() as $class => $stack_class)
                                    foreach ($stack_class as $mode => $players) {
                                        $count++;
                                        $names = array();
                                        $stack .= $class . ' [' . $mode . '] ';
                                        foreach ($players as $id => $client)
                                            $names[] = $id;
                                        $stack .= (!empty($names) ? implode(',', $names) . '<br>' : '');
                                    }


                                $from->send(json_encode(
                                    array(
                                        'path' => 'appchat',
                                        'res'  => array(
                                            'user'    => 'system',
                                            'message' => 'В стеке - ' . $count . ($count > 0 ? '<br>' . $stack : '')
                                        )
                                    )
                                ));

                            } elseif (isset($data->message)) {

                                foreach ($this->clients() as $client) {
                                    $client->send(json_encode(
                                        array(
                                            'path' => 'appchat',
                                            'res'  => array(
                                                'uid'     => $player->getId(),
                                                'user'    => $player->getNicName(),
                                                'message' => $data->message)
                                        )
                                    ));
                                }
                            }
                        } else {
                            echo $this->time() . ' default ' . (json_encode($msg));
                        }
                        break;
                }
                /* */
            } else
                echo $this->time(0, 'ERROR') . " onMessage: #{$from->resourceId} " . $from->Session->getId() . " без Entity Player \n";
    }

    public function onClose(ConnectionInterface $conn)
    {

        if (!$conn->Session->get(Player::IDENTITY))
            echo $this->time(0, 'ERROR') . " " . "onClose: #{$conn->resourceId} " . $conn->Session->getId() . " без Entity Player \n";

        if ($this->clients($conn->resourceId)) {

            $gamePlayer = new GamePlayer;
            $gamePlayer
                ->setId($conn->resourceId)
                ->fetch();

            if ($gamePlayer->getApp('Name') && $gamePlayer->getApp('Mode') && !$gamePlayer->getApp('Uid')) {

                echo $this->time() . " {$gamePlayer->getApp('Name')}" . "Игрок {$gamePlayer->getId()} удален из стека {$gamePlayer->getApp('Mode')} при выходе\n";
                $gamePlayer
                    ->setAppId(null)
                    ->setAppName(null)
                    ->setAppMode(null)
                    ->update();
            }

            $this->clients('unset', $conn->resourceId);

            echo $this->time(0, 'OUT') . " " . "#{$conn->resourceId} {$conn->Session->getId()}\n";

        } else
            echo $this->time(0, 'ERROR') . " " . "onClose: client #{$conn->resourceId} " . $conn->Session->getId() . " не найден в коллекции \n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {

        if ($e->getCode() == 'HY000' || stristr($e->getMessage(), 'HY000') || stristr($e->getMessage(), 'server has gone away')) {
            echo $this->time(0, 'ERROR') . " " . "{$e->getCode()} {$e->getMessage()} RECONNECT \n";
            try {
                DB::Connect('default', Config::instance()->dbConnectionProperties, true);
            } catch (\Exception $e) {
                echo $this->time(0, 'ERROR') . " " . "{$e->getCode()} {$e->getMessage()} CAN'T RECONNECT \n";
            }

        } else {
            echo $this->time() . " " . "An error has occurred: {$e->getMessage()}\n";
            $conn->close();
        }
    }


    public function sendCallback($clients, $response, $class = null)
    {

        if (!$class)
            $class = $this->_class;

        if (!isset($clients) OR !is_array($clients) OR !count($clients))
            echo $this->time(0, 'WARNING') . "  response пустой\n";

        elseif (!isset($response) || !$response)
            echo $this->time(0, 'WARNING') . "  callback пустой\n";

        // рассылаем игрокам результат обработки
        else
            foreach ($clients as $client) {
                if (!isset($client->bot)) {
                    if (is_numeric($client))
                        $client = (object)['id' => $client];
                    if (($con = $this->clients($client->id)) && ($con instanceof ConnectionInterface)) {

                        #echo $this->time(0,'RESPONSE') . "  #{$client->id} \n";//.json_encode((isset($response[$client->id]) ? $response[$client->id] : $response))." \n";

                        $con->send(
                            json_encode(
                                array(
                                    'path' => 'app' . $class,
                                    'res'  => (isset($response[$client->id]) ? $response[$client->id] : $response)
                                )));
                    } else
                        echo $this->time(0, 'WARNING') . "  соединение #{$client->id} не найдено \n";
                }
            }
    }

    /***********************
     **       Models
     ***********************/

    public function clients($first = null, $second = null)
    {
        $key   = '_clients';
        $array = $second;

        if (Config::instance()->cacheEnabled && 0) {

            if ($first == 'set')
                Cache::init()->set('websocket::' . $key, $second);
            else {
                $array = Cache::init()->get('websocket::' . $key);
                if ($first) {
                    if ($first == 'unset') {
                        unset($array[$second]);
                        Cache::init()->set('websocket::' . $key, $array);
                    } else {
                        if ($second) {
                            $array[$first] = $second;
                            Cache::init()->set('websocket::' . $key, $array);
                        }
                        $array = isset($array[$first]) ? $array[$first] : null;
                    }
                }
            }

        } else {

            if ($first == 'set')
                $this->{$key} = $second;
            else {
                $array = $this->{$key};
                if ($first) {
                    if ($first == 'unset') {
                        unset($this->{$key}[$second]);
                    } else {
                        if ($second) {
                            $this->{$key}[$first] = $second;
                        }
                        $array = isset($this->{$key}[$first]) ? $this->{$key}[$first] : null;
                    }
                }
            }

        }

        return $array;
    }

    public function apps($first = null, $second = null, $third = null)
    {
        $key   = '_apps';
        $array = $third;

        if (0 && Config::instance()->cacheEnabled) {

            if ($first == 'set') {

                Cache::init()->set('websocket::' . $key, $second);

            } else {

                $gameApp = new GameApp();

                $array = Cache::init()->get('websocket::' . $key);
                if ($first) {
                    if ($first == 'unset' && $second) {

                        if (!$third)
                            unset($array[$second]);
                        else {
                            $gameApp->formatFrom('app', $array[$second][$third])->delete();
                            unset($array[$second][$third]);

                        }

                        Cache::init()->set('websocket::' . $key, $array);

                    } else {

                        if ($second && $third) {
                            $array[$first][$second] = $third;
                            $gameApp->formatFrom('app', $third)->update();
                            Cache::init()->set('websocket::' . $key, $array);
                        }

                        $array = $second
                            ? (isset($array[$first]) && isset($array[$first][$second]) ? $array[$first][$second] : null)
                            : (isset($array[$first]) ? $array[$first] : null);
                    }
                }
            }

        } else {

            if ($first == 'set') {
                $this->{$key} = $second;

            } else {

                $gameApp = new GameApp();
                $array   = $this->{$key};
                if ($first) {
                    if ($first == 'unset' && $second) {

                        if (!$third)
                            unset($this->{$key}[$second]);
                        else {
                            $gameApp->setUid($third)->delete();
                            unset($this->{$key}[$second][$third]);
                        }


                    } else {

                        if ($second && $third) {
                            $this->{$key}[$first][$second] = $third;
                            $gameApp->formatFrom('app', $third)->update();
                        }

                        $array = $second
                            ? (isset($this->{$key}[$first]) && isset($this->{$key}[$first][$second]) ? $this->{$key}[$first][$second] : null)
                            : (isset($this->{$key}[$first]) ? $this->{$key}[$first] : null);

                        if ($gameApp->setUid($second)->fetch())
                            $array = $gameApp->getApp();
                        else
                            $array = false;

                    }
                } else {
                    $array = GameAppsModel::instance()->getList();
                }
            }

        }

        return $array;
    }

    private function time($spaces = 0, $str = null)
    {
        return str_repeat(' ', $spaces) . date('H:i:s', time()) . ($str ? ' [' . str_pad($str, 7, '.') . '] ' : '');
    }
}