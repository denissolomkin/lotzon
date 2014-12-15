<?php
namespace controllers\production;
use \Config,  \Hybrid_Auth, \Player, \EntityException, \Session2, \WideImage,  \Common;
use \GeoIp2\Database\Reader;

class AuthController extends \SlimController\SlimController {

    public function __construct(\Slim\Slim &$app)
    {
        parent::__construct($app);
        $this->init();
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



        if ($profile->email) {
            $player = new Player();
            $player->setEmail($profile->email);
            $loggedIn = false;
            try {
                $player->fetch();
                $loggedIn = true;
            } catch (EntityException $e) {
                if ($e->getCode() == 404) {

                    try {
                        if($profile->country){
                            $player->setCountry($profile->country);
                        }
                        else{
                            $geoReader =  new Reader(PATH_MMDB_FILE);
                            $country = $geoReader->country(Common::getUserIp())->country;
                            $player->setCountry($country->isoCode);
                        }

                    } catch (\Exception $e) {
                        $player->setCountry(Config::instance()->defaultLang);
                    }

                    try {
                        $player->setIp(Common::getUserIp())
                            ->setHash('')
                            ->setValid(true)
                            ->setName($profile->firstName)
                            ->setSurname($profile->lastName)
                            ->setAdditionalData(
                                array(
                                    $provider => $profile->additionalData
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
                        if ($profile->photoURL) {
                            try {
                                $image = WideImage::load($profile->photoURL);
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

            if ($loggedIn === true) {
                Session2::connect()->set(Player::IDENTITY, $player);

            }
        }

        $this->redirect('/');

    }


}