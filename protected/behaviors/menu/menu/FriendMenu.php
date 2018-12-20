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
class FriendMenu
{
    /**
     * @return array
     */
    public function profile()
    {
        $return = array();
        if(null == Yii::app()->userOwn->game_id || Yii::app()->userOwn->id == Yii::app()->user->id)
            return $return;

        $return = array(
            array(
                'label'=>'<i class="icon" id="blog"></i> '.Yii::app()->userOwn->login,
                'linkOptions' => array('class' => 'title'),
                'linkLabelWrapper' => 'h2',
                'itemOptions' => array('class' => 'title')
            ),
            array('label'=>'Профиль', 'url' => array('/user/profile/show', 'gameId' => Yii::app()->userOwn->game_id)),
            array('label'=>'Заметки', 'url'=>array('/user/show/posts', 'gameId' => Yii::app()->userOwn->game_id)),
            array('label'=>'Фотографии', 'url'=>array('/user/show/album_image', 'gameId' => Yii::app()->userOwn->game_id)),
            array('label'=>'Аудиозаписи', 'url'=>array('/user/show/album_audio', 'gameId' => Yii::app()->userOwn->game_id)),
            array('label'=>'Видеозаписи', 'url'=>array('/user/show/album_video', 'gameId' => Yii::app()->userOwn->game_id)),
        );

        return $return;
    }
}