<?php
namespace application\modules\comment\controllers;
/**
 * Class PostController
 * @package application.comment.controllers
 */
class CommunityController extends \FrontController
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
            'add' => '\application\modules\comment\actions\community\Add',
            'delete'  => '\application\modules\comment\actions\community\Delete',
            'reset'   => '\application\modules\comment\actions\community\Reset',
            'trunc'   => '\application\modules\comment\actions\community\Trunc',
        );
    }
}