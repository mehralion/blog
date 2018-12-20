<?php
namespace application\modules\subscribe\controllers;
class ShowController extends \FrontController
{
    public $layout = 'subscribe/subscribe';

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
            'post'   => '\application\modules\subscribe\actions\show\Post',
            'image'   => '\application\modules\subscribe\actions\show\Image',
            'video'   => '\application\modules\subscribe\actions\show\Video',
            'audio'   => '\application\modules\subscribe\actions\show\Audio',
            'comment'=> '\application\modules\subscribe\actions\show\Comment',
            'debate'=> '\application\modules\subscribe\actions\show\Debate',
        );
    }
}