<?php
namespace application\modules\gallery\controllers;
/**
 * Class AlbumController
 *
 * @package application.gallery.controllers
 *
 * @method \CAction add()
 * @method \CAction mUpdate()
 * @method \CAction delete()
 * @method \CAction show()
 * @method \CAction index()
 */
class AlbumController extends \FrontController
{
    public function filters()
    {
        return array(
            'accessControl',
            //'ajaxOnly + add_ima',
        );
    }

    public function accessRules()
    {
        return array(
            array('deny',
                'actions'=>array('add_image', 'add_audio', 'add_video', 'update_image', 'update_audio', 'update_video',
                    'delete_audio', 'delete_image', 'delete_video'),
                'users' => array('?'),
            )
        );
    }

    public function actions()
    {
        return array(
            'top_audio'   => '\application\modules\gallery\actions\album\audio\Top',
            'add_image'   => '\application\modules\gallery\actions\album\image\Add',
            'add_audio'   => '\application\modules\gallery\actions\album\audio\Add',
            'add_video'   => '\application\modules\gallery\actions\album\video\Add',
            'update_image'=> '\application\modules\gallery\actions\album\image\Update',
            'update_audio'=> '\application\modules\gallery\actions\album\audio\Update',
            'update_video'=> '\application\modules\gallery\actions\album\video\Update',
            'delete_audio'=> '\application\modules\gallery\actions\album\audio\Delete',
            'delete_image'=> '\application\modules\gallery\actions\album\image\Delete',
            'delete_video'=> '\application\modules\gallery\actions\album\video\Delete',
            'crop_audio' => '\application\modules\gallery\actions\album\audio\Crop',
            'crop_image' => '\application\modules\gallery\actions\album\image\Crop',
            'crop_video' => '\application\modules\gallery\actions\album\video\Crop',
            'show_video'  => array(
                'class' => '\application\modules\gallery\actions\album\video\Show',
                'userId' => \Yii::app()->user->id
            ),
            'index_video' => array(
                'class' => '\application\modules\gallery\actions\album\video\Index',
                'userId' => \Yii::app()->user->id
            ),
            'show_audio'  => array(
                'class' => '\application\modules\gallery\actions\album\audio\Show',
                'userId' => \Yii::app()->user->id
            ),
            'index_image' => array(
                'class' => '\application\modules\gallery\actions\album\image\Index',
                'userId' => \Yii::app()->user->id
            ),
            'show_image'  => array(
                'class' => '\application\modules\gallery\actions\album\image\Show',
                'userId' => \Yii::app()->user->id
            ),
            'index_audio' => array(
                'class' => '\application\modules\gallery\actions\album\audio\Index',
                'userId' => \Yii::app()->user->id
            ),
        );
    }
}