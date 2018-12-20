<?php
namespace application\modules\community\controllers;

class VideoController extends \FrontController
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
                'actions'=>array('add', 'update', 'delete', 'reset'),
                'users' => array('?'),
            )
        );
    }

    public function actions()
    {
        return array(
            'add'    => array(
                'class' => '\application\modules\gallery\actions\video\Add',
                'isCommunity' => true,
                'communityId' => \Yii::app()->community->id,
                'viewName' => 'form'
            ),
            'update' => array(
                'class' => '\application\modules\gallery\actions\video\Update',
                'isCommunity' => true,
                'communityId' => \Yii::app()->community->id,
                'viewName' => 'form'
            ),
            'delete'  => array(
                'class' => '\application\modules\gallery\actions\video\Delete',
                'isCommunity' => true,
                'communityId' => \Yii::app()->community->id,
            ),
            'show'   => array(
                'class' => '\application\modules\gallery\actions\video\Show',
                'isCommunity' => true,
                'communityId' => \Yii::app()->community->id,
                'viewName' => 'show'
            ),
            'reset'   => array(
                'class' => '\application\modules\gallery\actions\video\Reset',
                'isCommunity' => true,
                'communityId' => \Yii::app()->community->id,
            ),
            'preview'   => array(
                'class' => '\application\modules\gallery\actions\video\Preview',
            ),
        );
    }
}