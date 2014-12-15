<?php
namespace controllers\production;
use \Application, \Config, \Player, \EntityException, \Session2, \EmailInvites, \EmailInvite, \Common;

Application::import(PATH_CONTROLLERS . 'production/AjaxController.php');

class InvitesController extends \AjaxController
{
    public function init()
    {
        parent::init();
        if ($this->validRequest()) {
            if (!Session2::connect()->get(Player::IDENTITY) instanceof PLayer) {
                $this->ajaxResponse(array(), 0, 'NOT_AUTHORIZED');
            }    
            Session2::connect()->get(Player::IDENTITY)->markOnline();
        }
    }

    public function emailInviteAction()
    {
        $email = $this->request()->post('email');

        $invite = new EmailInvite();
        $invite->setEmail($email)
               ->setInviter(Session2::connect()->get(Player::IDENTITY));

        try {
            $invite->create();
            Session2::connect()->get(Player::IDENTITY)->decrementInvitesCount();

            Common::sendEmail($invite->getEmail(), 'Приглашение на www.lotzon.com', 'player_invite', array(
                'ivh'  => $invite->getHash(),
                'inviter' => $invite->getInviter(),
            ));
        } catch (EntityException $e) {
            $this->ajaxResponse(array(), 0, $e->getMessage());
        }

        $this->ajaxResponse(array(
            'invitesCount' => Session2::connect()->get(Player::IDENTITY)->getInvitesCount(),
        ));
    }
}