<?php
namespace application\modules\comment\controllers;
/**
 * Class VideoController
 * @package application.comment.controllers
 */
class VideoController extends \FrontController
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
                'users' => array('?'),
            )
        );
    }

    public function actions()
    {
        return array(
            'add' => '\application\modules\comment\actions\video\Add',
            'delete'  => '\application\modules\comment\actions\video\Delete',
            'reset'   => '\application\modules\comment\actions\video\Reset',
            'trunc'   => '\application\modules\comment\actions\video\Trunc',
        );
    }
}