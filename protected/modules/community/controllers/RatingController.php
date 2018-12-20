<?php
namespace application\modules\community\controllers;

class RatingController extends \FrontController
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
            'comment_add' => array(
                'class' => '\application\modules\rating\actions\comment\Add',
                'communityId' => \Yii::app()->community->id,
                'isCommunity' => true,
            ),
            'audio_add' => array(
                'class' => '\application\modules\rating\actions\audio\Add',
                'communityId' => \Yii::app()->community->id,
                'isCommunity' => true,
            ),
            'video_add' => array(
                'class' => '\application\modules\rating\actions\video\Add',
                'communityId' => \Yii::app()->community->id,
                'isCommunity' => true,
            ),
            'image_add' => array(
                'class' => '\application\modules\rating\actions\image\Add',
                'communityId' => \Yii::app()->community->id,
                'isCommunity' => true,
            ),
            'post_add' => array(
                'class' => '\application\modules\rating\actions\post\Add',
                'communityId' => \Yii::app()->community->id,
                'isCommunity' => true,
            ),
        );
    }
}