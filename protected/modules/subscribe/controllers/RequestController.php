<?php
namespace application\modules\subscribe\controllers;
class RequestController extends \FrontController
{
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
                'actions'=>array('subscribe', 'debate', 'deletedebate', 'post', 'image', 'video', 'audio'),
                'users' => array('?'),
            )
        );
    }

    public function actions()
    {
        return array(
            'subscribe'   => '\application\modules\subscribe\actions\request\Subscribe',
            'deletedebate'   => '\application\modules\subscribe\actions\request\DeleteDebate',
            'deletesubscribe'   => '\application\modules\subscribe\actions\request\DeleteSubscribe',
            'post'   => '\application\modules\subscribe\actions\request\Post',
            'image'   => '\application\modules\subscribe\actions\request\Image',
            'video'   => '\application\modules\subscribe\actions\request\Video',
            'audio'   => '\application\modules\subscribe\actions\request\Audio',
            'community'   => '\application\modules\subscribe\actions\request\Community',
        );
    }
}