<?php namespace controllers\production;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Ratchet\Wamp\Exception;
use \Cache, \DB, \Config, \Application;
use \Player, \GamePlayer, \GamePlayersModel, \GameAppsModel;
use \GameConstructorModel, \GameConstructorOnline;


Application::import(PATH_APPLICATION . '/model/Game.php');
Application::import(PATH_APPLICATION . '/model/entities/Player.php');
Application::import(PATH_APPLICATION . '/model/entities/GamePlayer.php');
Application::import(PATH_APPLICATION . '/model/entities/GameConstructorOnline.php');
Application::import(PATH_GAMES . '*');

class WebSocketController implements MessageComponentInterface
{

    const   MIN_WAIT_TIME    = 15;//1;
    const   MAX_WAIT_TIME    = 600;//20;
    const   PERIODIC_TIMER   = 2;//2
    const   TIMEOUT_PLAYER   = 60;//10
    const   TIMEOUT_APP      = 120;//10
    const   CREATE_TIMER     = 60;//5
    const   CONNECTION_TIMER = 1800;
    const   DEFAULT_MODE     = 'POINT-0-2';
    const   EMULATION        = false; //false;

    private $_reload = true;
    private $_class  = null;
    private $_loop   = null;

    private $_clients = array();
    private $_apps = array();

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
        $this->_loop->addPeriodicTimer(self::CREATE_TIMER, function () {
            $this->periodicCreate();
        });

