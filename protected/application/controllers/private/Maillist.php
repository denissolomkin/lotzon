<?php
namespace controllers\admin;

use \Application, \PrivateArea, \MaillistModel, \MaillistMessage, \MaillistTask, \MaillistTemplate, \Session2, \Admin, \WideImage, \PlayersModel;

Application::import(PATH_CONTROLLERS . 'private/PrivateArea.php');
Application::import(PATH_APPLICATION . '/model/models/MaillistModel.php');
Application::import(PATH_APPLICATION . '/model/entities/MaillistTask.php');
Application::import(PATH_APPLICATION . '/model/entities/MaillistMessage.php');
Application::import(PATH_APPLICATION . '/model/entities/MaillistTemplate.php');


class Maillist extends PrivateArea
{
    public $activeMenu = 'maillist';

    public function getTaskStatisticPlayerGamesAction($identifier)
    {
        if ($this->request()->isAjax()) {
            $response = array();
            try {
                $bars = MaillistModel::instance()->getTaskStatisticPlayerGames($identifier);
                $response = array(
                    'status'   => 1,
                    'message'  => 'OK',
                    'data'     => $bars
                );
            } catch (EntityException $e) {
                $response['status']  = 0;
                $response['message'] = $e->getCode();
            }

            die(json_encode($response));
        }

        $this->redirect('/private');
    }

    public function getTaskFilterCountAction()
    {
        $messageId   = $this->request()->post('messageId',0);
        $settings    = $this->request()->post('settings','');

        if ($this->request()->isAjax()) {
            $response = array();
            try {
                $message = new \MaillistTask();
                $message->setMessageId($messageId)
                        ->setSettings($settings);
                $response = array(
                    'status'   => 1,
                    'message'  => 'OK',
                    'data'     => array(
                        'count' => $message->getCountEmails(),
                    ),
                );
            } catch (EntityException $e) {
                $response['status']  = 0;
                $response['message'] = $e->getCode();
            }

            die(json_encode($response));
        }

        $this->redirect('/private');
    }

    public function listTasksAction()
    {

        /**
         * Filter for showing list of tasks
         * @var string
         */
        $status = $this->request()->get('status', 'all');

        $tasks = MaillistModel::instance()->getTaskList($status);
        $this->render('admin/maillist_tasks', array(
            'title'      => 'Email рассылка -> Задания',
            'layout'     => 'admin/layout.php',
            'activeMenu' => $this->activeMenu,
            'tasks'      => $tasks,
            'status'     => $status,
        ));
    }

    public function listMessagesAction()
    {
        $messages = MaillistModel::instance()->getMessageList();
        $this->render('admin/maillist_messages', array(
            'title'      => 'Email рассылка -> Шаблоны',
            'layout'     => 'admin/layout.php',
            'activeMenu' => $this->activeMenu,
            'messages'   => $messages,
        ));
    }

    public function deleteMessageAction($identifier)
    {
        if ($this->request()->isAjax()) {
            $response = array(
                'status'  => 1,
                'message' => 'OK',
                'data'    => array(),
            );

            try {
                $message = new MaillistMessage();
                $message->setId($identifier)->delete();
            } catch (EntityException $e) {
                $response['status']  = 0;
                $response['message'] = $e->getCode();
            }

            die(json_encode($response));
        }

        $this->redirect('/private');
    }

    public function deleteTaskAction($identifier)
    {
        if ($this->request()->isAjax()) {
            $response = array(
                'status'  => 1,
                'message' => 'OK',
                'data'    => array(),
            );

            try {
                 $task = MaillistModel::instance()->getTask($identifier);
                if ($task->getStatus()=='archived') {
                    $task->delete();
                } else {
                    $task->archive();
                }
            } catch (EntityException $e) {
                $response['status']  = 0;
                $response['message'] = $e->getCode();
            }

            die(json_encode($response));
        }

        $this->redirect('/private');
    }

