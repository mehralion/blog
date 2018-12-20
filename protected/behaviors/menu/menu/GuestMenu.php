<?php
/**
 * Class EventMenu Меню событий
 * Created by JetBrains PhpStorm.
 * User: Николай
 * Date: 14.06.13
 * Time: 20:08
 * To change this template use File | Settings | File Templates.
 *
 * @package application.behaviors.menu
 */
class GuestMenu
{
    /**
     * @return array
     */
    public function run()
    {
        if(!Yii::app()->user->isGuest)
            return array();
        
        return array(
            array(
                'label'=>'МЕНЮ',
                'linkOptions' => array('class' => 'title'),
                'linkLabelWrapper' => 'h2',
                'itemOptions' => array('class' => 'title')
            ),
            array('label'=>'Зарегистрироваться в игре', 'url'=>'https://oldbk.com/reg.php'),
        );
    }
}