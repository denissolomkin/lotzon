<?php
namespace controllers\production;
use \Config,  \Hybrid_Auth;


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

            // set selected provider name
            // $provider = @ trim( strip_tags( $_GET["provider"] ) );

            // try to authenticate the selected $provider
            $adapter = $hybridauth->authenticate( $provider );

            $user_profile = $adapter->getUserProfile();
            echo "<pre>";
            print_r($user_profile);
            // if okey, we will redirect to user profile page
            die;
            $hybridauth->redirect( "/" );
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

    }


}