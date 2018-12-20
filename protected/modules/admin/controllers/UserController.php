<?php
namespace application\modules\admin\controllers;
class UserController extends \FrontController
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
        );
    }

    public function actions()
    {
        return array(
            'index'  => '\application\modules\admin\actions\user\Index',
            'clear'   => '\application\modules\admin\actions\user\Clear',
            'list'   => '\application\modules\admin\actions\user\ListUser',
        );
    }
}