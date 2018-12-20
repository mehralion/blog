<?php
namespace application\modules\admin\controllers;
class RadioController extends \FrontController
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
            'index'  => '\application\modules\admin\actions\radio\Index',
            'report'  => '\application\modules\admin\actions\radio\Report',
        );
    }
}