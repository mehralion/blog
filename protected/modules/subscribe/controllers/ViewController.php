<?php
namespace application\modules\subscribe\controllers;
class ViewController extends \FrontController
{
    public function filters()
    {
        return array(
            'accessControl',
            'ajaxOnly + post, image, video, audio',
        );
    }

    public function accessRules()
    {
        return array(
            array('deny',
                'actions'=>array('post', 'image', 'video', 'audio'),
                'users' => array('?'),
            )
        );
    }

    public function actions()
    {
        return array(
            'post'   => '\application\modules\subscribe\actions\view\Post',
            'image'   => '\application\modules\subscribe\actions\view\Image',
            'video'   => '\application\modules\subscribe\actions\view\Video',
            'audio'   => '\application\modules\subscribe\actions\view\Audio',
        );
    }
}