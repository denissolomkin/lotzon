<?php

use Symfony\Component\HttpFoundation\Session\Session;

Application::import(PATH_APPLICATION . 'model/Entity.php');
Application::import(PATH_APPLICATION . 'model/entities/Transaction.php');
Application::import(PATH_APPLICATION . 'model/entities/LotterySettings.php');

class Player extends Entity
{
    const IDENTITY = "player_session";
    const AUTOLOGIN_COOKIE = "autologin";
    const AUTOLOGIN_HASH_COOKIE   = "autologinHash";
    const PLAYERID_COOKIE   = "_ga_ca";
    // 3 months
    const AUTOLOGIN_COOKIE_TTL = 7776000;

    const AVATAR_WIDTH  = 160;
    const AVATAR_HEIGHT = 160;

    static $MASK = array(
        'dates'=>array('Moment','QuickGame','ChanceGame','AdBlockLast','AdBlocked','WSocket','TeaserClick','Ping','Login','Notice','Registration'),
        'stats'=>array('WhoMore','SeaBattle','Notice','Note','AdBlock','Log','Ip','MyReferal','Referal','MyInviter','Inviter','ShopOrder','MoneyOrder','Review','Message','CookieId','Mult'),
        'counters'=>array('CaptchaCount','CaptchaTime'),
        'privacy'=>array('Name','Surname','Gender','Birthday','Age','Zip','Address','Message') // list of variables, which can be modify by player
    );

    protected $_id         = 0;
    protected $_email      = '';
    protected $_password   = '';
    protected $_salt       = '';
    protected $_hash       = '';

    protected $_socialId     = 0;
    protected $_socialEmail  = '';
    protected $_socialName   = '';
    protected $_socialEnable = 1;

    protected $_nicname    = '';
    protected $_name       = '';
    protected $_surname    = '';
    protected $_secondName = '';
    protected $_avatar     = '';
    protected $_birthday   = null;
    protected $_gender     = null;

    protected $_city    = '';
    protected $_zip     = '00000';
    protected $_address = '';

    protected $_agent      = '';
    protected $_referer    = '';

    protected $_phone       = null;
    protected $_yandexMoney = null;
    protected $_qiwi        = null;
    protected $_webMoney    = null;

    protected $_favoriteCombination = array();
    protected $_visible             = false;
    protected $_valid           = 0;
    protected $_complete        = 1;
    protected $_ban             = false;
    protected $_bot             = false;
    protected $_admin           = false;
    protected $_utc             = null;
    protected $_captchaTime     = 0;
    protected $_captchaCount    = 0;

    protected $_privacy        = array();
    protected $_dates          = array();
    protected $_counters       = array();
    protected $_country        = '';
    protected $_lang           = '';

    protected $_generatedPassword = '';

    protected $_points      = 0;
    protected $_money       = 0;
    protected $_gamesPlayed = 0;

    protected $_ip = '';
    protected $_lastIp = '';
    protected $_cookieId = 0;

    protected $_inviterId = 0;
    protected $_referalId = 0;
    protected $_referalPaid = 0;

    protected $_referralsProfit = 0;
    protected $_referralPay     = 0;

    protected $_goldTicket = 0;

    protected $_newsSubscribe = 1;

    protected $_additionalData = array();

    // filled only when list of players fetched
    protected $_ticketsFilled = 0;
    protected $_stats = array();

    protected $_friend = null;

    public function init()
    {
        $this->setModelClass('PlayersModel');
    }

    public function getNicName()
    {
        return $this->_nicname;
    }

    public function setLang($lang)
    {

        $this->_lang =
            \LanguagesModel::instance()->isLang($lang)
                ? $lang
                : \CountriesModel::instance()->defaultLang();

        return $this;
    }

    public function getFavoriteCombination()
    {
        if (!is_array($this->_favoriteCombination)) {
            return array_fill(1,\LotterySettings::REQUIRED_BALLS, null);
        }
        return array_pad($this->_favoriteCombination, \LotterySettings::REQUIRED_BALLS, null);
    }

    public function getInvitesCount()
    {
        $model = $this->getModelClass();

        try {
            $count = $model::instance()->getInvitesCount($this);
        } catch (ModelException $e) {
            throw new EntityException('INTERNAL_ERROR', 500);
        }

        return $count;
    }

    public function getAvailableInvitesCount()
    {
        return SettingsModel::instance()->getSettings('counters')->getValue('INVITES_PER_WEEK') - $this->getInvitesCount();
    }

