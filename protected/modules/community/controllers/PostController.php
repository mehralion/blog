<?php
namespace application\modules\community\controllers;

class PostController extends \FrontController
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
                'actions'=>array('add', 'update', 'reset', 'delete'),
                'users' => array('?'),
            )
        );
    }

    public function actions()
    {
        return array(
            'add'    => array(
                'class' => '\application\modules\post\actions\profile\Add',
                'isCommunity' => true,
                'communityId' => \Yii::app()->community->id,
                'viewName' => 'form'
            ),
            'update' => array(
                'class' => '\application\modules\post\actions\profile\Update',
                'isCommunity' => true,
                'communityId' => \Yii::app()->community->id,
                'viewName' => 'form'
            ),
            'index'  => array(
                'class' => '\application\modules\post\actions\index\Index',
                'isCommunity' => true,
                'communityId' => \Yii::app()->community->id,
                'viewName' => 'index'
            ),
            'show'   => array(
                'class' => '\application\modules\post\actions\index\Show',
                'isCommunity' => true,
                'communityId' => \Yii::app()->community->id,
                'viewName' => 'show'
            ),
            'delete' => array(
                'class' => '\application\modules\post\actions\profile\Delete',
                'isCommunity' => true,
                'communityId' => \Yii::app()->community->id,
            ),
            'reset' => array(
                'class' => '\application\modules\post\actions\profile\Reset',
                'isCommunity' => true,
                'communityId' => \Yii::app()->community->id,
            ),
        );
    }
}