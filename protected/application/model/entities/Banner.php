<?php

use \SettingsModel;

Application::import(PATH_APPLICATION . 'model/Entity.php');

class Banner extends Entity
{
    protected
        $_id = 0,
        $_key = '',
        $_device = '',
        $_template = '',
        $_location = '',
        $_page = '',
        $_country = '',
        $_div = '',
        $_script = '',
        $_enabled = false,
        $_title = '',
        $_chance = false;

    public function random()
    {

        $banners = SettingsModel::instance()->getSettings('ad')->getValue($this->getDevice());
        $enabled = SettingsModel::instance()->getSettings('ad')->getValue('enabled');

        if(is_array($banners) && isset($banners[$this->getLocation()])) {

            if (isset($banners[$this->getLocation()][$this->getPage()]))
                $banners = $banners[$this->getLocation()][$this->getPage()];
            elseif (isset($banners[$this->getLocation()]['default']))
                $banners = $banners[$this->getLocation()]['default'];
            else
                $banners = false;

            if (is_array($banners))
                foreach ($banners as $group) {
                    if (is_array($group)) {
                        shuffle($group);
                        foreach ($group as $banner) {

                            if (!$banner['title'] OR (is_array($banner['countries']) AND !in_array($this->getCountry(), $banner['countries'])))
                                continue;

                            $this->setDiv($banner['div'])
                                ->setScript($banner['script'])
                                ->setEnabled($enabled)
                                ->setTitle($banner['title'])
                                ->setChance($banner['chance']);

                            break;
                        }
                    }
                }
        }

        return $this;
    }

    public function render()
    {
        $response = null;

        if($this->isTitle()) {
            ob_start();
            $template = $this->getTemplate();
            if($this->getLocation() == 'context')
                $template .= '-'.$this->getLocation().'-'.$this->getPage();
            include_once(PATH_TEMPLATES . 'banner/' . $template . '.php');
            $response = ob_get_contents();
            ob_end_clean();
        }
        return $response;
    }
}