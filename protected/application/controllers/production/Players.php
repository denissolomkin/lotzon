<?php

namespace controllers\production;
use \Application, \Config, \Player, \EntityException, \Session2, \WideImage, \EmailInvites, \EmailInvite, \ModelException, \Common, \ChanceGamesModel;
use \GeoIp2\Database\Reader;
use Symfony\Component\HttpFoundation\Session\Session;

Application::import(PATH_APPLICATION . 'model/entities/Player.php');
Application::import(PATH_CONTROLLERS . 'production/AjaxController.php');
Application::import(PATH_PROTECTED . 'external/wi/WideImage.php');

class Players extends \AjaxController
{

    public function init()
    {
        $this->session = new Session();
        parent::init();
    }


    public function registerAction()
    {
        if ($this->validRequest()) {
            $agreed = $this->request()->post('agree', false);
            $email  = $this->request()->post('email', null);
            if (empty($email)) {
                $this->ajaxResponse(array(), 0, 'EMPTY_EMAIL');
            }
            if (!$agreed) {
                $this->ajaxResponse(array(), 0, 'AGREE_WITH_RULES');
            }
            $player = new Player();
            $player->setEmail($email);

            try {
                $geoReader =  new Reader(PATH_MMDB_FILE);
                $country = $geoReader->country(Common::getUserIp())->country;
                $player->setCountry($country->isoCode);

            } catch (\Exception $e) {
                $player->setCountry(Config::instance()->defaultLang);
            }

            $player->setVisibility(true);
            $player->setIP(Common::getUserIp());
            $player->setHash(md5(uniqid()));
            $player->setValid(false);

            if ($ref = $this->request()->post('ref', null)) {
                $player->setReferalId((int)$ref);
            }

            try {
                $player->create();
            } catch (EntityException $e) {
                $this->ajaxResponse(array(), 0, $e->getMessage());
            }
            // check invites
            $invite = false;
            try {
                $invite = EmailInvites::instance()->getInvite($player->getEmail());
            } catch (ModelException $e) {}

            if ($invite && $invite->getValid()) {

                // mark referal unpaid for preverse of double points
                if ($player->getReferalId() && !$player->isReferalPaid()) {
                    try {
                        $player->markReferalPaid();
                    } catch (EntityException $e) {}
                }

                // add bonuses to inviter and delete invite
                try {
                    $invite->getInviter()->addPoints(EmailInvite::INVITE_COST, 'Приглашение друга ' . $player->getEmail());
                    $invite->delete();
                } catch (EntityException $e) {}
            }

            if ($player->getId() <= 1000) {
                $player->addPoints(300, 'Бонус за регистрацию в первой тысяче участников');
            }
            $this->ajaxResponse(array(
                'id' => $player->getId(),
            ));
        }

        $this->redirect('/');
    }

    public function loginAction()
    {
        if ($this->validRequest()) {
            $email  = $this->request()->post('email', null);
            $password = $this->request()->post('password', null);
            $rememberMe = $this->request()->post('remember', false);

            if (empty($email)) {
                $this->ajaxResponse(array(), 0, 'EMPTY_EMAIL');
            }
            if (!$password) {
                $this->ajaxResponse(array(), 0, 'EMPTY_PASSWORD');
            }
            $player = new Player();
            $player->setEmail($email);

            try {
                $player->login($password)->markOnline();

                if ($rememberMe) {
                    $player->enableAutologin();
                }
                // set cookie to not show register form
                setcookie("showLoginScreen", "1", time() + (10 * 365 * 24 * 60 * 60), '/');
            } catch (EntityException $e) {
                $this->ajaxResponse(array(), 0, $e->getMessage());
            }

            $this->ajaxResponse(array());
        }

        $this->redirect('/');
    }