        $this->memcache = new \Memcache;
        $this->memcache->connect('localhost', 11211);
        GameAppsModel::instance()->deleteApps();
        GameAppsModel::instance()->recacheRatingAndFund();
    }

    /***********************
     **      Periodic
     ***********************/

    public function checkConnections()
    {

        echo $this->time()." checkConnections:\n";
        foreach (GamePlayersModel::instance()->getList(array('ping' => time() - self::CONNECTION_TIMER)) as $gamePlayer) {

            echo $this->time() . " " . "#{$gamePlayer->getId()} ping timeout\n";

            /* если отсутствует более положенного времени, сигнализирует об уходе */
            $this->quitPlayer($gamePlayer->getId());
            $gamePlayer->delete();

            if(!$gamePlayer->isBot()) {
                if (($client = $this->clients($gamePlayer->getId())) && $client instanceof ConnectionInterface) {
                    echo $this->time(0, 'CLOSE') . " #{$gamePlayer->getId()} {$client->Session->getId()} \n";
                    $client->close();
                } else {
                    echo $this->time(0, 'ERROR') . " client #{$gamePlayer->getId()} не найден в коллекции\n";
                }
            }
        }
    }

    public function periodicCreate()
    {

        $publishedGames = \GamesPublishedModel::instance()->getList();
        if (isset($publishedGames['OnlineGame'])) {

            $availableBots = GamePlayersModel::instance()->getAvailableBots();

            if (!empty($availableBots)) {
                shuffle($availableBots);

                foreach ($publishedGames['OnlineGame']->getLoadedGames() as $gameConstructor) {

                    if (empty($availableBots)) {
                        #echo "AVAILABLE_BOTS_ENDED\n";
                        break;
                    }

                    if (!$gameConstructor->isEnabled()) {
                        #echo "GAME DISABLED\n";
                        continue;
                    }

                    if (!$gameConstructor->getOptions('b')) {
                        #echo "GAME DOESN'T SUPPORT BOTS\n";
                        continue;
                    }

                    if (GamePlayersModel::instance()->hasStack($gameConstructor->getId())) {
                        #echo "STACK IS NOT EMPTY\n";
                        continue;
                    }

                    if (GameAppsModel::instance()->countWaitingApps($gameConstructor->getId())) {
                        #echo "WAITING APPS ARE EXISTS\n";
                        continue;
                    }

                    if ($gameConstructor->getOptions('f')) {
                        #echo "GAME HAVEN'T STACK, GAME RUN IMMEDIATELY \n";
                        continue;
                    }

                    #echo "periodicCreate: {$gameConstructor->getKey()}\n";

                    if (isset($gameConstructor->getModes()['POINT'])) {
                        foreach ($gameConstructor->getModes()['POINT'] as $mode => $botHit) {
                            if ($mode > 0) {

                                $gameConstructorOnline = new GameConstructorOnline();
                                $gameConstructorOnline
                                    ->setType('online')
                                    ->setId($gameConstructor->getId())
                                    ->fetch();

                                $bot        = (object) array_pop($availableBots);
                                $gamePlayer = new GamePlayer();
                                $gamePlayer
                                    ->formatFrom('bot', $bot)
                                    ->update();

                                if ($gameConstructor->getOptions('f')) {

                                    $appMode = array(
                                        'currency' => 'POINT',
                                        'price'    => $mode,
                                        'number'   => 2
                                    );

                                    $appVariation    = $gameConstructorOnline->initVariation();
                                    $appMode['mode'] = implode('-', $appMode) . '-' . http_build_query($appVariation);

                                    $clients                       = array();
                                    $clients[$gamePlayer->getId()] = $gamePlayer->export('player');

                                    $this->initGame($clients, $gameConstructor->getKey(), $appMode, $appVariation);

                                } else {

                                    $gamePlayer
                                        ->setAppName($gameConstructor->getKey())
                                        ->setAppId($gameConstructor->getId())
                                        ->setAppMode(
                                            implode('-', array_filter(array(
                                                'currency'  => 'POINT',
                                                'price'     => $mode,
                                                'number'    => 2,
                                                'variation' => http_build_query($gameConstructorOnline->initVariation())
                                            ))))
                                        ->update();
                                }

                                break;
                            }
                        }
                    }
                }

            } else {
                #echo "EMPTY_AVAILABLE_BOTS\n";
            }

        } else {
            #echo "EMPTY_PUBLISHED_GAMES\n";
        }

        return true;
    }

    public function periodicTimer()
    {
        $this->periodicStack();
        $this->periodicApps();
        $this->periodicReload();
    }

    public function periodicStack()
    {

        if (GamePlayersModel::instance()->hasStack()) {

            $gameConstructor = new GameConstructorOnline();

            foreach (GamePlayersModel::instance()->getStack() as $key => $modes) {

                $gameConstructor
                    ->setType('online')
                    ->setId(null)
                    ->setKey($key)
                    ->fetch();

                foreach ($modes as $mode => $stacks) {
                    foreach ($stacks as $id => $gamePlayer) {

                        echo "periodicStack:" . $id . "\n";

                        if ($this->_reload)
                            $this->_reload = false;

                        if ($gamePlayer->getPing() + self::MAX_WAIT_TIME < time()) {

                           echo $this->time(0) . " $key " . " Игрок {$gamePlayer->getId()} удален из стека {$gamePlayer->getApp('Mode')} по таймауту\n";

                            $gamePlayer
                                ->setAppMode(null)
                                ->update();

                            if ($_client = $this->clients($gamePlayer->getId()))
                                $_client->send(json_encode(array('path' => 'cancel')));

                        } elseif (!$gamePlayer->isBot() && $gamePlayer->getPing() + self::MIN_WAIT_TIME < time() && $gameConstructor->getOptions('b')) {

                            $availableBots = GamePlayersModel::instance()->getAvailableBots();

                            if (count($availableBots) >= $gameConstructor->getOptions('p') - 1) {

                                $clients      = array();
                                $clients[$id] = $gamePlayer->export('player');

                                while (count($clients) < $gameConstructor->getOptions('p')) {

                                    do {
                                        $bot = (object)$availableBots[array_rand($availableBots)];
                                    } while (array_key_exists($bot->id, $clients));

                                    $gamePlayer = new GamePlayer();

                                    $clients[$bot->id] = $gamePlayer
                                        ->formatFrom('bot', $bot)
                                        ->update()
                                        ->export('player');
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
                            }
                        }
                    }
                }
            }
        }
    }

    public function periodicApps()
    {

        #echo 'periodicAppName: ' . $appName . "\n";
        foreach ($this->apps() as $uid => $app) {

            #echo 'periodicAppUid: ' . $app->getUid() . "\n";

            if ($this->_reload)
                $this->_reload = false;

            if ($app->isOver() && !empty($app->_bot)) {
                foreach ($app->_bot as $bot) {
                    if ($app->currentPlayer()['timeout'] - $app->getOptions('t') + 10 < time()) {
                        #echo " -- таймер на выход после 10 сек \n";
                        $this->runGame($app->getKey(), $app->getUid(), 'quitAction', $bot);
                    } elseif (!in_array($bot, $app->_botReplay) && $app->currentPlayer()['timeout'] + rand(2, 4) - $app->getOptions('t') < time()) {
                        if (rand(1, 5) == 1) {
                            #echo " -- таймер на случайный выход\n";
                            $this->runGame($app->getKey(), $app->getUid(), 'quitAction', $bot);
                        } else {
                            #echo " -- таймер на повтор \n";
                            $this->runGame($app->getKey(), $app->getUid(), 'replayAction', $bot);
                        }
                    }
                }

            } elseif (!$app->isOver() && $app->isRun()
                && $app->getTime() + $app->getOptions('t') < time()
                && $app->currentPlayer()['timeout'] < time()
                && $app->currentPlayer()['pid']
            ) {

                echo " -- таймер на таймаут \n";
                $this->runGame($app->getKey(), $app->getUid(), 'timeoutAction', $app->currentPlayer()['pid']);

            } elseif (!$app->isRun()
                && ((isset($app->currentPlayer()['timeout']) && $app->currentPlayer()['timeout'] + self::TIMEOUT_PLAYER < time())
                    || (!isset($app->currentPlayer()['timeout']) && $app->getTime() + self::TIMEOUT_APP < time()))
            ) {

                foreach ($app->getPlayers() as $player) {
                    echo " -- таймер на выход игрока №{$player['pid']}\n";
                    $this->quitPlayer($player['pid']);
                }

            } elseif (!$app->isOver() && !$app->isRun() && $app->getOptions('b')
                && $app->getNumberPlayers() > count($app->getClients())
                && count($app->getClients()) != count($app->_bot)
                && $app->getTime() + self::MIN_WAIT_TIME < time()
            ) {

                $availableBots = GamePlayersModel::instance()->getAvailableBots();
                if (count($availableBots)) {

                    $bot = (object)$availableBots[array_rand($availableBots)];
                    $gameBot = new GamePlayer();
                    $gameBot
                        ->formatFrom('bot', $bot)
                        ->setAppId($app->getId())
                        ->setAppUid($app->getUid())
                        ->setAppName($app->getKey())
                        ->setAppMode($app->getMode())
                        ->update();

                    // $bot->time = time();
                    $app->addClients(array(
                        $gameBot->getId() => $gameBot->export()
                    ));

                    $app->update();

                    $this->runGame($app->getKey(), $app->getUid(), 'startAction', $bot->id);
                    #echo " -- таймер на добавление бота в игру \n";
                }
            }
        }

    }

    public function periodicReload()
    {

        if ($this->_reload) {
            $seo = \SEOModel::instance()->getSEOSettings();
            if (isset($seo['WebSocketReload']) && $seo['WebSocketReload']) {
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

    public function initGame($clients, $appName, $appMode, $appVariation, $clientId = null)
    {
        $this->_class = $class = '\\' . $appName;

        /* todo getOnlineGame by id */
        $app = new $class(
            GameConstructorModel::instance()->getOnlineGame($appName),
            $appVariation
        );

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

        $app->create();
        $this->apps($app->getUid(), $app);

        $this->runGame($app->getKey(), $app->getUid(), 'startAction', $clientId);
    }

    public function runGame($appName, $appUid, $action, $playerId = null, $data = null)
    {
        if ($app = $this->apps($appUid)) {

            $this->_class = $class = '\\' . $appName;
            echo $this->time() . " " . "$appName $appUid $action " . (empty($app->_bot) || !in_array($playerId, $app->_bot) ? "игрок №" : 'бот №') . $playerId . ($action != 'startAction' ? ' (текущий №' . implode(',', $app->currentPlayers()) . ")" : '') . " \n";

            if (isset($playerId)) {

                $_client = $this->clients($playerId);

                if (in_array($action, array('replayAction', 'startAction', 'readyAction')) && $app->getClients($playerId) && !isset($app->getClients($playerId)->bot)) {
                    if ($_client) {
                        $_player = $_client->Session->get(Player::IDENTITY);

                        if ($_player && $_player instanceof Player) {

                            if (!$_player->checkBalance($app->getCurrency(), $app->getPrice())) {
                                #echo $this->time() . " " . "Игрок {$from->resourceId} - недостаточно средств для игры\n";
                                return $_client->send(json_encode(array('error' => 'INSUFFICIENT_FUNDS')));
                            }

                        } else {

                            echo $this->time(0, 'ERROR') . " runGame: игрок #{$playerId} без Entity Player \n";
                            return $_client->send(json_encode(array('error' => 'PLAYER_NOT_FOUND')));
                        }

                    } else {

                        echo $this->time(0, 'ERROR') . " runGame: Client #{$playerId} не найден в коллекции\n";
                        return false;
                    }

                }

                #echo $this->time() . " " . "прошли проверку, устанавливаем клиента \n";
                $app->setClient($playerId);
            }

            #echo $this->time() . " " . "пробуем вызвать экшн \n";
            if ($app->getClient() && ($app->isRun() || $action != 'moveAction')) {

                if (method_exists($app, $action) && is_callable(array($app, $action))) {
                    call_user_func(array($app, $action), $data);
                } else if ($_client) {
                    return $_client->send(json_encode(array('error' => 'WRONG_ACTION')));
                }

                #echo $this->time() . " " . "рассылаем игрокам результат обработки \n";
                if (count($app->getResponse()))
                    $this->sendCallback($app->getResponse(), $app->getCallback());
            }


            if ($playerId && ($action == 'timeoutAction' || $action == 'quitAction') && !array_key_exists($playerId, $app->getClients())) {

                if ($_client) {
                    echo $this->time(1) . " отправляем клиенту quit \n";
                    $_client->send(json_encode(array('path' => 'quit')));
                }

                echo $this->time(1) . " " . $appName . ' ' . $appUid . " удаление appId у игрока №{$playerId}\n";

                $gamePlayer = new GamePlayer;
                $gamePlayer
                    ->setId($playerId)
                    ->fetch();

                $gamePlayer
                    ->setAppUid(null)
                    ->setAppMode(null)
                    ->update();
            }

            $this->checkGame($app);

        } else {

            $gamePlayer = new GamePlayer;
            $gamePlayer
                ->setId($playerId)
                ->fetch();

            $gamePlayer->setAppUid(null)
                ->setAppMode(null)
                ->update();

            echo $this->time(0, 'WARNING') . " $appName $appUid для запуска не найден\n";
        }
    }

    public function checkGame($app)
    {

        if ($app->isRun() && !$app->isOver()) {

            $appName = $app->getKey();
            $appUid  = $app->getUid();

            foreach ($app->_botTimer as $bot => $timer) {
                unset($app->_botTimer[$bot]);
                if (in_array($bot, $app->currentPlayers())) {
                    $this->_loop->addTimer($timer, function () use ($appName, $appUid, $bot) {
                        $this->runGame($appName, $appUid, 'moveAction', $bot);
                        //echo $this->time() . " " . "$appName {$this->_apps[$appName][$appUid]->getUid()} moveAction Бот \n";
                    });
                }
            }
        }

        echo "Приложение сохранено??? {$app->isSaved()} Приложение завершено??? {$app->isOver()} Приложение запущено??? {$app->isRun()}  \n";

        if ($app->isOver() && count($app->getClients()) && !$app->isSaved()) {

            echo $this->time(1) . " {$app->getKey()} {$app->getUid()} приложение завершилось, записываем данные\n";

            $playersBalance = $app->saveResults();
            if(is_array($playersBalance) && !empty($playersBalance)){
                foreach($playersBalance as $playerId => $balance){
                    if($_client = $this->clients($playerId)) {
                        $_client->send(json_encode(
                            array('path' => 'update',
                                'res' => array(
                                    'money' => $balance['Money'],
                                    'points' => $balance['Points']
                                ))));
                    }
                }
            }


        }

        if ((!$app->isOver() && !count($app->getClients())) ||
            ($app->isOver() &&
                (($app->getOptions('b') && count($app->getClients()) == count($app->_bot))
                    || (count($app->getClients()) < $app->getOptions('p') && !$app->getOptions('f')) || !count($app->getClients())
            ))
        ) {

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

            $app->delete();
            $this->apps($app->getUid(), 'unset');

        } else {

            echo $this->time(1) . " {$app->getKey()} {$app->getUid()} сохраняем приложение \n\n";

            $app->update();

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
            $appUid   = $gamePlayer->getApp('Uid');
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

            if (isset($appUid) AND ($app = $this->apps($appUid))) {
                if (array_key_exists($playerId, $app->getClients())) {

                    // если есть игра - сдаемся
                    $app->setClient($playerId);

                    if (!$app->isOver() && $app->isRun()) {

                        echo $this->time(1) . " " . "$appName $appUid Игра активная - сдаемся\n";
                        $app->surrenderAction();

                        if(count($app->getResponse()))
                            $this->sendCallback($app->getResponse(), $app->getCallback());

                        $this->checkGame($app);
                    }

                    $this->runGame($appName, $appUid, 'quitAction', $playerId);

                }
            }

        }

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

            if(!$this->clients($player->getId(), $conn))
                return;

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
                          'audio'  => isset($gameConstructor) ? $gameConstructor->getAudio() : null,
                          'key'    => isset($gameConstructor) ? $gameConstructor->getKey() : null
                      ))));

            if ($gamePlayer->getApp('Uid')) {
                if ($gamePlayer->getApp('Name'))
                    $this->runGame(
                        $gamePlayer->getApp('Name'),
                        $gamePlayer->getApp('Uid'),
                        'startAction',
                        $gamePlayer->getId()
                    );
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

                // GameAppsModel::instance()->getApp('unset',$name);

                $clients                   = array();
                $clients[$player->getId()] = (object)array(
                    'time'   => time(),
                    'id'     => $player->getId(),
                    'avatar' => $player->getAvatar(),
                    'name'   => $player->getNicname());
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
                list($type, $appName, $appUid) = array_pad(explode("/", $data->path), 3, 0);

                echo "###################################################\n"
                    . $this->time(0, 'MESSAGE') . " #{$from->resourceId}: " . $data->path . (isset($data->data->action) ? " - " . $data->data->action : '') . " \n";


                if (isset($data->data))
                    $data = $data->data;

                if ($appName) {

                    $game = new GameConstructorOnline;
                    $game
                        ->setType('online');

                    if(is_numeric($appName)) {
                        $game->setId($appName);
                    } else {
                        $game->setKey($appName);
                    }

                    try {

                        $game->fetch();
                        $gamePlayer
                            ->setAppId($game->getId())
                            ->setAppName($game->getKey())
                            ->update();
                        $appName = $game->getKey();
                        $appId = $game->getId();

                    } catch(\EntityException $e){
                        echo $this->time(0, 'ERROR') . "WRONG_APPLICATION_NAME $appName\n";
                        $from->send(json_encode(array('error' => 'WRONG_APPLICATION_NAME')));
                        return false;
                    }

                } else if (!isset($data->message)) {
                    echo $this->time(0, 'ERROR') . "EMPTY_MESSAGE $msg\n";
                    $from->send(json_encode(array('error' => 'EMPTY_MESSAGE')));
                    return false;
                }

                $this->_class = $class = '\\' . $appName;


                if (!($client = $this->clients($player->getId())) || !($client instanceof ConnectionInterface)) {
                    echo $this->time(0, 'WARNING') . "  соединение #{$player->getId()} {$from->Session->getId()} не найдено в коллекции клиентов \n";
                    if(!$this->clients($player->getId(), $from))
                        return;
                }

                switch ($type) {
                    case 'app':
                        try {

                            if (!class_exists($class)) {
                                $from->send(json_encode(array('error' => 'CLASS_NOT_EXISTS')));
                                return false;
                            }

                            if ($action = (isset($data->action) ? $data->action . 'Action' : null)) {

                                // $appMode = (isset($data->mode) && $game->checkMode($data->mode) ? $data->mode : self::DEFAULT_MODE);
                                if(isset($data->players))
                                    $data->mode .= '-'.$data->players;

                                $appMode = array('currency' => null, 'price' => null, 'number' => null, 'variation' => null);
                                list($appMode['currency'], $appMode['price'], $appMode['number'], $appMode['variation'])
                                    = array_pad(explode("-", (isset($data->mode) && $game->checkMode($data->mode) ? $data->mode : self::DEFAULT_MODE)), 4, null);

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

                            if ($action) {

                                // если любое действие, которое не совпадает с Uid запущенной у игрока игры
                                if ($gamePlayer->getApp('Uid') && $gamePlayer->getApp('Uid') !== $appUid) {

                                    if ($this->apps($gamePlayer->getApp('Uid'))) {

                                        echo $this->time(0, 'DANGER') . " {$gamePlayer->getApp('Name')}" . " Игрок {$gamePlayer->getId()} отправил запрос на новую при активной игре {$gamePlayer->getApp('Uid')}\n";

                                        $this->runGame(
                                            $gamePlayer->getApp('Name'),
                                            $gamePlayer->getApp('Uid'),
                                            in_array($action, array('timeoutAction', 'quitAction')) ? $action : 'startAction',
                                            $gamePlayer->getId()
                                        );

                                        return false;

                                    } else {

                                        echo $this->time(0, 'DANGER') . " {$gamePlayer->getApp('Name')}" . " Игрок {$gamePlayer->getId()} отправил запрос на новую при отсутствующей старой запущенной игры {$gamePlayer->getApp('Uid')}\n";
                                        $gamePlayer
                                            ->setAppMode(null)
                                            ->setAppUid(null)
                                            ->update();

                                    }

                                }


                                // нет запущенного приложения, пробуем создать новое или просто записаться в очередь
                                if (!$appUid) {
                                    #echo $this->time() . " " . "id приложения нет \n";

                                    if ($gamePlayer->getApp('Name') && $gamePlayer->getApp('Uid') && $this->apps($gamePlayer->getApp('Uid'))) {

                                        echo $this->time(0, 'DANGER') . " {$gamePlayer->getApp('Name')}" . " Игрок {$from->resourceId} отправил запрос без ID при активной игре {$gamePlayer->getApp('Uid')}\n";

                                        $this->runGame(
                                            $gamePlayer->getApp('Name'),
                                            $gamePlayer->getApp('Uid'),
                                            'startAction',
                                            $gamePlayer->getId()
                                        );

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
                                                && ($app = $this->apps($gamePlayer->getApp('Uid'))) && !$app->isOver()
                                            ) {

                                                echo $this->time(0, 'ERROR') . " " . "{$gamePlayer->getApp('Name')} Запуск игроком {$from->resourceId} новой игры при незавершенной {$gamePlayer->getApp('Uid')}\n";

                                                $this->runGame(
                                                    $gamePlayer->getApp('Name'),
                                                    $gamePlayer->getApp('Uid'),
                                                    'startAction',
                                                    $gamePlayer->getId()
                                                );

                                                return false;

                                            }

                                            if ($gamePlayer->getApp('Mode')) {

                                                echo $this->time() . " " . "{$gamePlayer->getApp('Name')} Игрок {$from->resourceId} выписался из стека {$gamePlayer->getApp('Mode')}\n";
                                                $gamePlayer
                                                    ->setAppMode('null')
                                                    ->update();
                                            }

                                            $clients = array();
                                            $success = false;

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

                                                $stack = GamePlayersModel::instance()->getStack($appName, $appMode['mode']);

                                                // если насобирали минимальную очередь
                                                if (true || (count($stack) >= $game->getOptions('s') AND count($stack) >= $appMode['number']) || $game->getOptions('f')) {

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

                                                foreach($clients as &$client)
                                                    unset($client->bot, $client->admin);

                                                /* todo $this->sendCallback(array_keys($clients), $response) */

                                                $from->send(json_encode(
                                                    array('path' => 'stack',
                                                          'res'  => array(
                                                              'stack' => count($stack),
                                                              'mode'  => $appMode['mode'],
                                                              'action' => 'stack',
                                                              'app'   => array(
                                                                  'uid' => 0,
                                                                  'key' => $appName,
                                                                  'id'  => $appId
                                                              ),
                                                              'playerNumbers' => $appMode['number'],
                                                              'players' => $clients
                                                          ))));

                                            }

                                        } else {

                                            echo $this->time(1) . " {$gamePlayer->getApp('Name')}" . " У игрока {$gamePlayer->getId()} недостаточно денег {$gamePlayer->getApp('Mode')}\n";
                                            $from->send(json_encode(array('error' => 'INSUFFICIENT_FUNDS')));

                                            return false;
                                        }

                                    } else {
                                        echo "выходим\r\n";
                                        $from->send(json_encode(array('path' => 'quit')));
                                    }

                                    // пробуем загрузить приложение, проверяем наличие
                                    // если нет, сообщаем об ошибке

                                } elseif (!($app = $this->apps($appUid))) {

                                    if ($action == 'replayAction' || $action == 'quitAction') {
                                        echo $this->time() . " " . "id есть, но приложения $appName $appUid нет, {$from->resourceId} $action заглушка\n";
                                        $from->send(json_encode(array('path' => 'quit')));

                                    } else {

                                        echo $this->time() . " " . "id есть, но приложения $appName $appUid нет, сообщаем об ошибке, удаляем из активных игроков \n";
                                        $this->sendCallback(array($from->resourceId), array(
                                            'action' => 'error',
                                            'error'  => 'APPLICATION_DOESNT_EXISTS',
                                            'appId'  => 0));

                                        $this->quitPlayer($player->getId());
                                        $from->send(json_encode(array('path' => 'quit')));
                                    }

                                    // если есть, загружаем и удаляем игрока из стека
                                } else {

                                    if (!in_array($player->getId(), array_keys($app->getClients()))) {

                                        if (!$app->getOptions('v') && count($app->getClients()) == $app->getNumberPlayers()) {

                                            echo $this->time(1) . " {$gamePlayer->getApp('Name')}" . " Игра {$app->getUid()} переполнена при попытке входа {$gamePlayer->getId()}\n";
                                            $from->send(json_encode(array('error' => 'GAME_IS_FULL')));

                                            return false;

                                        } else if ($gamePlayer->getApp('Name') && $gamePlayer->getApp('Mode')) {

                                            $gamePlayer
                                                ->setAppMode(null)
                                                ->update();

                                            echo $this->time(1) . " {$gamePlayer->getApp('Name')}" . " Игрок {$player->getId()} запускает другую игру, пребывая в стеке {$gamePlayer->getApp('Mode')}\n";

                                        } else if (!$player->checkBalance($app->getCurrency(), $app->getPrice())) {

                                            echo $this->time(1) . " {$gamePlayer->getApp('Name')}" . " У игрока {$gamePlayer->getId()} недостаточно денег {$gamePlayer->getApp('Mode')}\n";
                                            $from->send(json_encode(array('error' => 'INSUFFICIENT_FUNDS')));

                                            return false;

                                        }

                                        $gamePlayer
                                            ->setAppId($app->getId())
                                            ->setAppName($app->getKey())
                                            ->setAppMode($app->getMode())
                                            ->setAppUid($app->getUid())
                                            ->update();

                                        $app->addClients(array(
                                            $gamePlayer->getId() => $gamePlayer->export('player')
                                        ));

                                        $app->update();
                                    }

                                    #echo $this->time() . " " . "приложение нашли $appName  $appUid\n";
                                    $this->runGame(
                                        $appName,
                                        $appUid,
                                        $action,
                                        $player->getId(),
                                        $data
                                    );

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
                            $fund = GameAppsModel::instance()->getFund($game->getId());
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
                            $rating = GameAppsModel::instance()->getPlayerRating($game->getId(), $from->resourceId);

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

                            foreach (GameAppsModel::instance()->getList($game->getId()) as $uid => $game) {
                                $games[] = array(
                                    'id'        => $uid,
                                    'mode'      => $game->getMode(),
                                    'variation' => $game->getVariation(),
                                    'players'   => $game->getClients()
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
                                    'top' => GameAppsModel::instance()->getRating($game->getId()),
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
                                    $names[] = $client->Session->get(Player::IDENTITY)->getNicname();

                                $from->send(json_encode(
                                    array(
                                        'path' => 'appchat',
                                        'res'  => array(
                                            'user'    => 'system',
                                            'message' => 'Игроки онлайн - ' . count($this->clients()) . ': ' . implode(', ', $names))
                                    )
                                ));
                            } elseif ($data->message == 'stats') {

                                $from->send(json_encode(
                                    array(
                                        'path' => 'appchat',
                                        'res'  => array(
                                            'user'    => 'system',
                                            'message' => array('games' => count($this->apps()), 'players' => count($this->clients()))
                                        )
                                    )
                                ));


                            } elseif ($data->message == 'games') {

                                $games = '';
                                $count = 0;

                                foreach (GameAppsModel::instance()->getList() as $app_title => $apps_class) {
                                    if(is_numeric($app_title))
                                        continue;
                                    $games .= $app_title . ' (' . count($apps_class) . '):<br>';
                                    $count += count($apps_class);
                                    foreach ($apps_class as $app) {
                                        $games .= $app->getUid() . ' [' . $app->getMode() . '] ' . (time() - $app->getPing()) . 's ';
                                        $games .= implode(':', $app->getClients()) . '<br>';
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
                                                'user'    => $player->getNicname(),
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
                        $array = true;
                    } elseif(is_numeric($first) && is_array($this->{$key})) {
                        if ($second instanceof ConnectionInterface) {
                            $this->{$key}[$first] = $second;
                        }
                        $array =  isset($this->{$key}[$first]) ? $this->{$key}[$first] : null;
                    } else {
                        $array = false;
                    }
                }
            }

        }

        return $array;
    }

    public function apps($uid = null, $app = null)
    {
        $key   = '_apps';

        if (!$uid) {
            return $this->{$key};

        } else if ($app) {

            if ($app == 'unset') {
                unset($this->{$key}[$uid]);

            } else {
                $this->{$key}[$uid] = $app;
            }

        } else {
            $app = isset($this->{$key}[$uid])?$this->{$key}[$uid]:null;
        }

        return $app;
    }

    private function time($spaces = 0, $str = null)
    {
        return str_repeat(' ', $spaces) . date('H:i:s', time()) . ($str ? ' [' . str_pad($str, 7, '.') . '] ' : '');
    }
}