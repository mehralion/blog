<?php
namespace application\modules\poll\controllers;
class RequestController extends \FrontController
{
    public function filters()
    {
        return array(
            'accessControl',
            'ajaxOnly + add',
        );
    }

    public function accessRules()
    {
        return array(
            array('deny',
                'actions'=>array('add'),
                'users' => array('?'),
            )
        );
    }

    public function actions()
    {
        return array(
            'add'=> '\application\modules\poll\actions\request\Add',
            'index'=> '\application\modules\poll\actions\request\Index',
        );
    }
}