<?php

class GameStack extends Entity
{
    protected $_app          = array();
    protected $_appId        = null;
    protected $_appUid       = null;
    protected $_appKey       = null;
    protected $_appName      = null;
    protected $_appMode      = null;
    protected $_appVariation = null;
    protected $_ping         = 0;

    public function init()
    {
        $this->setModelClass('GameStacksModel');
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
            throw new EntityException($e->getMessage(), $e->getCode());
        }

        return $this;
    }

    public function formatFrom($data, $from = 'DB')
    {
        if ($from == 'DB') {
            $this
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
        }

        return $this;
    }
}
