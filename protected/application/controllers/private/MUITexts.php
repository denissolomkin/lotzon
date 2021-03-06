<?php
namespace controllers\admin;
use \PrivateArea, \StaticText, \StaticTextsModel, \SettingsModel, \Session2, \Admin;

class StaticTexts extends PrivateArea
{
    public $activeMenu = 'statictexts';

    public function init()
    {
        parent::init();

        if(!array_key_exists($this->activeMenu, SettingsModel::instance()->getSettings('rights')->getValue(Session2::connect()->get(Admin::SESSION_VAR)->getRole())))
            $this->redirect('/private');

    }

    public function indexAction()
    {
        $curCategory = $this->request()->get('category', false);
        $list = StaticTextsModel::instance()->getCategory($curCategory);

        $this->render('admin/statictexts', array(
            'title'      => 'Тексты на сайте',
            'layout'     => 'admin/layout.php',
            'activeMenu' => $this->activeMenu,
            'curCategory'=> $curCategory,
            'frontend'   => 'statictexts',
            'list'       => $list,
        ));
    }

    public function getAction($key=null)
    {
        $list = StaticTextsModel::instance()->getList();
        if($text = $list[$key])
            $response = array(
                'status'  => 1,
                'message' => 'OK',
                'data'    =>
                    array(
                        'id'  => $text->getId(),
                        'key'  => $text->getKey(),
                        'category'  => $text->getCategory(),
                        'texts'    => $text->getText()
                    ));
        else
            $response = array(
                'status'  => 1,
                'message' => 'OK',
                'data'    =>
                    array(
                        'key'  => $key,
                    ));

        die(json_encode($response));
    }

    public function saveAction()
    {
        if ($this->request()->isAjax()) {
            $response = array(
                'status'  => 1,
                'message' => 'OK',
                'data'    => array(),
            );

            $id = $this->request()->post('id');
            $key = $this->request()->post('key');
            $category = $this->request()->post('category');
            $texts = array_filter($this->request()->post('texts'));

            $object = new StaticText();
            $object->setCategory($category)
                ->setKey($key)
                ->setId($id);

            if (count($texts)) {
                foreach ($texts as $key => $text) {

                    if($text)
                        $object->addText($key, $text);

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

    }

    public function deleteAction($id)
    {
        if ($this->request()->isAjax()) {
            $response = array(
                'status'  => 1,
                'message' => 'OK',
                'data'    => array(),
            );

            try {
                $text = new StaticText();
                $text->setId($id)->delete();
            } catch (EntityException $e) {
                $response['status'] = 0;
                $response['message'] = $e->getCode();
            }
            
            die(json_encode($response));
        }

        $this->redirect('/private');
    }

}