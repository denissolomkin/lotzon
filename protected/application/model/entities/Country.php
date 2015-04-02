<?php

Application::import(PATH_APPLICATION . 'model/Entity.php');

class Country extends Entity
{
    private $_id          = 0;
    private $_code        = 'RU';
    private $_lang        = 'RU';
    private $_currency    = '';
    private $_countries   = array('AF','AX','AL','DZ','AS','AD','AO','AI','AQ','AG','AR','AM','AW','AU','AT','AZ','BS','BH','BD','BB','BY','BE','BZ','BJ','BM','BT','BO','BQ','BA','BW','BV','BR','IO','BN','BG','BF','BI','KH','CM','CA','CV','KY','CF','TD','CL','CN','CX','CC','CO','KM','CG','CD','CK','CR','CI','HR','CU','CW','CY','CZ','DK','DJ','DM','DO','EC','EG','SV','GQ','ER','EE','ET','FK','FO','FJ','FI','FR','GF','PF','TF','GA','GM','GE','DE','GH','GI','GR','GL','GD','GP','GU','GT','GG','GN','GW','GY','HT','HM','VA','HN','HK','HU','IS','IN','ID','IR','IQ','IE','IM','IL','IT','JM','JP','JE','JO','KZ','KE','KI','KP','KR','KW','KG','LA','LV','LB','LS','LR','LY','LI','LT','LU','MO','MK','MG','MW','MY','MV','ML','MT','MH','MQ','MR','MU','YT','MX','FM','MD','MC','MN','ME','MS','MA','MZ','MM','NA','NR','NP','NL','NC','NZ','NI','NE','NG','NU','NF','MP','NO','OM','PK','PW','PS','PA','PG','PY','PE','PH','PN','PL','PT','PR','QA','RE','RO','RU','RW','BL','SH','KN','LC','MF','PM','VC','WS','SM','ST','SA','SN','RS','SC','SL','SG','SX','SK','SI','SB','SO','ZA','GS','SS','ES','LK','SD','SR','SJ','SZ','SE','CH','SY','TW','TJ','TZ','TH','TL','TG','TK','TO','TT','TN','TR','TM','TC','TV','UG','UA','AE','GB','US','UM','UY','UZ','VU','VE','VN','VG','VI','WF','EH','YE','ZM','ZW');

 
    public function init()
    {
        $this->setModelClass('CountriesModel');
    }

    public function setCode($char)
    {
        $this->_code = $char;

        return $this;
    }

    public function getCode()
    {
        return $this->_code;
    }

    public function setId($int)
    {
        $this->_id = (int) $int;

        return $this;
    }

    public function getId()
    {
        return $this->_id;
    }


    public function setLang($char)
    {
        $this->_lang = $char;

        return $this;
    }

    public function getLang()
    {
        return $this->_lang;
    }

    public function setCurrency($array) 
    {
        $this->_currency = $array;

        return $this;
    }

    public function getCurrency()
    {
        return $this->_currency;
    }

    public function loadCurrency()
    {
        $currency = new Currency();
        try {
            $currency->setId($this->getCurrency())->fetch();
        } catch (EntityException $e) {
        }
        return $currency;
    }


    public function validate($event, $params = array())
    {
        switch ($event) {
            case 'update' :

            break;

            case 'delete' :

            break;

            case 'fetch' :

            break;

            case 'create' :
                $this->isValidCode();
                $this->isValidCurrency();
                $this->isValidLang();
            break;
            default:
                throw new EntityException("Object does not pass validation", 400);
            break;
        }

        return true;
    }

    private function isValidCode($throwException = true)
    {
        if ($this->getCode() && preg_match('/^('.(implode('|',$this->_countries)).')$/i', $this->getCode())) {
            return true;
        }

        if ($throwException) {
            throw new EntityException('Invalid country code format', 400);
        }

        return false;
    }

    private function isValidCurrency($throwException = true)
    {
        if ($this->getCurrency()) {
            return true;
        }

        if ($throwException) {
            throw new EntityException('Empty currency', 400);
        }

        return false;
    }

    private function isValidLang($throwException = true) 
    {
        if ($this->getLang() && in_array($this->getLang(), $this->_countries)) {
            return true;
        }

        if ($throwException) {
            throw new EntityException('Empty or invalid country lang setting', 400);
        }

        return false;   
    }


    public function formatFrom($from, $data) {
        switch ($from) {
            case 'DB' :
                $this->setId($data['Id'])
                     ->setCode($data['Code'])
                     ->setLang($data['Lang'])
                     ->setCurrency($data['Currency']);
            break;
        }

        return $this;
    }
}