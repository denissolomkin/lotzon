<?php

use \Player;

Application::import(PATH_APPLICATION . 'model/Entity.php');

class MaillistMessage extends Entity
{
    static $MACROS = array(
        '%unsubscribe%' => array(
            'description' => 'ссылка отписывания от рассылки',
        ),
        '%nickname%'   => array(
            'description' => 'ник',
        ),
    );

    private $_id          = 0;
    private $_description = '';
    private $_templateId  = 0;
    private $_values      = array();
    private $_settings    = array();

    public function init()
    {
        $this->setModelClass('MaillistModel');
    }

    public function setId($id)
    {
        $this->_id = (int)$id;
        return $this;
    }

    public function getId()
    {
        return (int)$this->_id;
    }

    public function setDescription($description)
    {
        $this->_description = $description;
        return $this;
    }

    public function getDescription()
    {
        return $this->_description;
    }

    public function setTemplateId($templateId)
    {
        $this->_templateId = (int)$templateId;
        return $this;
    }

    public function getTemplateId()
    {
        return (int)$this->_templateId;
    }

    public function setValues($values)
    {
        $this->_values = $values;
        return $this;
    }

    public function getValues()
    {
        return $this->_values;
    }

    public function setSettings($settings)
    {
        $this->_settings = $settings;
        return $this;
    }

    public function getSettings()
    {
        return $this->_settings;
    }

    public function formatFrom($from, $data)
    {
        if ($from == 'DB') {
            $this->setId($data['Id'])
                ->setDescription($data['Description'])
                ->setTemplateId($data['TemplateId'])
                ->setValues(unserialize($data['Values']))
                ->setSettings(unserialize($data['Settings']));
        }
        return $this;
    }

    public function getArray()
    {
        return array(
            'Id'          => $this->getId(),
            'Description' => $this->getDescription(),
            'TemplateId'  => $this->getTemplateId(),
            'Values'      => $this->getValues(),
            'Settings'    => $this->getSettings()
        );
    }

    public function delete()
    {
        $model = $this->getModelClass();
        $model::instance()->deleteMessage($this);
    }

    public function create()
    {
        $model = $this->getModelClass();
        $model::instance()->createMessage($this);
    }

    public function update()
    {
        $model = $this->getModelClass();
        $model::instance()->updateMessage($this);
    }

    /**
     * Render template with variables values of language $lang or default language if $lang==false
     * If values for $lang not found return false
     *
     * @param  string|false $lang
     *
     * @return array|bool
     */
    public function renderTemplate($lang = false)
    {
        $template  = MaillistModel::instance()->getTemplate($this->getTemplateId());
        $html      = $template->getHTML();
        $variables = $template->getVariables();
        $values    = $this->getValues();
        $settings  = $this->getSettings();

        if (isset($values[$lang])) {
            $language = $lang;
        } elseif(isset($values[$settings['defaultLanguage']])) {
            $language = $settings['defaultLanguage'];
        } else {
            return false;
        }

        foreach($variables as $key=>$variable) {
            if (isset($values[$language][$key])) {
                $html = str_replace('%'.$key.'%', $values[$language][$key], $html);
            } else {
                if (isset($variables[$key]["default"][$language])) {
                    $html = str_replace('%' . $key . '%', $variables[$key]["default"][$language], $html);
                } else {
                    $html = str_replace('%' . $key . '%', '', $html);
                }
            }
        }

        if (isset($values[$language]['header'])) {
            $header = $values[$language]['header'];
        } else {
            $header = '';
        }

        return array(
            'header' => $header,
            'html'   => $html
        );
    }

    /**
     * Render message with replace macros
     *
     * @param int          $playerId
     * @param string|false $lang
     * @param array|false  $renderTemplate If set - using this render html template
     *
     * @return array|bool
     * @throws EntityException
     */
    public function render($playerId = 0, $lang = false, $renderTemplate = false)
    {
        if ($renderTemplate===false) {
            $render = $this->renderTemplate($lang);
        } else {
            $render = $renderTemplate;
        }
        if ($playerId>0) {
            $player = new \Player;
            $player->setId($playerId)->fetch();
        }
        foreach (MaillistMessage::$MACROS as $macros => $param) {
            if ($playerId==0) {
                $render['html'] = str_replace($macros, '', $render['html']);
            } else {
                switch ($macros) {
                    case '%unsubscribe%':
                        $render['html'] = str_replace($macros, 'http://lotzon.com/unsubscribe/?email='.$player->getEmail().'&hash='.$player->getSalt(), $render['html']);
                        break;
                    case '%nickname%':
                        $render['html'] = str_replace($macros, $player->getNicName(), $render['html']);
                        break;
                    default:
                        $render['html'] = str_replace($macros, '', $render['html']);
                }
            }
        }
        return $render;
    }

}