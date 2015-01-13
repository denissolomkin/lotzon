<?php
namespace controllers\production;
use \Config,  \Hybrid_Auth, \Player, \EntityException, \WideImage,  \Common;
use \GeoIp2\Database\Reader;
use Symfony\Component\HttpFoundation\Session\Session;

class AuthController extends \SlimController\SlimController {

    private $session;

    public function __construct(\Slim\Slim &$app)
    {
        parent::__construct($app);
        $this->init();
        $this->session = new Session();
    }

    public function init()
    {

    }

    public function endpointAction()
    {
            require_once PATH_PROTECTED . 'external/hybridauth/index.php';

    }

    public function authAction($provider) {

        /* etc. */

        try{

            require_once PATH_PROTECTED . 'external/hybridauth/Hybrid/Auth.php';

            // create an instance for Hybridauth with the configuration file path as parameter
            $hybridauth = new Hybrid_Auth(Config::instance()->hybridAuth);

            // try to authenticate the selected $provider
            $adapter = $hybridauth->authenticate( $provider );

            $profile = $adapter->getUserProfile();

            // if okey, we will redirect to user profile page
            //    $hybridauth->redirect( "/" );
        }
        catch( Exception $e ){
            // In case we have errors 6 or 7, then we have to use Hybrid_Provider_Adapter::logout() to
            // let hybridauth forget all about the user so we can try to authenticate again.

            // Display the received error,
            // to know more please refer to Exceptions handling section on the userguide
            switch( $e->getCode() ){
                case 0 : $error = "Unspecified error."; break;
                case 1 : $error = "Hybriauth configuration error."; break;
                case 2 : $error = "Provider not properly configured."; break;
                case 3 : $error = "Unknown or disabled provider."; break;
                case 4 : $error = "Missing provider application credentials."; break;
                case 5 : $error = "Authentication failed. The user has canceled the authentication or the provider refused the connection."; break;
                case 6 : $error = "User profile request failed. Most likely the user is not connected to the provider and he should to authenticate again.";
                    $adapter->logout();
                    break;
                case 7 : $error = "User not connected to the provider.";
                    $adapter->logout();
                    break;
            }

            // well, basically your should not display this to the end user, just give him a hint and move on..
            $error .= "<br /><br /><b>Original error message:</b> " . $e->getMessage();
            $error .= "<hr /><pre>Trace:<br />" . $e->getTraceAsString() . "</pre>";
            $this->ajaxResponse(array(), 0, $error);
        }

        $profile->method=$this->request()->get('method');
        $profile->enabled=true;

            $player = new Player();
            $player->setEmail($this->session->get(Player::IDENTITY)?$this->session->get(Player::IDENTITY)->getEmail():$profile->email)
                ->setSocialId($profile->identifier)
                ->setSocialName($provider)
                ->setSocialEmail($profile->email);
            $loggedIn = false;
            try {
                $player->fetch();

                if(!$player->getName() AND $profile->firstName)
                    $player->setName($profile->firstName);

                if(!$player->getSurname() AND $profile->lastName)
                    $player->setSurname($profile->lastName);

                if(!$player->getValid() AND $profile->email AND $player->getEmail()==$profile->email)
                    $player->setValid(true);

                if(!$this->session->has(Player::IDENTITY))
                    $player->setDateLastLogin(time());

                // try to catch avatar
                if ($profile->photoURL AND !$player->getAvatar())
                    $player->uploadAvatar($profile->photoURL);

                if(!array_key_exists($provider, $player->getAdditionalData()) AND !$player->isSocialUsed())
                    $player->addPoints(Player::SOCIAL_PROFILE_COST, 'Бонус за привязку социальной сети '.$provider);

                if(!$_COOKIE[Player::PLAYERID_COOKIE])
                    setcookie(Player::PLAYERID_COOKIE, $player->getId(), time() + Player::AUTOLOGIN_COOKIE_TTL, '/');

                $player->updateSocial()
                    ->setLastIp(Common::getUserIp())
                    ->setCookieId(($_COOKIE[Player::PLAYERID_COOKIE]?:$player->getId()))
                    ->setAdditionalData(array($provider=>array_filter(get_object_vars($profile))))
                    ->update();

                $loggedIn = true;

            } catch (EntityException $e) {
                if ($e->getCode() == 500) {
                    // fetch more than one player
                }
                if ($e->getCode() == 404) {

                    try {
                        $geoReader =  new Reader(PATH_MMDB_FILE);
                        $country = $geoReader->country(Common::getUserIp())->country;
                        $player->setCountry($country->isoCode);
                    } catch (\Exception $e) {
                        if($profile->country){
                            $player->setCountry($profile->country);
                        }
                        else {
                            $player->setCountry(Config::instance()->defaultLang);
                        }
                    }

                    try {
                        $player->setIp(Common::getUserIp())
                            ->setHash('')
                            ->setName($profile->firstName)
                            ->setSurname($profile->lastName)
                            ->setAdditionalData(
                                array($provider=>array_filter(get_object_vars($profile)))
                            );

                        if ($ref = $this->request()->get('ref')) {
                            $player->setReferalId((int)$ref);
                        }

                        if($profile->email) {

                            $player->setValid(true)
                                ->setDateLastLogin(time())
                                ->create();

                            if(!$player->isSocialUsed()) // If Social Id didn't use earlier
                                $player->addPoints(Player::SOCIAL_PROFILE_COST, 'Бонус за регистрацию через социальную сеть ' . $provider);

                            $player->updateSocial()
                                ->payInvite()
                                ->payReferal()
                                ->markOnline();
                            $loggedIn = true;

                            // try to catch avatar
                            if ($profile->photoURL)
                                $player->uploadAvatar($profile->photoURL);

                            if ($player->getId() <= 1000) {
                                $player->addPoints(300, 'Бонус за регистрацию в первой тысяче участников');
                            }

                        }
                        else{
                            $this->session->set('SOCIAL_IDENTITY', $player);
                        }

                    } catch (EntityException $e) {
                        // do nothing
                    }

                }
            }

            if ($loggedIn === true) {
                $this->session->set(Player::IDENTITY, $player);

            }


        $this->redirect('/');

    }


}