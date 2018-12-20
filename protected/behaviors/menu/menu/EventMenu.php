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
class EventMenu
{
    /**
     * @return array
     */
    public function run()
    {
        if(Yii::app()->user->isGuest)
            return array();

        return array(
            array(
                'label'=>'БЛОГИ ДРУЗЕЙ И СООБЩЕСТВА',
                'linkOptions' => array('class' => 'title'),
                'linkLabelWrapper' => 'h2',
                'itemOptions' => array('class' => 'title')
            ),
            array('label'=>'Заметки', 'url'=>array('/event/news/post', 'type' => 'friend')),
            array('label'=>'Фотографии', 'url'=>array('/event/news/image', 'type' => 'friend')),
            array('label'=>'Аудиозаписи', 'url'=>array('/event/news/audio', 'type' => 'friend')),
            array('label'=>'Видеозаписи', 'url'=>array('/event/news/video', 'type' => 'friend')),
            array('label'=>'Комментарии', 'url'=> array('/event/comment/friend')),
        );
    }
}