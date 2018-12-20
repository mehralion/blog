<?php
/**
 * Class FriendMenu Меню материалов друзей
 * Created by JetBrains PhpStorm.
 * User: Николай
 * Date: 14.06.13
 * Time: 20:08
 * To change this template use File | Settings | File Templates.
 *
 * @package application.behaviors.menu
 */
class SubscribeMenu
{
    /**
     * @return array
     */
    public function run()
    {
        if(Yii::app()->user->isGuest)
            return array();

        $return = array(
            array(
                'label'=>'ПОДПИСКИ',
                'linkOptions' => array('class' => 'title'),
                'linkLabelWrapper' => 'h2',
                'itemOptions' => array('class' => 'title')
            ),
            array('label'=>'Заметки', 'url' => array('/subscribe/show/post', 'gameId' => Yii::app()->user->game_id)),
            array('label'=>'Фотографии', 'url'=>array('/subscribe/show/image', 'gameId' => Yii::app()->user->game_id)),
            array('label'=>'Аудиозаписи', 'url'=>array('/subscribe/show/audio', 'gameId' => Yii::app()->user->game_id)),
            array('label'=>'Видеозаписи', 'url'=>array('/subscribe/show/video', 'gameId' => Yii::app()->user->game_id)),
            array('label'=>'Комментарии', 'url'=>array('/subscribe/show/comment', 'gameId' => Yii::app()->user->game_id)),
            array('label'=>'Дискуссии', 'url'=>array('/subscribe/show/debate', 'gameId' => Yii::app()->user->game_id)),
        );

        return $return;
    }
}