    public function checkDate($key)
    {
        $model = $this->getModelClass();
        $check = false;

        try {
            $check = $model::instance()->checkDate($key, $this);
        } catch (ModelException $e) {
            throw new EntityException('INTERNAL_ERROR', 500);
        }

        if($check)
            $this->setDates(time(), $key);

        return $check;
    }

    public function getAge() {

        if($this->getBirthday()) {
            $birthday = new DateTime();
            $birthday->setTimestamp($this->getBirthday());
            $now = new DateTime();
            $age = $now->diff($birthday);
            return $age->y < 100 ? $age->y : null;
        } else
            return null;

    }

    public function setLastIP($ip)
    {
        if(!$this->getIp()){
            $this->setIp($ip);
        } elseif($ip!=$this->getIp()){
            $this->_lastIp = $ip;
        }
        return $this;
    }

    public function updateInvite()
    {
        $model = $this->getModelClass();

        try {
            $model::instance()->getProcessor()->updateInvite($this);
        } catch (ModelException $e) {
            throw new EntityException('INTERNAL_ERROR', 500);
        }

        return $this;
    }

    public function markReferalPaid()
    {
        $model = $this->getModelClass();

        try {
            $model::instance()->getProcessor()->markReferalPaid($this);
        } catch (ModelException $e) {
            throw new EntityException('INTERNAL_ERROR', 500);
        }

        return $this;
    }

    public function writeLogin()
    {
        $model = $this->getModelClass();

        try {
            $model::instance()->getProcessor()->writeLogin($this);
        } catch (ModelException $e) {
            throw new EntityException('INTERNAL_ERROR', 500);
        }

        return $this;
    }

    public function writeLog($options=array())
    {
        $model = $this->getModelClass();

        try {
            $model::instance()->getProcessor()->writeLog($this, $options);
        } catch (ModelException $e) {
            throw new EntityException('INTERNAL_ERROR', 500);
        }

        return $this;
    }

    public function reportTrouble($trouble)
    {
        $model = $this->getModelClass();

        try {
            $model::instance()->getProcessor()->reportTrouble($this, $trouble);
        } catch (ModelException $e) {
            throw new EntityException('INTERNAL_ERROR', 500);
        }

        return $this;
    }

    public function setAdditionalData($additionalData=null)
    {

        if(is_array($additionalData)){
            $additionalData=array_merge($this->getAdditionalData(),$additionalData);
            foreach($additionalData as $provider => $info)
                $this->_additionalData[$provider] = $info;
        }

        return $this;
    }

    public function getSocial()
    {

        $socials = array(
            'disabled'  => false,
            'enabled'   => false,
            'providers' => array()
        );

        $providers = array_keys(\Config::instance()->hybridAuth['providers']);

        if(is_array($providers) && !empty($providers)) {
            foreach ($providers as $provider) {

                $social = $this->getAdditionalData($provider);

                if (!$social || !$social['enabled']) {
                    $socials['disabled'] = true;
                    $socials['providers'][$provider] = 0;
                } else {
                    $socials['enabled'] = true;
                    $socials['providers'][$provider] = $social['identifier'];
                }
            }
        }


        return $socials;
    }

    public function generatePassword()
    {
        $an = array(
            0 => "abcdefghijklmnopqrstuwxyz",
            1 => "ABCDEFGHIJKLMNOPQRSTUWXYZ",
            2 => "0123456789",
        );
        $pass = substr(str_shuffle($an[0]), 0, 6);
        $pass .= substr(str_shuffle($an[1]), 0, 6);
        $pass .= substr(str_shuffle($an[2]), 0, 4);

        $this->setGeneratedPassword(str_shuffle($pass));
        return $this->getGeneratedPassword();
    }

    public function hidePassword($pass)
    {
        if(strlen($pass)>4){
            $pass=substr($pass,0, 2).str_pad('', strlen($pass)-4,"*"). substr($pass,strlen($pass)-2, 2);
        }
        return $pass;
    }

    public function compilePassword($password)
    {
        if (!$this->getSalt())
        {
            $this->setSalt(uniqid());
        }

        return md5($this->getSalt() . sha1($password));
    }

