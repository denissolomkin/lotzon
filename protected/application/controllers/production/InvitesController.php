<?php
namespace controllers\production;
use \Application, \Config, \Player, \EntityException, \Session, \EmailInvites, \EmailInvite, \Common;

Application::import(PATH_CONTROLLERS . 'production/AjaxController.php');

class InvitesController extends \AjaxController
{
    public function init()
    {
        parent::init();
        if ($this->validRequest()) {
            if (!Session::connect()->get(Player::IDENTITY) instanceof PLayer) {
                $this->ajaxResponse(array(), 0, 'NOT_AUTHORIZED');
            }    
            Session::connect()->get(Player::IDENTITY)->markOnline();
        }
    }

    public function emailInviteAction()
    {
        $email = $this->request()->post('email');

        $invite = new EmailInvite();
        $invite->setEmail($email)
               ->setInviter(Session::connect()->get(Player::IDENTITY));

        try {
            $invite->create();
            Session::connect()->get(Player::IDENTITY)->decrementInvitesCount();

            Common::sendEmail($invite->getEmail(), 'Приглашение на www.lotzon.com', 'player_invite', array(
                'ivh'  => $invite->getHash(),
                'inviter' => $invite->getInviter(),
            ));
        } catch (EntityException $e) {
            $this->ajaxResponse(array(), 0, $e->getMessage());
        }

        $this->ajaxResponse(array(
            'invitesCount' => Session::connect()->get(Player::IDENTITY)->getInvitesCount(),
        ));
    }
}