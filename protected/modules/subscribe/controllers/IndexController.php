<?php
namespace application\modules\subscribe\controllers;
class IndexController extends \FrontController
{
    public $layout = 'subscribe/profile';
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
                'actions'=>array('user', 'debate', 'community'),
                'users' => array('?'),
            )
        );
    }

    public function actions()
    {
        return array(
            'user'   => '\application\modules\subscribe\actions\index\User',
            'debate'   => '\application\modules\subscribe\actions\index\Debate',
            'community'   => '\application\modules\subscribe\actions\index\Community',
        );
    }
}