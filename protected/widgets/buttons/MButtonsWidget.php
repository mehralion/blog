<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Nick Nikitchenko
 * Skype: quietasice
 * E-mail: quietasice123@gmail.com
 * Date: 04.07.13
 * Time: 17:38
 * To change this template use File | Settings | File Templates.
 *
 * @package application.widgets.itembuttons
 */
class MButtonsWidget extends CWidget
{
    public $buttons = array();

    public function run()
    {
        if(Yii::app()->user->isGuest)
            return;

        $buttonsArray = array();
        foreach ($this->buttons as $name => $options) {
            if(!$options['visible'])
                continue;

            $buttonsArray[] = $options;
        }

        $this->render('index', array('buttons' => $buttonsArray));
    }
}