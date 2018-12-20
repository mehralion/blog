<?php
/**
 * Class RatingMenu Меню рейтингов
 * Created by JetBrains PhpStorm.
 * User: Николай
 * Date: 14.06.13
 * Time: 20:08
 * To change this template use File | Settings | File Templates.
 *
 * @package application.behaviors.menu
 */
class RatingMenu
{
    /**
     * @return array
     */
    public function run()
    {
        return array(
            array(
                'label' => 'РЕЙТИНГИ',
                'linkOptions' => array('class' => 'title'),
                'linkLabelWrapper' => 'h2',
                'itemOptions' => array('class' => 'title')
            ),
            array(
                'label'=> '<i class="icon blog2"></i> Топ блоги',
                'url' => array('/user/show/top',),
                'linkOptions' => array('class' => 'image')
            ),
            array(
                'label'=> '<div class="icon like"></div> Топ заметки',
                'url'=>array('/post/index/top',),
                'linkOptions' => array('class' => 'image')
            ),
            array(
                'label' => '<div class="icon like"></div> Топ фотографии',
                'url'=>array('/gallery/image/top'),
                'linkOptions' => array('class' => 'image')
            ),
            array(
                'label' => '<div class="icon like"></div> Топ аудиоальбомы',
                'url'=>array('/gallery/album/top_audio'),
                'linkOptions' => array('class' => 'image')
            ),
            array(
                'label'=> '<div class="icon like"></div> Топ видеозаписи',
                'url'=>array('/gallery/video/top'),
                'linkOptions' => array('class' => 'image')
            ),
            array(
                'label'=> '<div class="icon like"></div> Топ сообщества',
                'url'=>array('/community/request/top'),
                'linkOptions' => array('class' => 'image')
            ),
            array(
                'label'=> CHtml::image(Yii::app()->theme->baseUrl.'/images/comment.png').' Самые обсуждаемые темы',
                'url'=>array('/post/index/most'),
                'linkOptions' => array('class' => 'image')
            ),
        );
    }
}