<?php
namespace application\modules\admin\controllers;
class RightsController extends \FrontController
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
            'index'  => '\application\modules\admin\actions\rights\Index',
            'add'  => '\application\modules\admin\actions\rights\Add',
            'delete'  => '\application\modules\admin\actions\rights\Delete',
        );
    }
}