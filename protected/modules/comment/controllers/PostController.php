<?php
namespace application\modules\comment\controllers;
/**
 * Class PostController
 * @package application.comment.controllers
 */
class PostController extends \FrontController
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
            'add' => '\application\modules\comment\actions\post\Add',
            'delete'  => '\application\modules\comment\actions\post\Delete',
            'reset'   => '\application\modules\comment\actions\post\Reset',
            'trunc'   => '\application\modules\comment\actions\post\Trunc',
        );
    }
}