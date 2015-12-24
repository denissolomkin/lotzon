<?php

class GameApp extends Entity
{
    protected $_id              = 0;
    protected $_uid             = null;
    protected $_key             = null;
    protected $_mode            = null;
    protected $_players         = null;
    protected $_requiredPlayers = null;
    protected $_variation       = null;
    protected $_over            = null;
    protected $_app            = null;
    protected $_saved           = null;
    protected $_run             = null;
    protected $_ping            = 0;

    public function init()
    {
        $this->setModelClass('GameAppsModel');
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

    public function formatFrom($from, $data)
    {
        switch ($from) {
            case 'DB':
                $this
                    ->setKey($data['Key'])
                    ->setId($data['Id'])
                    ->setUid($data['Uid'])
                    ->setMode($data['Mode'])
                    ->setPlayers(@unserialize($data['Players']))
                    ->setRequiredPlayers($data['RequiredPlayers'])
                    ->setApp(@unserialize($data['AppData']))
                    ->setOver($data['IsOver'])
                    ->setSaved($data['IsSaved'])
                    ->setRun($data['IsRun'])
                    ->setPing($data['Ping']);
                break;

            case 'app':
                $this
                    ->setKey($data->getKey())
                    ->setId($data->getId())
                    ->setUid($data->getIdentifier())
                    ->setMode($data->getMode())
                    ->setPlayers($data->getClients())
                    ->setRequiredPlayers($data->getNumberPlayers())
                    ->setApp($data)
                    ->setOver($data->isOver())
                    ->setSaved($data->isSaved())
                    ->setRun($data->isRun())
                    ->setPing(null);
                break;

        }

        return $this;
    }
}