    public function loginVkAction() {
        if (!$this->request()->get('redirected')) {
            if ($ref = $this->request()->post('ref', '')) {
                $ref = '&ref=' . $ref;
            }
            $auth_url = "https://oauth.vk.com/authorize?client_id=%s&scope=%s&redirect_uri=%s&response_type=code";
            $auth_url = vsprintf($auth_url, array(
                Config::instance()->vkCredentials['appId'],
                Config::instance()->vkCredentials['scope'],
                urlencode(Config::instance()->vkCredentials['redirectUrl'] . $ref),
            ));

            $this->redirect($auth_url);
        } else {
            // returned
            if ($vkAuthCode = $this->request()->get('code')) {

                $token_url = vsprintf("https://oauth.vk.com/access_token?client_id=%s&client_secret=%s&code=%s&redirect_uri=%s", array(
                    Config::instance()->vkCredentials['appId'],
                    Config::instance()->vkCredentials['secret'],
                    $vkAuthCode,
                    urlencode(Config::instance()->vkCredentials['redirectUrl']),
                ));
                $data = @file_get_contents($token_url);
                if ($data = @json_decode($data)) {
                    if ($data->email) {
                        $player = new Player();
                        $player->setEmail($data->email);
                        $loggedIn = false;
                        try {
                            $player->fetch();

                            $loggedIn = true;
                        } catch (EntityException $e) {
                            if ($e->getCode() == 404) {
                                $infoUrl = vsprintf("https://api.vk.com/method/users.get?user_id=%s&v=5.26&access_token=%s&fields=country,photo_200,interests,music,movies,tv,books,games", array(
                                    $data->user_id,
                                    $data->access_token,
                                ));

                                $profile = @file_get_contents($infoUrl);
                                if ($resp = @json_decode($profile, true)) {
                                    $profile = array_shift($resp['response']);
                                    $countries = array(
                                        1 => 'RU',
                                        2 => 'UA',
                                        3 => 'BY',
                                    );
                                    $realCountry = $countries[$profile['country']['id']] ?: Config::instance()->defaultLang;

                                    try {
                                        $player->setCountry($realCountry)
                                           ->setIp(Common::getUserIp())
                                           ->setHash('')
                                           ->setValid(true)
                                           ->setName($profile['first_name'])
                                           ->setSurname($profile['last_name'])
                                           ->setAdditionalData(
                                                array(
                                                    'vkInfo' => array(
                                                        'interests' => $profile['interests'],
                                                        'music'     => $profile['music'],
                                                        'movies'    => $profile['movies'],
                                                        'tv'        => $profile['tv'],
                                                        'books'     => $profile['books'],
                                                        'games'     => $profile['games'],
                                                    )
                                                )
                                            );
                                        if ($ref = $this->request()->post('ref', null)) {
                                            $player->setReferalId((int)$ref);
                                        }
                                        $player->create()->markOnline();

                                        $loggedIn = true;

                                        if ($player->getId() <= 1000) {
                                            $player->addPoints(300, 'Бонус за регистрацию в первой тысяче участников');
                                        }

                                        // try to catch avatar
                                        if ($profile['photo_200']) {
                                            try {
                                                $image = WideImage::load($profile['photo_200']);
                                                $image = $image->resize(Player::AVATAR_WIDTH, Player::AVATAR_WIDTH);
                                                $image = $image->crop("center", "center", Player::AVATAR_WIDTH, Player::AVATAR_WIDTH);

                                                $imageName = uniqid() . ".jpg";
                                                $saveFolder = PATH_FILESTORAGE . 'avatars/' . (ceil($player->getId() / 100)) . '/';

                                                if (!is_dir($saveFolder)) {
                                                    mkdir($saveFolder, 0777);
                                                }
                                                $image->saveToFile($saveFolder . $imageName, 100);
                                                // remove old one
                                                $player->setAvatar($imageName)->saveAvatar();

                                                $this->ajaxResponse($data);
                                            } catch (\Exception $e) {
                                                // do nothing
                                            }
                                        }
                                    } catch (EntityException $e) {
                                        // do nothing
                                    }

                                }
                            }
                        }

                        if ($loggedIn === true) {
                            $this->session->set(Player::IDENTITY, $player);
                        }
                    }
                }
            }

            $this->redirect('/');
        }
    }

    public function logoutAction()
    {
        //$session=new Session();
        if($this->session->get(Player::IDENTITY))
            $this->session->get(Player::IDENTITY)->disableAutologin();
        // $this->session->get(Player::IDENTITY)->disableAutologin();
        // $this->session->close();
        session_destroy();

        $this->redirect('/');
    }

    public function updateAction()
    {
        if ($this->validRequest()) {
            $email = $this->request()->post('email');
            if (!$this->session->get(Player::IDENTITY)) {
                $this->ajaxResponse(array(), 0, 'FRAUD');
            }
            if ($this->session->get(Player::IDENTITY)->getEmail() !== $email) {
                $this->ajaxResponse(array(), 0, 'FRAUD');
            }


            $player = $this->session->get(Player::IDENTITY);

            try {
                if ($this->request()->post('bd') && !strtotime($this->request()->post('bd'))) {
                    throw new EntityException("INVALID_DATE_FORMAT", 400);
                }
                $player->setNicname($this->request()->post('nick'))
                   ->setName($this->request()->post('name'))
                   ->setSurName($this->request()->post('surname'))
                   ->setSecondName($this->request()->post('secondname'))
                   ->setPhone($this->request()->post('phone'))
                   ->setBirthday(strtotime($this->request()->post('bd')))
                   ->setVisibility($this->request()->post('visible', false));
                $favs = $this->request()->post('favs', array());

                $player->setFavoriteCombination($favs);

                $player->update();

                $this->session->set(Player::IDENTITY, $player);
            } catch (EntityException $e){
                $this->ajaxResponse(array(), 0, $e->getMessage());
            }
            if ($pwd = $this->request()->post('password')) {
                $this->session->get(Player::IDENTITY)->changePassword($pwd);
            }
            $this->ajaxResponse(array());
        }
        $this->redirect('/');
    }