    public function validate($action, $params = array())
    {
        switch ($action) {
            case 'create':
                $this->validEmail();
                $this->validIp();
                try {
                    $this->fetch();
                    throw new EntityException('REG_LOGIN_EXISTS', 500);
                } catch (EntityException $e) {
                    if ($e->getCode() != 404) {
                        throw new EntityException($e->getMessage(), 500);
                    }
                }
            break;
            case 'fetch' :
                $this->getSocialId() || $this->getId() || $this->validEmail() || $this->validIp();
            break;
            case 'login' :
                $this->validEmail();
                $this->validIp();
                if (empty($params['password'])) {
                    throw new EntityException('EMPTY_PASSWORD', 400);
                }
                $this->fetch();
                if (!$this->getValid()) {
                    $this->writeLog(array('action'=>'LOGIN_DENIED', 'desc'=>'EMAIL_NOT_VALIDATED', 'status'=>'danger'));
                    throw new EntityException("EMAIL_NOT_VALIDATED", 400);
                }
            break;
            case 'update' :
                $this->validEmail();
                $this->setNicname(trim(htmlspecialchars(strip_tags($this->getNicname()))));
                $this->checkNickname();

                if ($this->getPhone()){
                    if(!preg_match('/^[+0-9\- ()]*$/', $this->getPhone()))
                        throw new EntityException("INVALID_PHONE_FORMAT", 400);
                    //$this->checkPhone();
                }

                if ($this->getYandexMoney()){
                    if(!preg_match('/^41001[0-9]{7,10}$/', $this->getYandexMoney()))
                        throw new EntityException("INVALID_YANDEXMONEY_FORMAT", 400);
                    //$this->checkYandexMoney();
                }

                if ($this->getWebMoney()){
                    if(!preg_match('/^[RZUBE][0-9]{12}$/', $this->getWebMoney()))
                        throw new EntityException("INVALID_WEBMONEY_FORMAT", 400);
                    //$this->checkWebMoney();
                }

                if ($this->getQiwi()){
                    if(!preg_match('/^[+0-9\- ()]*$/', $this->getQiwi()))
                        throw new EntityException("INVALID_QIWI_FORMAT", 400);
                    //$this->checkQiwi();
                }

                $this->setName(trim(htmlspecialchars(strip_tags($this->getName()))));
                $this->setSurname(trim(htmlspecialchars(strip_tags($this->getSurname()))));
                $this->setSecondName(trim(htmlspecialchars(strip_tags($this->getSecondName()))));

            break;

            default:
                # code...
            break;
        }

        return true;
    }

    public function uploadAvatar($photoURL=null)
    {
        try {
            $imageName = uniqid() . ".png";
            if ($photoURL) {
                \Common::saveImageMultiResolution('', PATH_FILESTORAGE . 'users/', $imageName, array(array(50, 'crop'), array(100, 'crop'), array(200, 'crop')), $photoURL);
            } else {
                \Common::saveImageMultiResolution('image', PATH_FILESTORAGE . 'users/', $imageName, array(array(50, 'crop'), array(100, 'crop'), array(200, 'crop')));
            }
            \Common::removeImageMultiResolution(PATH_FILESTORAGE.'users/', $this->getAvatar(), array(array(50),array(100),array(200)));

            /**
             * old saving
             */
            if ($photoURL) {
                $image = WideImage::load($photoURL);
            } else {
                $image = WideImage::loadFromUpload('image');
            }
            $image = $image->resize(Player::AVATAR_WIDTH, Player::AVATAR_WIDTH);
            $image = $image->crop("center", "center", Player::AVATAR_WIDTH, Player::AVATAR_WIDTH);
            $saveFolder = PATH_FILESTORAGE . 'avatars/' . (ceil($this->getId() / 100)) . '/';
            if (!is_dir($saveFolder)) {
                mkdir($saveFolder, 0777);
            }
            $image->saveToFile($saveFolder . $imageName);
            if ($this->getAvatar()) {
                @unlink($saveFolder . $this->getAvatar());
            };
            /**
             * /old saving
             */

            $this->setAvatar($imageName)->saveAvatar();

        } catch (EntityException $e) {
            throw new EntityException($e->getMessage(), $e->getCode());
        }

        if(!$photoURL)
            return $imageName;
        else
            return $this;
    }

    public function saveAvatar()
    {
        $model = $this->getModelClass();

        try {
            $model::instance()->saveAvatar($this);
        } catch (ModelException $e) {
            throw new EntityException('INTERNAL_ERROR', 500);
        }

        return $this;
    }

    public function initStats($data=null)
    {

        if(!isset($data)) {
            $model = $this->getModelClass();

            try {
                $data = $model::instance()->initStats($this);
            } catch (ModelException $e) {
                throw new EntityException($e->getMessage(), $e->getCode());

            }
        }

        $stats = array();
        foreach(self::$MASK['stats'] as $key)
            if(isset($data['Stat'.$key]))
                $stats[$key] = $data['Stat'.$key];
            elseif(isset($data[$key]))
                $stats[$key] = $data[$key];

        $this->setStats($stats);

        return $this;
    }

