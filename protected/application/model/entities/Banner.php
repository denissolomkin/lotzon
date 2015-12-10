<?php
use \SettingsModel;

class Banner extends Entity
{
    private
        $_id = 0,
        $_group = '',
        $_key = '',
        $_country = '',
        $_template = '',
        $_div = '',
        $_script = '',
        $_enabled = false,
        $_title = '',
        $_chance = false;

    public function random()
    {

        $banners = SettingsModel::instance()->getSettings('banners')->getValue()[$this->getGroup()];

        if(is_array($banners)){
            foreach ($banners as $group) {
                if (is_array($group)) {
                    shuffle($group);
                    foreach ($group as $banner) {

                        if (is_array($banner['countries']) and !in_array($this->getCountry(), $banner['countries']))
                            continue;

                        $this->setDiv($banner['div'])
                            ->setScript($banner['script'])
                            ->setTitle($banner['title'])
                            ->setEnabled($banners['settings']['enabled'])
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
        if($this->isTitle())
            return include_once(PATH_TEMPLATES.'banner/'.$this->getTemplate().'.php');
    }
}