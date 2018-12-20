<?php
/**
 * Class ProfileWidget
 *
 * @package application.user.widgets.menu
 */
class ProfileWidget extends CWidget
{
	public function run()
	{
        if(!Yii::app()->user->isGuest)
		    $this->render('index');
	}
}