    public function initCounters($data=null)
    {

        if(!$data) {
            $model = $this->getModelClass();

            try {
                $data = $model::instance()->initCounters($this);
            } catch (ModelException $e) {
                throw new EntityException($e->getMessage(), $e->getCode());

            }
        }

        $counters = array();
        foreach(self::$MASK['counters'] as $key)
            if(isset($data[$key]))
                $counters[$key] = $data[$key];

        $this->setCounters($counters);

        return $this;
    }

    public function initPrivacy($data=null)
    {

        if (!$data) {

            $model = $this->getModelClass();
            try {
                $this->setPrivacy($model::instance()->loadPrivacy($this));
            } catch (ModelException $e) {
                throw new EntityException($e->getMessage(), $e->getCode());
            }

        } else {

            foreach (self::$MASK['privacy'] as $key)
                if (isset($data[$key]))
                    $this->setPrivacy($data[$key], $key);
        }

        return $this;
    }

    public function initDates($data=null)
    {

        if(!$data) {
            $model = $this->getModelClass();

            try {
                $data = $model::instance()->initDates($this);

            } catch (ModelException $e) {
                throw new EntityException($e->getMessage(), $e->getCode());
            }
        }

        $dates = array();
        foreach(self::$MASK['dates'] as $key)
            if(isset($data[$key]))
                $dates[$key] = $data[$key];

        $this->setDates($dates);

        return $this;
    }

    public function updateCookieId($cookie)
    {
        $model = $this->getModelClass();

        try {
            $model::instance()->updateCookieId($this,$cookie);
        } catch (ModelException $e) {
            throw new EntityException($e->getMessage(), $e->getCode());

        }

        return $this;
    }


    public function updateLogin()
    {
        $model = $this->getModelClass();

        try {
            $model::instance()->updateLogin($this);
        } catch (ModelException $e) {
            throw new EntityException($e->getMessage(), $e->getCode());

        }

        return $this;
    }

    public function updateIp($ip)
    {

        if(!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4))
            return $this;

        $ip=sprintf("%u", ip2long($ip));
        $model = $this->getModelClass();

        try {
            if(is_numeric($ip)){
                $model::instance()->updateIp($this,$ip);}
        } catch (ModelException $e) {
            throw new EntityException($e->getMessage(), $e->getCode());

        }