    public function getTemplatePreviewAction($identifier)
    {
        if ($this->request()->isAjax()) {
            $response = array();
            try {
                $template = MaillistModel::instance()->getTemplate($identifier);
                $response = array(
                    'status'   => 1,
                    'message'  => 'OK',
                    'data'     => array(
                        'template' => $template->getArray(),
                        'preview'  => $template->getPreviewHTML(),
                    ),
                );
            } catch (EntityException $e) {
                $response['status']  = 0;
                $response['message'] = $e->getCode();
            }

            die(json_encode($response));
        }

        $this->redirect('/private');
    }

    public function getTaskAction($identifier)
    {
        $list = MaillistModel::instance()->getTaskList();
        if($text = $list[$identifier]) {
            $response = array(
                'status'   => 1,
                'message'  => 'OK',
                'data'     => $text->getArray(),
                'messages' => $this->objectsToArray(MaillistModel::instance()->getMessageList()),
                'filters'  => MaillistTask::$FILTERS,
                'events'   => MaillistTask::$EVENTS,
            );
        } else {
            $task = new MaillistTask();
            $response = array(
                'status'   => 1,
                'message'  => 'OK',
                'data'     => $task->getArray(),
                'messages' => $this->objectsToArray(MaillistModel::instance()->getMessageList()),
                'filters'  => MaillistTask::$FILTERS,
                'events'   => MaillistTask::$EVENTS,
            );
        }
        die(json_encode($response));
    }

    public function getMessageAction($identifier)
    {
        $list = MaillistModel::instance()->getMessageList();
        if($text = $list[$identifier]) {
            $response = array(
                'status'    => 1,
                'message'   => 'OK',
                'data'      => array(
                    'message'   => $text->getArray(),
                    'templates' => $this->objectsToArray(MaillistModel::instance()->getTemplateList()),
                )
            );
        } else {
            $message = new MaillistMessage();
            $response = array(
                'status'  => 1,
                'message' => 'OK',
                'data'    =>
                    array(
                        'message'   => $message->getArray(),
                        'templates' => $this->objectsToArray(MaillistModel::instance()->getTemplateList()),
                    ));
        }
        die(json_encode($response));
    }

    public function saveTaskAction()
    {
        if ($this->request()->isAjax()) {
            $response = array(
                'status'  => 1,
                'message' => 'OK',
                'data'    => array(),
            );

            $id          = $this->request()->post('id');
            $description = $this->request()->post('description');
            $messageId   = $this->request()->post('messageId');
            $schedule    = $this->request()->post('schedule')=="true"?true:false;
            $settings    = $this->request()->post('settings');
            $enable      = $this->request()->post('enable')=="true"?true:false;

            $message = new \MaillistTask();
            $message->setId($id)
                ->setDescription($description)
                ->setMessageId($messageId)
                ->setSchedule($schedule)
                ->setSettings($settings)
                ->setEnable($enable);

            if ($enable==false) {
                $message->setStatus("disable");
            } else {
                $message->setStatus("waiting");
            }

            try {
                if ($message->getId()>0) {
                    $message->update();
                } else {
                    $message->create();
                }

            } catch (EntityException $e) {
                $response['status'] = 0;
                $response['message'] = $e->getMessage();
            }

            die(json_encode($response));
        }

        $this->redirect('/private');
    }

    public function saveMessageAction()
    {
        if ($this->request()->isAjax()) {
            $response = array(
                'status'  => 1,
                'message' => 'OK',
                'data'    => array(),
            );

            $id          = $this->request()->post('id');
            $description = $this->request()->post('description');
            $templateId  = $this->request()->post('templateId');
            $values      = $this->request()->post('values');
            $settings    = $this->request()->post('settings');

            $message = new \MaillistMessage();
            $message->setId($id)
                    ->setDescription($description)
                    ->setTemplateId($templateId)
                    ->setValues($values)
                    ->setSettings($settings);

            try {
                if ($message->getId()>0) {
                    $message->update();
                } else {
                    $message->create();
                }

            } catch (EntityException $e) {
                $response['status'] = 0;
                $response['message'] = $e->getMessage();
            }

            die(json_encode($response));
        }

        $this->redirect('/private');
    }

    private function objectsToArray($arr)
    {
        $ret = array();
        foreach ($arr as $key=>$value) {
            $ret[$key] = $value->getArray();
        }
        return $ret;
    }
}
