<?php
/**
 * Class UserMenu Меню пользователя
 * Created by JetBrains PhpStorm.
 * User: Николай
 * Date: 14.06.13
 * Time: 20:08
 * To change this template use File | Settings | File Templates.
 *
 * @package application.behaviors.menu
 */
class CommunityMenu
{
    /**
     * @return array
     */
    public function run()
    {
        $menu = array(
            array(
                'label'=>Yii::app()->stringHelper->subString(Yii::app()->community->title, 20, '...'),
                'linkOptions' => array('class' => 'title', 'rel' => 'tooltip', 'title' => Yii::app()->community->title),
                'linkLabelWrapper' => 'h2',
                'itemOptions' => array('class' => 'title')
            ),
        );
        $menu = CMap::mergeArray($menu, array(
            array('label'=>'Информация о сообществе', 'url'=>array('/community/request/show', 'community_alias' => Yii::app()->community->alias)),
            array(
                'label'=>'Участники',
                'url'=>array('/community/users/index', 'community_alias' => Yii::app()->community->alias),
                'visible' => Yii::app()->community->isModer()
            ),
            array('label'=>'Заметки', 'url'=>array('/community/post/index', 'community_alias' => Yii::app()->community->alias)),
            array('label'=>'Фотографии', 'url'=>array('/community/album/image', 'community_alias' => Yii::app()->community->alias)),
            array('label'=>'Видеозаписи', 'url'=>array('/community/album/video', 'community_alias' => Yii::app()->community->alias)),
            array('label'=>'Аудиозаписи', 'url'=>array('/community/album/audio', 'community_alias' => Yii::app()->community->alias)),
            array(
                'label'=>'Участники',
                'url'=>array('/community/users/index', 'community_alias' => Yii::app()->community->alias),
                'visible' => Yii::app()->community->isModer()
            ),
            array(
                'label'=>'Корзина',
                'url'=>array('/community/trunc/post', 'community_alias' => Yii::app()->community->alias),
                'visible' => Yii::app()->community->isModer()
            ),
            array(
                'label'=>'Настройки',
                'url'=>array('/community/profile/settings', 'community_alias' => Yii::app()->community->alias),
                'visible' => Yii::app()->user->id == Yii::app()->community->user_id
            )
        ));

        return $menu;
    }
}