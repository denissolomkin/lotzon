<?php

namespace controllers\production;
use \Application, \Player, \EntityException, \CountriesModel, \SettingsModel, \StaticTextsModel, \WideImage, \EmailInvites, \EmailInvite, \ModelException, \Common, \NoticesModel, \GamesSettingsModel, \GameSettingsModel, \ChanceGamesModel;
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
            if(!in_array($_SERVER['HTTP_HOST'],array('lotzon.test','lotzon.com','testbed.lotzon.com','192.168.1.253','lotzon')))
                $this->ajaxResponse(array(), 0, 'ACCESS_DENIED');


            $player = new Player();
            $player->setEmail($email);

            try {
                $geoReader =  new Reader(PATH_MMDB_FILE);
                $country = $geoReader->country(Common::getUserIp())->country;
                $player->setCountry($country->isoCode);

            } catch (\Exception $e) {
                $player->setCountry(CountriesModel::instance()->defaultCountry());
            }

            $player->setVisibility(true);
            $player->setIP(Common::getUserIp());
            $player->setHash(md5(uniqid()));
            $player->setValid(false);
            $player->setLang(CountriesModel::instance()->getCountry($player->getCountry())->getLang());

            if ($ref = $this->request()->post('ref', null)) {
                $player->setReferalId((int)$ref);
            } elseif ($this->session->has('SOCIAL_IDENTITY') AND $ref = $this->session->get('SOCIAL_IDENTITY')->getReferalId()){
                $player->setReferalId((int)$ref);
            }

            try {
                $player->create();
            } catch (EntityException $e) {
                if($e->getMessage()=='REG_LOGIN_EXISTS' AND $this->session->has('SOCIAL_IDENTITY'))
                    $this->ajaxResponse(array(), 0, 'PROFILE_EXISTS_NEED_LOGIN');
                else
                    $this->ajaxResponse(array(), 0, $e->getMessage());
            }

            // check invites
            $player->payInvite();

            if ($player->getId() <= 100000) {
                $player->addPoints(200, 'Бонус за регистрацию');
            }

            if($this->session->has('SOCIAL_IDENTITY'))
            {
                $social=$this->session->get('SOCIAL_IDENTITY');
                $this->session->remove('SOCIAL_IDENTITY');

                if(!$social->isSocialUsed() && SettingsModel::instance()->getSettings('bonuses')->getValue('bonus_social_registration')) // If Social Id didn't use earlier
                    $player->addPoints(
                        SettingsModel::instance()->getSettings('bonuses')->getValue('bonus_social_registration'),
                        StaticTextsModel::instance()->setLang($player->getLang())->getText('bonus_social_registration'). $social->getSocialName());

                $player->setAdditionalData($social->getAdditionalData())
                    ->setName($social->getName())
                    ->setSurname($social->getSurname())
                    ->setDateLastLogin(time())
                    ->update()
                    ->setSocialId($social->getSocialId())
                    ->setSocialName($social->getSocialName())
                    ->setSocialEmail($social->getSocialEmail())
                    ->updateSocial()
                    ->payReferal()
                    ->markOnline();

                if (!$player->getAvatar() AND $photoURL=$social->getAdditionalData()[$social->getSocialName()]['photoURL'])
                    $player->uploadAvatar($photoURL);

                $this->session->set(Player::IDENTITY,$player);
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


            if(!in_array($_SERVER['HTTP_HOST'],array('lotzon.test','lotzon.com','testbed.lotzon.com','192.168.1.253','lotzon')))
            {$this->ajaxResponse(array(), 0, 'ACCESS_DENIED');}

            $player = new Player();
            $player->setEmail($email);

            try {
                $player->login($password)->markOnline();

                if($this->session->has('SOCIAL_IDENTITY'))
                {
                    $social=$this->session->get('SOCIAL_IDENTITY');

                    // If Social Id didn't use earlier And This Provider Link First Time
                    if(!array_key_exists($social->getSocialName(), $player->getAdditionalData()) AND !$social->isSocialUsed() && SettingsModel::instance()->getSettings('bonuses')->getValue('bonus_social_profile'))
                        $player->addPoints(
                            SettingsModel::instance()->getSettings('bonuses')->getValue('bonus_social_profile'),
                            StaticTextsModel::instance()->setLang($player->getLang())->getText('bonus_social_profile'). $social->getSocialName());

                    if (!$player->getAvatar() AND $photoURL=$social->getAdditionalData()[$social->getSocialName()]['photoURL'])
                        $this->saveAvatarAction($photoURL);

                    if(!$player->getName() AND $social->getName())
                        $player->setName($social->getName());

                    if(!$player->getSurname() AND $social->getSurname())
                        $player->setSurname($social->getSurname());

                    $player->setAdditionalData($social->getAdditionalData())
                        ->update()
                        ->setSocialId($social->getSocialId())
                        ->setSocialName($social->getSocialName())
                        ->setSocialEmail($social->getSocialEmail())
                        ->updateSocial();

                    $this->session->remove('SOCIAL_IDENTITY');
                }

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

    public function logoutAction()
    {
        //$session=new Session();
        if($this->session->has(Player::IDENTITY))
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
                $favs = $this->request()->post('favs', array());
                $player->setNicname($this->request()->post('nick'))
                    ->setName($this->request()->post('name'))
                    ->setSurName($this->request()->post('surname'))
                    ->setSecondName($this->request()->post('secondname'))
                    ->setPhone($this->request()->post('phone'))
                    ->setBirthday(strtotime($this->request()->post('bd')))
                    ->setVisibility($this->request()->post('visible', false))
                    ->setFavoriteCombination($favs)
                    ->update();

                $this->session->set(Player::IDENTITY, $player);
            } catch (EntityException $e){
                $this->ajaxResponse(array(), 0, $e->getMessage());
            }
            if ($pwd = $this->request()->post('password')) {
                $pwd=trim($pwd);
                $player->writeLog(array('action'=>'CHANGE_PASSWORD', 'desc'=>$player->hidePassword($pwd),'status'=>'info'))
                    ->changePassword($pwd);
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
        else
        try {
            $imageName = $this->session->get(Player::IDENTITY)->uploadAvatar();

            $data = array(
                'imageName' => $imageName,
                'imageWebPath' => '/filestorage/avatars/' . (ceil($this->session->get(Player::IDENTITY)->getId() / 100)) . '/' . $imageName,
            );

            $this->ajaxResponse($data);

        } catch (\Exception $e) {
            $this->ajaxResponse(array(), 0, 'INVALID');
        }
    }

    public function changeLanguageAction($lang)
    {
        if(!($lang=substr($lang,0,2)) || !(CountriesModel::instance()->isLang($lang))) {
            $this->ajaxResponse(array(), 0, 'LANGUAGE_ERROR');
        } else if (!$this->session->get(Player::IDENTITY) || !$player=$this->session->get(Player::IDENTITY)) {
            $this->ajaxResponse(array(), 0, 'FRAUD');
        } else {
            $player->setLang($lang)->update();
            $this->ajaxResponse(array(), 1, 'READY');
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

    public function disableSocialAction($provider)
    {

        if (!$this->session->get(Player::IDENTITY)) {
            $this->ajaxResponse(array(), 0, 'FRAUD');
        }

        try {
            $this->session->get(Player::IDENTITY)->setSocialName($provider)->disableSocial();
            $this->ajaxResponse(array(), 1, $provider);
        } catch (\Exception $e) {
            $this->ajaxResponse(array(), 0, 'INVALID');
        }

    }

    public function troubleAction($trouble)
    {

        if (!$this->session->get(Player::IDENTITY)) {
            $this->ajaxResponse(array(), 0, 'FRAUD');
        }

        try {
            $this->session->get(Player::IDENTITY)->reportTrouble($trouble);
        } catch (\Exception $e) {
            $this->ajaxResponse(array(), 0, 'INVALID');
        }

    }

    public function pingAction()
    {
        $resp = array();
        if ($this->session->has(Player::IDENTITY) && $player=$this->session->get(Player::IDENTITY)) {
            //$chanceGames = ChanceGamesModel::instance()->getGamesSettings();
            $gameSettings=GameSettingsModel::instance()->getList();

            if($title=NoticesModel::instance()->getPlayerLastUnreadNotice($player))
                $resp['notice'] = array(
                    'name'=> 'notice',
                    'title'=>'Уведомление',
                    'txt'=>$title,
                    'unread'=>NoticesModel::instance()->getPlayerUnreadNotices($player)
                );

            $AdBlockDetected=$this->request()->get('online', null);

            if(($player->getAdBlock() && !$AdBlockDetected) || (!$player->getAdBlock() && $AdBlockDetected))
                $player->writeLog(array('action'=>'AdBlock','desc'=>($AdBlockDetected?'ADBLOCK_DETECTED':'ADBLOCK_DISABLED'),'status'=>($AdBlockDetected?'danger':'warning')));

            $player->setWebSocket($this->request()->get('ws', null))
                ->setDateAdBlocked(($AdBlockDetected?time():null))
                ->setAdBlock(($AdBlockDetected?time():null))
                ->markOnline();


            $key='QuickGame';
            $timer=$gameSettings[$key]->getOption('min');

            if(!$this->session->has($key.'LastDate'))
                $this->session->set($key.'LastDate',time());


            $diff = $this->session->get($key.'LastDate') + $timer  * 60 - time();

            if ($diff<0 OR ($diff/60<=5 AND !$this->session->get($key.'Important'))) {

                if ($diff / 60 < 5 AND !$this->session->get($key.'Important'))
                    $this->session->set($key.'Important',true);

                $resp['qgame'] = array(
                    'timer' => $diff,
                    'important' => true
                );
            }

            #delete
            //$resp['moment'] = 1;
            //unset($_SESSION['chanceGame']);

            $key='Moment';

            // check for moment chance
            // if not already played chance game

            if (
                (!$this->session->has($key) && time() - $this->session->get('MomentLastDate') > $gameSettings['Moment']->getOption('max') * 60)
                ||
                ($this->session->has($key) && $this->session->get($key)->getTime() + $this->session->get($key)->getTimeout() * 60 < time() && $this->session->remove($key))
            ){
                $this->session->set('MomentLastDate', time());
            }

/*
            if ($_SESSION['chanceGame']['moment']) {
                if ($_SESSION['chanceGame']['moment']['start'] + 180 < time()) {
                    unset($_SESSION['chanceGame']['moment']);
                    $this->session->set($key.'LastDate',time());
                }
            }
*/
            if ($this->session->get($key.'LastDate') && !$this->session->has($key) && isset($gameSettings[$key])) {

                #delete
                /*
                 if($this->session->get('MomentLastDate') + $chanceGames['moment']->getMinTo()  * 60 > time()) {
                    $diff=($chanceGames['moment']->getMinFrom() - $chanceGames['moment']->getMinTo());
                    //if(($diff<5 AND !$_SESSION['timer_soon']['five']) OR ($diff<$chanceGames['moment']->getMinFrom() AND !$_SESSION['timer_soon']['start']) OR $diff<)
                    $resp['soon'] = array(
                        'name' => 'soon',
                        'title' => 'Моментальный шанс',
                        'txt' => 'Шанс будет доступен через  '.$diff.'<span id="timer_soon"></span><script>
                    $("#timer_soon").countdown({until: ' . ($this->session->get('MomentLastDate') + $chanceGames['moment']->getMinFrom() * 60 - time()) . ',layout: "{mnn}:{snn}",
                    onExpiry: function(){
                    $(".notification #soon .badge-block .txt").html("Не пропустите моментальный шанс ' . ' ' .($diff>0?'в ближайшие '. $diff .($diff>4 ? 'минут':$diff>1?'минуты':$diff>0?'минуту':''):'сейчас') . '!");}
                     })</script>',
                    );
                }
                */

                if ($this->session->get($key.'LastDate') + $gameSettings[$key]->getOption('min') * 60 <= time() &&
                    $this->session->get($key.'LastDate') + $gameSettings[$key]->getOption('max') * 60 >= time()) {
                    if ( ($rnd = mt_rand(0, 100)) <= 100 / (($gameSettings[$key]->getOption('max') - $gameSettings[$key]->getOption('min'))?:1) ) {
                        $resp['moment'] = 1;
                    } elseif ($this->session->get($key.'LastDate') + $gameSettings[$key]->getOption('max') * 60 - time()) {
                        // if not fired randomly  - fire at last minute
                        $resp['moment'] = 1;
                    }
                }

                #delete
                //$resp['moment'] = 0;

                //if (isset($resp['moment']) && $resp['moment']) {
                    //$this->session->set($key.'LastDate', time());

                    /*
                    if(is_array(Config::instance()->banners['Moment']))
                        foreach(Config::instance()->banners['Moment'] as $group) {
                            if (is_array($group)) {
                                shuffle($group);
                                foreach ($group as $banner) {
                                    if (is_array($banner['countries']) and !in_array($player->getCountry(), $banner['countries']))
                                        continue;

                                    if(!rand(0,$banner['chance']-1) AND $banner['chance'] AND Config::instance()->banners['settings']['enabled'])
                                        $resp['block'] = '<!-- ' . $banner['title'] . ' -->' .
                                            str_replace('document.write',"$('#mchance .block').append",$banner['div']).
                                            str_replace('document.write',"$('#mchance .block').append",$banner['script']).
                                            "<script>
                                            $('#mchance .mm-bk-pg').css('height','450px').css('overflow','hidden').children('div').last().css('position', 'absolute').css('bottom', '0');
                                            moment=$('#mchance').find('.block');
                                            moment.find('.tl').html('Загрузка...').next().css('top','200px').css('position','absolute').css('overflow','hidden');
                                            $('#mchance').find('li').off('click').on('click', function(){
                                            num=$(this).data('num');
                                            href=moment.find('a[target=\"_blank\"]:eq('+((moment.find('a[target=\"_blank\"]').length-6)+num*2-1)+')').attr('href');
                                            moment.css('position', 'absolute').css('bottom', '-10px').parent().css('position', 'initial').css('bottom','auto');
                                            window.setTimeout(function() {moment.find('.tl').html('Реклама').parent().prev().css('margin-bottom', '380px').next().find('div:eq(1)').css('top','auto').css('position', 'initial');}, 50);
                                            window.setTimeout(function() {moment.css('position', 'initial').parent().find('ul').css('margin-bottom', '-50px');}, 150);
                                            window.setTimeout(function() {moment.parent().find('ul').css('margin-bottom', 'auto').parent().parent().css('height','auto');}, 200);
                                            if(moment.find('a[target=\"_blank\"]').length>=3) window.setTimeout(function() { var win = window.open (href,'_blank');win.blur();window.focus();return false;}, 1000);
                                            startMoment();});
                                        </script>";
                                    else
                                        $resp['block'] = '<!-- ' . $banner['title'] . ' -->' .
                                            str_replace('document.write',"$('#mchance .block').append",$banner['div']).
                                            str_replace('document.write',"$('#mchance .block').append",$banner['script']).
                                            "<script>
                                            $('#mchance .mm-bk-pg').css('height', 'auto').children('div').last().css('position','initial');
                                            startMoment();
                                            </script>";
                                    break;
                                }
                            }
                        }

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

                    */
                //}

                //if($this->session->get($key.'LastDate') + $gameSettings[$key]->getOption('max') * 60 + 5*60 < time())
                //    $this->session->set($key.'LastDate', time());

            } elseif($this->session->has($key)) {
                $resp['game'] = 1;
            }

                $resp['test'] = ($this->session->get($key.'LastDate') + $gameSettings[$key]->getOption('min')  * 60 - time());
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
            $player->writeLog(array('action'=>'RESEND_PASSWORD', 'desc'=>$player->hidePassword($newPassword),'status'=>'warning'))->changePassword($newPassword);
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

            if(SettingsModel::instance()->getSettings('bonuses')->getValue('bonus_social_post'))
            $this->session->get(Player::IDENTITY)->addPoints(
                SettingsModel::instance()->getSettings('bonuses')->getValue('bonus_social_post'),
                StaticTextsModel::instance()->setLang($this->session->get(Player::IDENTITY)->getLang())->getText('bonus_social_post'));

            $this->ajaxResponse(array(
                'postsCount' => $this->session->get(Player::IDENTITY)->getSocialPostsCount(),
            ));
        } else {
            $this->ajaxResponse(array(), 0, 'NO_MORE_POSTS');
        }
    }
}