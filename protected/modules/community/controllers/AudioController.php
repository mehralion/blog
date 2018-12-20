<?php
namespace application\modules\community\controllers;

class AudioController extends \FrontController
{
    public $layout = 'community/profile';

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
                'class' => '\application\modules\gallery\actions\audio\Add',
                'isCommunity' => true,
                'communityId' => \Yii::app()->community->id,
                'viewName' => 'form_add'
            ),
            'update' => array(
                'class' => '\application\modules\gallery\actions\audio\Update',
                'isCommunity' => true,
                'communityId' => \Yii::app()->community->id,
                'viewName' => 'form_update'
            ),
            'delete' => array(
                'class' => '\application\modules\gallery\actions\audio\Delete',
                'isCommunity' => true,
                'communityId' => \Yii::app()->community->id,
            ),
            'reset' => array(
                'class' => '\application\modules\gallery\actions\audio\Reset',
                'isCommunity' => true,
                'communityId' => \Yii::app()->community->id,
            ),
        );
    }
}