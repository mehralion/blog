<?php
namespace application\modules\community\controllers;

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
                'actions'=>array('connect', 'logout', 'accept'),
                'users' => array('?'),
            )
        );
    }

    public function actions()
    {
        return array(
            'index' => '\application\modules\community\actions\request\Index',
            'show' => '\application\modules\community\actions\request\Show',
            'connect' => '\application\modules\community\actions\request\Connect',
            'logout' => '\application\modules\community\actions\request\Logout',
            'top' => '\application\modules\community\actions\request\Top',
            'list' => '\application\modules\community\actions\request\ComList',
            'accept' => '\application\modules\community\actions\request\Accept',
            'decline' => '\application\modules\community\actions\request\Decline',
        );
    }
}