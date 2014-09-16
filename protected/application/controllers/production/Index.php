<?php

namespace controllers\production;

class Index extends \SlimController\SlimController 
{
    public function indexAction()
    {
        $this->render('production/landing', array(
            'layout' => false,
        ));
    }
}