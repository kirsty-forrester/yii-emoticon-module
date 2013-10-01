<?php

class EmoticonToolbar extends CWidget
{
    public $menu;
    public $controller, $action;

    public function init()
    {
        $this->menu = array(
            array(
                'label' => 'Home',
                'url' => array('default/index'),
                'active' => $this->controller == 'default' && $this->action == 'index',
            ),
            array(
                'label'=>'All Emoticons',
                'url'=>array('emoticon/index'),
                'active' => $this->controller == 'emoticon' && $this->action == 'index',
            ),
            array(
                'label'=>'Upload Emoticons',
                'url'=>array('emoticon/create'),
                'active' => $this->controller == 'emoticon' && $this->action == 'create',
            ),
            array(
                'label'=>'Emoticon Sets',
                'url'=>array('emoticonSet/index'),
                'active' => $this->controller == 'emoticonSet' && $this->action == 'index',
            ),
            array(
                'label'=>'Create a new Emoticon Set',
                'url'=>array('emoticonSet/create'),
                'active' => $this->controller == 'emoticonSet' && $this->action == 'create',
            ),

        );
        return parent::init();
    }

    public function run()
    {
        $this->render('emoticonToolbar');
    }
}
