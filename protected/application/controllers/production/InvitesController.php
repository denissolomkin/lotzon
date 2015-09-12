<?php
namespace controllers\production;
use \Application, \Player, \EntityException, \EmailInvites, \EmailInvite, \Common;
use Symfony\Component\HttpFoundation\Session\Session;

Application::import(PATH_CONTROLLERS . 'production/AjaxController.php');

class InvitesController extends \AjaxController
{
    public function init()
    {
        $this->session = new Session();
        parent::init();
        if ($this->validRequest()) {
            if (!$this->session->get(Player::IDENTITY) instanceof PLayer) {
                $this->ajaxResponse(array(), 0, 'NOT_AUTHORIZED');
            }
        }
    }

    public function emailInviteAction()
    {
        $email = $this->request()->post('email');

        $invite = new EmailInvite();
        $invite->setEmail($email)
               ->setInviter($this->session->get(Player::IDENTITY));

        try {
            $invite->create();

            Common::sendEmail($invite->getEmail(), 'Приглашение на www.lotzon.com', 'player_invite', array(
                'ivh'  => $invite->getHash(),
                'inviter' => $invite->getInviter(),
            ));
        } catch (EntityException $e) {
            $this->ajaxResponse(array(), 0, $e->getMessage());
        }

        $this->ajaxResponse(array(
            'invitesCount' => $this->session->get(Player::IDENTITY)->getAvailableInvitesCount(),
        ));
    }
}