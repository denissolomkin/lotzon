<?php

class GamePlayer extends Entity
{
    protected $_id           = 0;
    protected $_lang         = '';
    protected $_country      = '';
    protected $_name         = '';
    protected $_avatar       = '';
    protected $_bot          = null;
    protected $_admin        = null;
    protected $_app          = array();
    protected $_appId        = null;
    protected $_appKey       = null;
    protected $_appName      = null;
    protected $_appMode      = null;
    protected $_appVariation = null;
    protected $_appUid       = null;
    protected $_ping         = 0;

    public function init()
    {
        $this->setModelClass('GamePlayersModel');
    }

    public function validate()
    {
        return true;
    }

    public function fetch()
    {
        try {
            $model = $this->getModelClass();
            $model::instance()->fetch($this);
        } catch (ModelException $e) {
            if ($e->getCode() == 404) {
                return false;
            } else
                throw new EntityException($e->getMessage(), $e->getCode());
        }

        return $this;
    }

    public function update()
    {

        $this->setPing(time());

        try {
            $model = $this->getModelClass();
            $model::instance()->update($this);
        } catch (ModelException $e) {
            echo $e->getMessage(). $e->getCode();
        }

        return $this;
    }

    public function formatFrom($from = 'DB', $data)
    {
        if ($from == 'DB') {
            $this->setId($data['PlayerId'])
                ->setLang($data['Lang'])
                ->setCountry($data['Country'])
                ->setName($data['Name'])
                ->setAvatar($data['Avatar'])
                ->setAdmin($data['Admin'])
                ->setBot($data['Bot'])
                ->setApp(array(
                    'Name' => $data['AppName'],
                    'Key'  => $data['AppName'],
                    'Mode' => $data['AppMode'],
                    'Id'   => $data['AppId'],
                    'Uid'  => $data['AppUid']))
                ->setAppName($data['AppName'])
                ->setAppId($data['AppId'])
                ->setAppUid($data['AppUid'])
                ->setAppMode($data['AppMode'])
                ->setPing($data['Ping']);

        } else if ($from == 'player') {

            $this->setId($data->getId())
                ->setCountry($data->getCountry())
                ->setLang($data->getLang())
                ->setName($data->getNicname())
                ->setAvatar($data->getAvatar())
                ->setAdmin($data->isAdmin());

        } else if ($from == 'bot') {

            $this->setId($data->id)
                ->setCountry('RU')
                ->setLang('RU')
                ->setName($data->name)
                ->setAvatar($data->avatar)
                ->setBot(1);
        }

        return $this;
    }

    public function export($to)
    {
        $ret = null;

        switch ($to) {
            case 'player':
                $ret = (object)array(
                    'time'    => time(),
                    'id'      => $this->getId(),
                    'avatar'  => $this->getAvatar(),
                    'lang'    => $this->getLang(),
                    'country' => $this->getCountry(),
                    'admin'   => $this->isAdmin(),
                    'name'    => $this->getName());
                break;
        }

        return $ret;


    }
}
