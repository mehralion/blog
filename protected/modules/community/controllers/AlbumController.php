<?php
namespace application\modules\community\controllers;

class AlbumController extends \FrontController
{
    public $layout = 'community/community';

    public function filters()
    {
        return array(
            'accessControl',
        );
    }

    public function accessRules()
    {
        return array(
            array('deny',
                'actions'=>array(
                    'image_add', 'image_update', 'image_delete', 'image_crop',
                    'audio_add', 'audio_update', 'audio_delete', 'audio_crop',
                    'video_add', 'video_update', 'video_delete', 'video_crop',
                ),
                'users' => array('?'),
            )
        );
    }

    public function actions()
    {
        return array(
            'image' => array(
                'class' => '\application\modules\gallery\actions\album\image\Index',
                'communityId' => \Yii::app()->community->id,
                'isCommunity' => true,
                'viewName' => 'image/index'
            ),
            'image_show' => array(
                'class' => '\application\modules\gallery\actions\album\image\Show',
                'communityId' => \Yii::app()->community->id,
                'isCommunity' => true,
                'viewName' => 'image/show'
            ),
            'image_add' => array(
                'class' => '\application\modules\gallery\actions\album\image\Add',
                'communityId' => \Yii::app()->community->id,
                'isCommunity' => true,
                'viewName' => 'image/form'
            ),
            'image_update' => array(
                'class' => '\application\modules\gallery\actions\album\image\Update',
                'communityId' => \Yii::app()->community->id,
                'isCommunity' => true,
                'viewName' => 'image/form'
            ),
            'image_delete' => array(
                'class' => '\application\modules\gallery\actions\album\image\Delete',
                'communityId' => \Yii::app()->community->id,
                'isCommunity' => true,
            ),
            'image_crop' => array(
                'class' => '\application\modules\gallery\actions\album\image\Crop',
                'communityId' => \Yii::app()->community->id,
                'isCommunity' => true,
            ),
            'audio' => array(
                'class' => '\application\modules\gallery\actions\album\audio\Index',
                'communityId' => \Yii::app()->community->id,
                'isCommunity' => true,
                'viewName' => 'audio/index'
            ),
            'audio_show' => array(
                'class' => '\application\modules\gallery\actions\album\audio\Show',
                'communityId' => \Yii::app()->community->id,
                'isCommunity' => true,
                'viewName' => 'audio/show'
            ),
            'audio_add' => array(
                'class' => '\application\modules\gallery\actions\album\audio\Add',
                'communityId' => \Yii::app()->community->id,
                'isCommunity' => true,
                'viewName' => 'audio/form'
            ),
            'audio_update' => array(
                'class' => '\application\modules\gallery\actions\album\audio\Update',
                'communityId' => \Yii::app()->community->id,
                'isCommunity' => true,
                'viewName' => 'audio/form'
            ),
            'audio_delete' => array(
                'class' => '\application\modules\gallery\actions\album\audio\Delete',
                'communityId' => \Yii::app()->community->id,
                'isCommunity' => true,
            ),
            'audio_crop' => array(
                'class' => '\application\modules\gallery\actions\album\audio\Crop',
                'communityId' => \Yii::app()->community->id,
                'isCommunity' => true,
            ),
            'video' => array(
                'class' => '\application\modules\gallery\actions\album\video\Index',
                'communityId' => \Yii::app()->community->id,
                'isCommunity' => true,
                'viewName' => 'video/index'
            ),
            'video_show' => array(
                'class' => '\application\modules\gallery\actions\album\video\Show',
                'communityId' => \Yii::app()->community->id,
                'isCommunity' => true,
                'viewName' => 'video/show'
            ),
            'video_add' => array(
                'class' => '\application\modules\gallery\actions\album\video\Add',
                'communityId' => \Yii::app()->community->id,
                'isCommunity' => true,
                'viewName' => 'video/form'
            ),
            'video_update' => array(
                'class' => '\application\modules\gallery\actions\album\video\Update',
                'communityId' => \Yii::app()->community->id,
                'isCommunity' => true,
                'viewName' => 'video/form'
            ),
            'video_delete' => array(
                'class' => '\application\modules\gallery\actions\album\video\Delete',
                'communityId' => \Yii::app()->community->id,
                'isCommunity' => true,
            ),
            'video_crop' => array(
                'class' => '\application\modules\gallery\actions\album\video\Crop',
                'communityId' => \Yii::app()->community->id,
                'isCommunity' => true,
            ),
        );
    }
}