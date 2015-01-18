<?php
namespace controllers\admin;

use \Application, \PrivateArea, \StaticSiteText, \StaticSiteTextsModel, \Config, \Session2, \Admin;

Application::import(PATH_CONTROLLERS . 'private/PrivateArea.php');
Application::import(PATH_APPLICATION . '/model/models/StaticSiteTextsModel.php');
Application::import(PATH_APPLICATION . '/model/entities/StaticSiteText.php');

class Texts extends PrivateArea 
{
    public $activeMenu = 'texts';

    public function init()
    {
        parent::init();

        if (!Config::instance()->rights[Session2::connect()->get(Admin::SESSION_VAR)->getRole()][$this->activeMenu]) {
            $this->redirect('/private');
        }
    }

    public function indexAction()
    {
        $list = StaticSiteTextsModel::instance()->getListGroupedByIdentifier();

        $this->render('admin/texts', array(
            'title'      => 'Тексты на сайте',
            'layout'     => 'admin/layout.php',
            'activeMenu' => $this->activeMenu,
            'list'       => $list,
        ));
    }

    public function saveAction()
    {
        if ($this->request()->isAjax()) {
            $response = array(
                'status'  => 1,
                'message' => 'OK',
                'data'    => array(),
            );

            $id = $this->request()->post('identifier');
            $texts = $this->request()->post('text');

            if (count($texts)) {
                foreach ($texts as $lang => $text) {
                    $object = new StaticSiteText();
                    $object->setLang($lang)
                           ->setText($text)
                           ->setId($id);

                    try {
                        $object->create();
                    } catch (EntityException $e) {
                        $response['status'] = 0;
                        $response['message'] = $e->getMessage();

                        break;
                    }   
                }
            } else {
                $response['status'] = 0;
                $response['message'] = 'Unexpected server error';
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
                $text = new StaticSiteText();
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