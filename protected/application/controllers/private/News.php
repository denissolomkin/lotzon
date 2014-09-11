<?php
namespace controllers\admin;

use \Application, \PrivateArea, \NewsModel, \Config;

Application::import(PATH_CONTROLLERS . 'private/PrivateArea.php');
Application::import(PATH_APPLICATION . '/model/models/NewsModel.php');
Application::import(PATH_APPLICATION . '/model/entities/News.php');

class News extends PrivateArea 
{
    const NEWS_PER_PAGE = 10;

    public $activeMenu = 'news';

    public function init()
    {
        parent::init();
    }

    public function indexAction($lang = '')
    {   
        if (empty($lang)) {
            $lang = Config::instance()->defaultLang;
        }
        $page = $this->request()->get('page', 1);

        $list = NewsModel::instance()->getList($lang, self::NEWS_PER_PAGE, $page == 1 ? 0 : self::NEWS_PER_PAGE * $page);
        $this->render('admin/news', array(
            'title'      => 'Новости',
            'layout'     => 'admin/layout.php',
            'activeMenu' => $this->activeMenu,
            'list'       => $list,
            'pageLang'   => $lang,
        ));
    }

    public function saveAction($lang)
    {
        if ($this->request()->isAjax()) {
            $response = array(
                'status'  => 1,
                'message' => 'OK',
                'data'    => array(),
            );

            $id = $this->request()->post('id', false);
            $title = $this->request()->post('title');
            $text = $this->request()->post('text');

            $newsObj = new \News();
            $newsObj->setId($id)
                    ->setTitle($title)
                    ->setText($text)
                    ->setLang($lang);

            try {
                if ($newsObj->getId()) {
                    $newsObj->update();
                } else {
                    $newsObj->create();
                }

            } catch (EntityException $e) {
                $response['status'] = 0;
                $response['message'] = $e->getMessage();

                break;
            }   

            die(json_encode($response));
        }

        $this->redirect('/private');
    }

    public function deleteAction($identifier) 
    {
        if ($this->request()->isAjax()) {
            $response = array(
                'status'  => 1,
                'message' => 'OK',
                'data'    => array(),
            );

            try {
                $text = new \News();
                $text->setId($identifier)->delete();    
            } catch (EntityException $e) {
                $response['status'] = 0;
                $response['message'] = $e->getCode();
            }
            
            die(json_encode($response));
        }

        $this->redirect('/private');
    }

}