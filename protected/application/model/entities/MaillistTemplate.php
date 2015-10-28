<?php
Application::import(PATH_APPLICATION . 'model/Entity.php');

class MaillistTemplate extends Entity
{
    private $_id          = 0;
    private $_fileName    = '';
    private $_variables   = array();
    private $_description = '';

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

    public function setFileName($filename)
    {
        $this->_fileName = $filename;
        return $this;
    }

    public function getFileName()
    {
        return $this->_fileName;
    }

    public function setVariables($variables)
    {
        $this->_variables = $variables;
        return $this;
    }

    public function getVariables()
    {
        return $this->_variables;
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

    public function formatFrom($from, $data)
    {
        if ($from == 'DB') {
            $this->setId($data['Id'])
                 ->setFileName($data['FileName'])
                 ->setVariables(unserialize($data['Variables']))
                 ->setDescription($data['Description']);
        }
        return $this;
    }

    public function getArray()
    {
        return array(
            'Id'          => $this->getId(),
            'FileName'    => $this->getFileName(),
            'Variables'   => $this->getVariables(),
            'Description' => $this->getDescription()
        );
    }

    public function getPreviewHTML()
    {
        $html = $this->getHTML();

        $from = strpos($html,'<body');
        $to   = strpos($html,'</body');

        $html = '<div style="margin-bottom:5px;">%header%</div><div><div'.substr($html,$from+5,$to-$from-5).'</div>';

        return $html;
    }

    public function getHTML()
    {
        if (file_exists(PATH_TEMPLATES . 'emails/' . $this->_fileName)) {
            ob_start();
            include(PATH_TEMPLATES . 'emails/' . $this->_fileName);
            $html = ob_get_clean();
        }

        return $html;
    }

}