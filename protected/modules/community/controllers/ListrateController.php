<?php
namespace application\modules\community\controllers;

class ListrateController extends \FrontController
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
                'actions'=>array('settings', 'add', 'own'),
                'users' => array('?'),
            )
        );
    }

    public function actions()
    {
        return array(
            'comment_community' => array(
                'class' => '\application\modules\comment\actions\community\ListRate',
                'communityId' => \Yii::app()->community->id,
                'isCommunity' => true,
            ),
            'comment_post' => array(
                'class' => '\application\modules\comment\actions\post\ListRate',
                'communityId' => \Yii::app()->community->id,
                'isCommunity' => true,
            ),
            'comment_image' => array(
                'class' => '\application\modules\comment\actions\image\ListRate',
                'communityId' => \Yii::app()->community->id,
                'isCommunity' => true,
            ),
            'comment_video' => array(
                'class' => '\application\modules\comment\actions\video\ListRate',
                'communityId' => \Yii::app()->community->id,
                'isCommunity' => true,
            ),
            'comment_audio' => array(
                'class' => '\application\modules\comment\actions\audio\ListRate',
                'communityId' => \Yii::app()->community->id,
                'isCommunity' => true,
            ),
            'audio' => array(
                'class' => '\application\modules\album\actions\audio\ListRate',
                'communityId' => \Yii::app()->community->id,
                'isCommunity' => true,
            ),
            'video' => array(
                'class' => '\application\modules\album\actions\video\ListRate',
                'communityId' => \Yii::app()->community->id,
                'isCommunity' => true,
            ),
            'image' => array(
                'class' => '\application\modules\album\actions\image\ListRate',
                'communityId' => \Yii::app()->community->id,
                'isCommunity' => true,
            ),
            'post' => array(
                'class' => '\application\modules\album\actions\post\ListRate',
                'communityId' => \Yii::app()->community->id,
                'isCommunity' => true,
            ),
            'community' => array(
                'class' => '\application\modules\album\actions\community\ListRate',
                'communityId' => \Yii::app()->community->id,
                'isCommunity' => true,
            ),
        );
    }
}