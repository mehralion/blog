<?php
namespace application\modules\community\controllers;

class ImageController extends \FrontController
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
                'actions'=>array('update', 'reset', 'upload', 'delete'),
                'users' => array('?'),
            )
        );
    }

    public function actions()
    {
        return array(
            'upload'    => array(
                'class' => '\application\modules\gallery\actions\image\Add',
                'isCommunity' => true,
                'communityId' => \Yii::app()->community->id,
                'viewName' => 'form_add'
            ),
            'update' => array(
                'class' => '\application\modules\gallery\actions\image\Update',
                'isCommunity' => true,
                'communityId' => \Yii::app()->community->id,
                'viewName' => 'form_update'
            ),
            'show'   => array(
                'class' => '\application\modules\gallery\actions\image\Show',
                'isCommunity' => true,
                'communityId' => \Yii::app()->community->id,
                'viewName' => 'show'
            ),
            'delete'   => array(
                'class' => '\application\modules\gallery\actions\image\Delete',
                'isCommunity' => true,
                'communityId' => \Yii::app()->community->id,
                'viewName' => 'show'
            ),
            'reset'   => array(
                'class' => '\application\modules\gallery\actions\image\Reset',
                'isCommunity' => true,
                'communityId' => \Yii::app()->community->id,
            ),
            'preview'   => array(
                'class' => '\application\modules\gallery\actions\image\Preview',
            ),
        );
    }
}