    public function saveAvatarAction()
    {

        if (!$this->session->get(Player::IDENTITY)) {
            $this->ajaxResponse(array(), 0, 'FRAUD');
        }

        try {
            $image = WideImage::loadFromUpload('image');
            $image = $image->resize(Player::AVATAR_WIDTH, Player::AVATAR_WIDTH);
            $image = $image->crop("center", "center", Player::AVATAR_WIDTH, Player::AVATAR_WIDTH);

            $imageName = uniqid() . ".jpg";
            $saveFolder = PATH_FILESTORAGE . 'avatars/' . (ceil($this->session->get(Player::IDENTITY)->getId() / 100)) . '/';

            if (!is_dir($saveFolder)) {
                mkdir($saveFolder, 0777);
            }

            $image->saveToFile($saveFolder . $imageName, 100);
            // remove old one
            if ($this->session->get(Player::IDENTITY)->getAvatar()) {
                @unlink($saveFolder . $this->session->get(Player::IDENTITY)->getAvatar());
            };
            $data = array(
                'imageName' => $imageName,
                'imageWebPath' => '/filestorage/avatars/' . (ceil($this->session->get(Player::IDENTITY)->getId() / 100)) . '/' . $imageName,
            );

            $this->session->get(Player::IDENTITY)->setAvatar($imageName)->saveAvatar();

            $this->ajaxResponse($data);
        } catch (\Exception $e) {
            $this->ajaxResponse(array(), 0, 'INVALID');
        }
    }

    public function removeAvatarAction()
    {
        if ($this->session->get(Player::IDENTITY)->getAvatar()) {
            @unlink(PATH_FILESTORAGE . 'avatars/' . (ceil($this->session->get(Player::IDENTITY)->getId() / 100)) . '/' . $this->session->get(Player::IDENTITY)->getAvatar());
        }
        $this->session->get(Player::IDENTITY)->setAvatar("")->saveAvatar();

        $this->ajaxResponse(array());
    }

    public function pingAction()
    {

        $resp = array();
        if ($this->session->get(Player::IDENTITY)) {
            $this->session->get(Player::IDENTITY)->markOnline();
            // check for moment chance
            // if not already played chance game
            if ($_SESSION['chanceGame']['moment']) {
                if ($_SESSION['chanceGame']['moment']['start'] + 180 < time()) {
                    unset($_SESSION['chanceGame']['moment']);
                }
            }
            if ($this->session->get('MomentChanseLastDate') && !$_SESSION['chanceGame']) {
                $chanceGames = ChanceGamesModel::instance()->getGamesSettings();

                if ($this->session->get('MomentChanseLastDate') + $chanceGames['moment']->getMinFrom() * 60 <= time() &&
                    $this->session->get('MomentChanseLastDate') + $chanceGames['moment']->getMinTo() * 60 >= time()) {
                    if (($rnd = mt_rand(0, 100)) <= 30) {
                        $resp['moment'] = 1;
                    } else if ($this->session->get('MomentChanseLastDate') + $chanceGames['moment']->getMinTo()  * 60 - time() < 60) {
                        // if not fired randomly  - fire at last minute
                        $resp['moment'] = 1;
                    }
                }

                $resp['moment']=1;
                if (isset($resp['moment']) && $resp['moment']) {
                    $gameField = $chanceGames['moment']->generateGame();
                    $_SESSION['chanceGame']=array(
                        'moment' => array(
                            'id'     => 'moment',
                            'start'  => time(),
                            'field'  => $gameField,
                            'clicks' => array(),
                            'status' => 'process',
                        ),
                    );
                    $this->session->set('MomentChanseLastDate', time() + $chanceGames['moment']->getMinTo()  * 60);
                }
            }
        }

        $this->ajaxResponse($resp);
    }

    public function resendPasswordAction()
    {
        $email = $this->request()->post('email');
        $player = new Player();
        $player->setEmail($email);

        try {
            $player->fetch();

            $newPassword = $player->generatePassword();
            $player->changePassword($newPassword);
        } catch (EntityException $e) {
            $this->ajaxResponse(array(), 0, $e->getMessage());
        }

        Common::sendEmail($player->getEmail(), 'Восстановление пароля на www.lotzon.com', 'player_password', array(
            'password'  => $newPassword,
        ));

        $this->ajaxResponse(array());
    }

    public function socialAction()
    {
        if ($this->session->get(Player::IDENTITY)->getSocialPostsCount() > 0) {
            $this->session->get(Player::IDENTITY)->decrementSocialPostsCount();
            $this->session->get(Player::IDENTITY)->addPoints(Player::SOCIAL_POST_COST, "Пост с реферальной ссылкой");
            $this->ajaxResponse(array(
                'postsCount' => $this->session->get(Player::IDENTITY)->getSocialPostsCount(),
            ));
        } else {
            $this->ajaxResponse(array(), 0, 'NO_MORE_POSTS');
        }

    }
}