<?php

Application::import(PATH_APPLICATION . 'model/Entity.php');

class SupportedCountry extends Entity
{
    private $_countryCode = '';
    private $_title       = '';
    private $_enabled     = '';
    private $_lang        = '';

 
    public function init()
    {
        $this->setModelClass('SupportedCountriesModel');
    }

    public function setCountryCode($cc) 
    {
        $this->_countryCode = $cc;

        return $this;
    }

    public function getCountryCode() 
    {
        return $this->_countryCode;
    }

    public function setTitle($title) 
    {
        $this->_title = $title;

        return $this;
    }

    public function getTitle() 
    {
        return $this->_title;
    }

    public function setEnabled($enabled) 
    {
        $this->_enabled = $enabled;

        return $this;
    }

    public function getEnabled() 
    {
        return $this->_enabled;
    }

    public function setLang($lang) 
    {
        $this->_lang = $lang;

        return $this;
    }

    public function getLang() 
    {
        return $this->_lang;
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
                $this->isValidCountryCode();
                $this->isValidTitle();
                $this->isValidLang();
            break;
            default:
                throw new EntityException("Object does not pass validation", 400);
            break;
        }

        return true;
    }

    private function isValidCountryCode($throwException = true) 
    {
        if ($this->getCountryCode() && preg_match('/^(AF|AX|AL|DZ|AS|AD|AO|AI|AQ|AG|AR|AM|AW|AU|AT|AZ|BS|BH|BD|BB|BY|BE|BZ|BJ|BM|BT|BO|BQ|BA|BW|BV|BR|IO|BN|BG|BF|BI|KH|CM|CA|CV|KY|CF|TD|CL|CN|CX|CC|CO|KM|CG|CD|CK|CR|CI|HR|CU|CW|CY|CZ|DK|DJ|DM|DO|EC|EG|SV|GQ|ER|EE|ET|FK|FO|FJ|FI|FR|GF|PF|TF|GA|GM|GE|DE|GH|GI|GR|GL|GD|GP|GU|GT|GG|GN|GW|GY|HT|HM|VA|HN|HK|HU|IS|IN|ID|IR|IQ|IE|IM|IL|IT|JM|JP|JE|JO|KZ|KE|KI|KP|KR|KW|KG|LA|LV|LB|LS|LR|LY|LI|LT|LU|MO|MK|MG|MW|MY|MV|ML|MT|MH|MQ|MR|MU|YT|MX|FM|MD|MC|MN|ME|MS|MA|MZ|MM|NA|NR|NP|NL|NC|NZ|NI|NE|NG|NU|NF|MP|NO|OM|PK|PW|PS|PA|PG|PY|PE|PH|PN|PL|PT|PR|QA|RE|RO|RU|RW|BL|SH|KN|LC|MF|PM|VC|WS|SM|ST|SA|SN|RS|SC|SL|SG|SX|SK|SI|SB|SO|ZA|GS|SS|ES|LK|SD|SR|SJ|SZ|SE|CH|SY|TW|TJ|TZ|TH|TL|TG|TK|TO|TT|TN|TR|TM|TC|TV|UG|UA|AE|GB|US|UM|UY|UZ|VU|VE|VN|VG|VI|WF|EH|YE|ZM|ZW)$/i', $this->getCountryCode())) {
            return true;
        }

        if ($throwException) {
            throw new EntityException('Invalid country code format', 400);
        }

        return false;
    }

    private function isValidTitle($throwException = true)
    {
        if ($this->getTitle()) {
            $this->setTitle(htmlspecialchars(strip_tags($this->getTitle())));

            return true;
        }

        if ($throwException) {
            throw new EntityException('Empty country title', 400);
        }

        return false;
    }

    private function isValidLang() 
    {
        if ($this->getLang() && in_array($this->getLang(), Config::instance()->langs)) {
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
                $this->setCountryCode($data['CountryCode'])
                     ->setTitle($data['Title'])
                     ->setEnabled($data['Enabled'])
                     ->setLang($data['Lang']);
            break;
        }

        return $this;
    }
}