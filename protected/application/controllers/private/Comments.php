<?php
namespace controllers\admin;
use \Session2, \Application, \EntityException, \LotterySettings, \LotterySettingsException, \LotterySettingsModel, \CommentsModel, \Comment, \WideImage, \Admin, \Config;

Application::import(PATH_CONTROLLERS . 'private/PrivateArea.php');
Application::import(PATH_APPLICATION . '/model/models/CommentsModel.php');
Application::import(PATH_APPLICATION . '/model/entities/Comment.php');
Application::import(PATH_PROTECTED . '/external/wi/WideImage.php');

class Comments extends \PrivateArea
{
    public $activeMenu = 'comments';

    public function init()
    {
        parent::init();

        if (!Config::instance()->rights[Session2::connect()->get(Admin::SESSION_VAR)->getRole()][$this->activeMenu]) {
            $this->redirect('/private');
        }
    }

    public function indexAction()
    {
        $comments = CommentsModel::instance()->getList();

        $this->render('admin/comments', array(
            'title'      => 'Комментарии',
            'layout'     => 'admin/layout.php',
            'activeMenu' => $this->activeMenu,
            'comments'   => $comments,
        ));
    }

    public function saveAction()
    {
        $comment = new Comment();
        $comment->setAuthor($this->request()->post('author'))
                ->setLink($this->request()->post('link'))
                ->setDate($this->request()->post('date') ? strtotime($this->request()->post('date')) : time())
                ->setText(nl2br($this->request()->post('text')));

        $image = WideImage::loadFromUpload('avatar');
        if ($image) {
            $image = $image->resize(50, 50);
            $image = $image->crop("center", "center", 50, 50);    
            
            $imageName = uniqid() . ".jpg";
            $image->saveToFile(PATH_FILESTORAGE .  'avatars/comments/' . $imageName, 100);

            $comment->setAvatar($imageName);
        }
        
        try {
            $comment->create();
        } catch (EntityException $e) {}

        $this->redirect('/private/comments');
    }

    public function deleteAction($id)
    {
        $comment = new Comment();
        $comment->setId($id)->fetch();

        try {
            if ($comment->getAvatar()) {
                @unlink(PATH_FILESTORAGE .  'avatars/comments/' . $comment->getAvatar());                
            }
            $comment->delete();
        } catch (EntityException $e) {

        }

        $this->redirect('/private/comments');   
    }
}