        return $this;
    }

    protected function checkNickname()
    {
        $model = $this->getModelClass();

        try {
            $model::instance()->checkNickname($this);
        } catch (ModelException $e) {
            if ($e->getCode() == 403) {
                throw new EntityException("NICKNAME_BUSY", 400);
            }
            throw new EntityException($e->getMessage(), $e->getCode());

        }

        return true;
    }

    protected function checkPhone()
    {
        $model = $this->getModelClass();

        try {
            $model::instance()->checkPhone($this);
        } catch (ModelException $e) {
            if ($e->getCode() == 403) {
                throw new EntityException("PHONE_BUSY", 400);
            }
            throw new EntityException($e->getMessage(), $e->getCode());

        }

        return true;
    }

    protected function checkQiwi()
    {
        $model = $this->getModelClass();

        try {
            $model::instance()->checkQiwi($this);
        } catch (ModelException $e) {
            if ($e->getCode() == 403) {
                throw new EntityException("QIWI_BUSY", 400);
            }
            throw new EntityException($e->getMessage(), $e->getCode());

        }

        return true;
    }

    protected function checkWebMoney()
    {
        $model = $this->getModelClass();

        try {
            $model::instance()->checkWebMoney($this);
        } catch (ModelException $e) {
            if ($e->getCode() == 403) {
                throw new EntityException("WEBMONEY_BUSY", 400);
            }
            throw new EntityException($e->getMessage(), $e->getCode());

        }

        return true;
    }

    protected function checkYandexMoney()
    {
        $model = $this->getModelClass();

        try {
            $model::instance()->checkYandexMoney($this);
        } catch (ModelException $e) {
            if ($e->getCode() == 403) {
                throw new EntityException("YANDEXMONEY_BUSY", 400);
            }
            throw new EntityException($e->getMessage(), $e->getCode());

        }

        return true;
    }

    protected function validIp()
    {

        $blockedIps=SettingsModel::instance()->getSettings('blockedIps')->getValue();
        if ((in_array($this->getIp(), $blockedIps) || $this->getLastIp() && in_array($this->getLastIp(), $blockedIps))) {
            throw new EntityException('BLOCKED_IP', 400);
        }

        return true;
    }

    protected function validEmail($throwException = true)
    {

        if (!filter_var($this->getEmail(), FILTER_VALIDATE_EMAIL)) {
            if ($throwException) {
                throw new EntityException('INVALID_EMAIL', 400);
            }
        }

        $emailDomain = substr(strrchr($this->getEmail(), "@"), 1);
        if (in_array($emailDomain, SettingsModel::instance()->getSettings('blockedEmails')->getValue())) {
            throw new EntityException('BLOCKED_EMAIL_DOMAIN', 400);
        }

        return true;
    }

    public function updatePrivacy()
    {
        $model = $this->getModelClass();

        try {
            $model::instance()->updatePrivacy($this);
        } catch (ModelException $e) {
            throw new EntityException('INTERNAL_ERROR', 500);
        }

        return $this;
    }

    public function updateSocial()
    {
        $model = $this->getModelClass();

        try {
            $model::instance()->updateSocial($this);
        } catch (ModelException $e) {
            throw new EntityException('INTERNAL_ERROR', 500);
        }

        return $this;
    }

    public function updateNewsSubscribe($newsSubscribe)
    {
        $model = $this->getModelClass();

        try {
            $model::instance()->updateNewsSubscribe($this, $newsSubscribe);
        } catch (ModelException $e) {
            throw new EntityException('INTERNAL_ERROR', 500);
        }

        return $this;
    }

    public function updateBalance($currency, $quantity=0)
    {
        $model = $this->getModelClass();

        try {
            $model::instance()->updateBalance($this, $currency, $quantity);
        } catch (ModelException $e) {
            throw new EntityException('INTERNAL_ERROR', 500);
        }

        return $this;
    }

    public function getBalance($forUpdate = false)
    {
        $model = $this->getModelClass();

        try {
            return $model::instance()->getBalance($this, $forUpdate);
        } catch (ModelException $e) {
            throw new EntityException('INTERNAL_ERROR', 500);
        }

    }

    public function checkBalance($currency, $sum, $withCoefficient = true)
    {
        $balance = $this->getBalance();
        switch ($currency) {

            case LotterySettings::CURRENCY_MONEY:
            case 'Money':
                if($withCoefficient) {
                    $sum *= \CountriesModel::instance()->getCountry($this->getCountry())->loadCurrency()->getCoefficient();
                }
                return $balance['Money'] >= $sum;
                break;

            case LotterySettings::CURRENCY_POINT:
            case 'Points':
                return $balance['Points'] >= $sum;
                break;

        }

    }

    public function disableSocial()
    {
        $model = $this->getModelClass();

        try {
            $socialData=$this->getAdditionalData()[$this->getSocialName()];
            $socialData['enabled']=0;
            $this->setAdditionalData(array($this->getSocialName()=>$socialData))->update();
            $model::instance()->disableSocial($this);
        } catch (ModelException $e) {
            throw new EntityException('INTERNAL_ERROR', 500);
        }

        return $this;
    }

    public function isSocialUsed()
    {
        $model = $this->getModelClass();
        return $model::instance()->isSocialUsed($this);
    }

    public function create()
    {
        $session = new Session();
        $psw=$this->generatePassword();
        $this->setPassword($this->compilePassword($psw))
            ->setAgent($_SERVER['HTTP_USER_AGENT'])
            //->setDates(time(), 'Registration')
            ->setReferer($session->get('REFERER'));

        parent::create();

        $this->updateIp(Common::getUserIp())
            ->writeLog(array('action'=>'PLAYER_CREATED', 'desc'=>$this->hidePassword($psw), 'status'=>'success'));

        /*
        Common::sendEmail($this->getEmail(), 'Регистрация на www.lotzon.com', 'player_registration', array(
            'login' => $this->getEmail(),
            'password'  => $this->getGeneratedPassword(),
            'hash'  => $this->getHash(),
        ));
        */

        return $this;
    }

    public function addMoney($quantity, $description = '', $inplaceUpdate = true) {

        $this->setMoney($this->getBalance()['Money'] + $quantity);

        if ($inplaceUpdate) {
            $this->updateBalance('Money', $quantity);
        }

        $this->addTransaction(
            LotterySettings::CURRENCY_MONEY,
            $quantity,
            $this->getMoney(),
            $description
        );

        return $this;
    }

    public function addPoints($quantity, $description = '', $inplaceUpdate = true) {
        //@TODO process transaction

        $this->setPoints($this->getBalance()['Points'] + $quantity);

        if ($inplaceUpdate) {
            $this->updateBalance('Points', $quantity);
        }

        $this->addTransaction(
            LotterySettings::CURRENCY_POINT,
            $quantity,
            $this->getPoints(),
            $description
        );

        return $this;
    }

    public function addTransaction($currency, $quantity, $balance, $description = '')
    {

        $transaction = new Transaction();
        $transaction->setPlayerId($this->getId())
            ->setCurrency($currency)
            ->setSum($quantity)
            ->setBalance($balance);

        if (is_array($description)) {
            $transaction
                ->setObjectType($description['type'])
                ->setObjectId(isset($description['id']) ? $description['id'] : null)
                ->setObjectUid(isset($description['uid']) ? $description['uid'] : null)
                ->setDescription($description['title']);
        } else {
            $transaction->setDescription($description);
        }

        $transaction->create();

        return $this;
    }


    public function login($password)
    {
        $password=trim($password);
        $this->validate('login', array(
            'password' => $password,
        ));

        try {
            $this->fetch()
                ->initDates();
        } catch (EntityException $e) {
            if ($e->getCode() == 404) {
                throw new EntityException("PLAYER_NOT_FOUND", 404);
            } else {
                $this->writeLog(array('action'=>'LOGIN_DENIED', 'desc'=>'INTERNAL_ERROR', 'status'=>'danger'));
                throw new EntityException("INTERNAL_ERROR", 500);
            }
        }
        if ($this->getBan()) {
            throw new EntityException("ACCESS_DENIED", 403);
        }

        $whitelist = SettingsModel::instance()->getSettings('whitelist')->getValue();
        if (is_array($whitelist) && !empty($whitelist) && !in_array($this->getId(), $whitelist)) {
            throw new EntityException("USER_NOT_ACCEPT", 403);
        }

        if ($this->getPassword() !== $this->compilePassword($password)) {
            $this->writeLog(array('action'=>'INVALID_PASSWORD', 'desc'=>$this->hidePassword($password), 'status'=>'danger'));
            throw new EntityException("INVALID_PASSWORD", 403);
        }

        if(!$_COOKIE[self::PLAYERID_COOKIE] OR $_COOKIE[self::PLAYERID_COOKIE] != $this->getId() OR $_COOKIE[self::PLAYERID_COOKIE] != $this->getCookieId() OR !$this->getCookieId())
            $this->updateCookieId($_COOKIE[self::PLAYERID_COOKIE]?:$this->getId());

        if(!$_COOKIE[self::PLAYERID_COOKIE])
            setcookie(self::PLAYERID_COOKIE, $this->getId(), time() + self::AUTOLOGIN_COOKIE_TTL, '/');

        $session = new Session();
        $session->set('QuickGameLastDate',($this->getDates('Login') < strtotime(date("Y-m-d"))? $this->getDates('Login') : time() ));

        $this->setDates(time(), 'Login')
            ->setCookieId(($_COOKIE[self::PLAYERID_COOKIE]?:$this->getId()))
            ->setLastIp(Common::getUserIp())
            ->updateIp(Common::getUserIp())
            ->payReferal()
            ->setAgent($_SERVER['HTTP_USER_AGENT'])
            ->update()
            ->updateLogin()
            ->writeLogin()
            ->initPrivacy()
            ->initCounters();

        $session->set(Player::IDENTITY, $this);

        return $this;
    }


    public function payInvite()
    {

        // check invites
        $invite = false;
        try {
            $invite = EmailInvites::instance()->getInvite($this->getEmail());
        } catch (ModelException $e) {}

        if ($invite && $invite->getValid()) {

            // mark referal unpaid for preverse of double points
            if ($this->getReferalId() && !$this->isReferalPaid())
                $this->setReferalPaid(1);
            /*
                try {
                    $this->markReferalPaid();
                } catch (EntityException $e) {}
            */
            // add bonuses to inviter and delete invite
            try {
                if(!$this->getReferer() && SettingsModel::instance()->getSettings('bonuses')->getValue('bonus_email_invite')){
                    $invite->getInviter()->addPoints(
                        SettingsModel::instance()->getSettings('bonuses')->getValue('bonus_email_invite'),
                        StaticTextsModel::instance()->setLang($this->getLang())->getText('bonus_email_invite'). $this->getEmail());
                }
                $invite->delete();
            } catch (EntityException $e) {}

            try {
                $this->setInviterId($invite->getInviter()->getId())->updateInvite();
            } catch (EntityException $e) {}

        }

        return $this;
    }

    public function payReferal()
    {

        // add referal points on first login
        if ($this->getReferalId() && !$this->isReferalPaid()) {
            try {
                if(!$this->getReferer() && SettingsModel::instance()->getSettings('bonuses')->getValue('bonus_referal_invite')){
                    $refPlayer = new Player();
                    $refPlayer->setId($this->getReferalId())->fetch();
                    $refPlayer->addPoints(
                        SettingsModel::instance()->getSettings('bonuses')->getValue('bonus_referal_invite'),
                        StaticTextsModel::instance()->setLang($this->getLang())->getText('bonus_referal_invite') .$this->getId());
                }
                $this->markReferalPaid();
            } catch (EntityException $e) {}
        }

        return $this;
    }

    public function changePassword($password)
    {
        $this->setSalt("")
            ->setPassword($this->compilePassword($password))
            ->setComplete(1);

        $model = $this->getModelClass();

        try {
            $model::instance()->changePassword($this);
        } catch (ModelException $e) {
            throw new EntityException('INTERNAL_ERROR', 500);
        }

        return $this;
    }

    public function updateNotice()
    {
        $this->setDates(time(), 'Notice');

        $model = $this->getModelClass();

        try {
            $model::instance()->updateNotice($this);
        } catch (ModelException $e) {
            throw new EntityException('INTERNAL_ERROR', 500);
        }
    }

    public function markOnline()
    {
        $this->setDates(time(), 'Ping');
        $model = $this->getModelClass();

        try {
            $model::instance()->markOnline($this);

        } catch (ModelException $e) {
            throw new EntityException('INTERNAL_ERROR', 500);
        }
    }

    public function formatFrom($from, $data)
    {
        if ($from == 'DB') {
            $this->setId($data['Id'])
                 ->setBan($data['Ban'])
                 ->setBot($data['Bot'])
                 ->setUtc($data['UTC'])
                 ->setCaptchaTime($data['CaptchaTime'])
                 ->setCaptchaCount($data['CaptchaCount'])
                 ->setEmail($data['Email'])
                 ->setPassword($data['Password'])
                 ->setSalt($data['Salt'])
                 ->setNicname($data['Nicname'])
                 ->setName($data['Name'])
                 ->setSurname($data['Surname'])
                 ->setSecondName($data['SecondName'])
                 ->setPhone($data['Phone'])
                 ->setQiwi($data['Qiwi'])
                 ->setYandexMoney($data['YandexMoney'])
                 ->setWebMoney($data['WebMoney'])
                 ->setBirthday($data['Birthday'])
                 ->setCountry($data['Country'])
                 ->setCity($data['City'])
                 ->setZip($data['Zip'])
                 ->setAddress($data['Address'])
                 ->setLang($data['Lang'])
                 ->setAvatar($data['Avatar'])
                 ->setAgent($data['Agent'])
                 ->setGender($data['Gender'])
                 ->setReferer($data['Referer'])
                 ->setVisible((boolean)$data['Visible'])
                 ->setFavoriteCombination(!empty($data['Favorite']) ? @unserialize($data['Favorite']) : array())
                 ->setPoints($data['Points'])
                 ->setMoney($data['Money'])
                 ->setGamesPlayed($data['GamesPlayed'])
                 ->setCookieId($data['CookieId'])
                 ->setIp($data['Ip'])
                 ->setLastIp($data['LastIp'])
                 ->setHash($data['Hash'])
                 ->setValid($data['Valid'])
                 ->setComplete($data['Complete'])
                 ->setInviterId($data['InviterId'])
                 ->setReferalId($data['ReferalId'])
                 ->setReferalPaid($data['ReferalPaid'])
                 ->setReferralsProfit($data['ReferralsProfit'])
                 ->setReferralPay($data['ReferralPay'])
                 ->setNewsSubscribe($data['NewsSubscribe'])
                 ->setAdmin(\Session2::connect()->has(\Admin::SESSION_VAR))
                 ->setAdditionalData(!empty($data['AdditionalData']) ? @unserialize($data['AdditionalData']) : null)
                 ->setGoldTicket($data['GoldTicket']);

            if (isset($data['CaptchaCount'])) {
                $this->initCounters($data);
            }

            if (isset($data['TicketsFilled'])) {
                $this->setTicketsFilled($data['TicketsFilled']);
            }

            if (isset($data['Registration'])) {
                $this->initDates($data);
            }

            if (isset($data['MyReferal'])) {
                $this->initStats($data);
            }
        }

        if ($from == 'Preregistration') {
            $this->setEmail($data['Email'])
                ->setIp($data['Ip'])
                ->setHash($data['Hash'])
                ->setReferalId($data['ReferalId']);
            $this->setDates($data['DateRegistration'], 'Registration');
            if ($data['SocialName']) {
                $this->setSocialEmail($data['SocialEmail'])
                    ->setSocialId($data['SocialId'])
                    ->setSocialName($data['SocialName']);
            }
        }

        return $this;
    }

    public function generateAutologinHash()
    {
        return md5($this->getEmail() . $this->getIp() . $this->getSalt());
    }

    public function enableAutologin()
    {
        setcookie(self::AUTOLOGIN_COOKIE, $this->getEmail(), time() + self::AUTOLOGIN_COOKIE_TTL, '/', false, true);
        setcookie(self::AUTOLOGIN_HASH_COOKIE, $this->generateAutologinHash(), time() + self::AUTOLOGIN_COOKIE_TTL, '/', false, true);

        return $this;
    }

    public function disableAutologin()
    {
        setcookie(self::AUTOLOGIN_COOKIE, "", -1, '/');
        setcookie(self::AUTOLOGIN_HASH_COOKIE, "", -1, '/');
    }

    public function setFriendship($friendId)
    {
        if (\FriendsModel::instance()->getStatus($friendId, $this->getId())===0) {
            $this->setFriend('request');
        } else {
            $this->setFriend(\FriendsModel::instance()->isFriend($friendId, $this->getId()));
        }

        return $this;
    }

    public function applyPrivacy($field)
    {
        switch (true) {
            case $this->getPrivacy($field) == 2:
            case $this->getPrivacy($field) == 1 && $this->getFriend():
                return isset($this->{'_' . strtolower($field)}) ? $this->{'get' . $field}() : true;
                break;

            default:
                return null;
                break;
        }
    }

    public function loadPreregistration() {
        $model = $this->getModelClass();
        return $model::instance()->loadPreregistration($this);
    }

    public function export($to)
    {
        switch ($to) {
            case 'referral':
                $ret = array(
                    'id'           => $this->getId(),
                    'img'          => $this->getAvatar(),
                    'name'         => $this->getNicname(),
                    'subreferrals' => array(
                        'total'  => \PlayersModel::instance()->getReferralsCount($this->getId()),
                        'active' => \PlayersModel::instance()->getReferralsCount($this->getId(),true),
                    ),
                    'lotteries'    => $this->getGamesPlayed(),
                    'profit'       => $this->getReferralPay(),
                );
                break;
            case 'card':
                $ret = array(
                    'id'   => $this->getId(),
                    'img'  => $this->getAvatar(),
                    'name' => $this->getNicname(),
                    'ping' => $this->getDates('Ping'),
                    'money'    => $this->getMoney(),
                    'points'   => $this->getPoints(),
                    'friends'  => \FriendsModel::instance()->getStatusCount($this->getId(), 1),
                );
                break;
            case 'info':
                $this->initPrivacy();
                $ret = array(
                    'id'       => $this->getId(),
                    'img'      => $this->getAvatar(),
                    'title'    => array(
                        'nickname'   => $this->getNicname(),
                        'name'       => $this->applyPrivacy('Name'),
                        'surname'    => $this->applyPrivacy('Surname'),
                    ),
                    'money'    => $this->getMoney(),
                    'points'   => $this->getPoints(),
                    'ping'     => $this->getDates('Ping'),
                    'gender'   => $this->applyPrivacy('Gender'),
                    'age'      => $this->applyPrivacy('Age'),
                    'birthday' => $this->applyPrivacy('Birthday') ? date('d.m.', $this->getBirthday())."1900" : null,
                    'zip'      => $this->applyPrivacy('Zip'),
                    'address'  => $this->applyPrivacy('Address'),
                    'location' => array(
                        'country'    => $this->applyPrivacy('Country'),
                        'city'       => $this->applyPrivacy('City'),
                    ),
                    'friends'  => \FriendsModel::instance()->getStatusCount($this->getId(), 1),
                    /*
                    'social'     => $this->getSocial()
                    */
                );
                if ($this->getFriend()!==null) {
                    $ret['isFriend'] = $this->getFriend();
                }
                break;
            default:
                throw new EntityException('Export type is not supported', 500);
        }
        return $ret;
    }

}
