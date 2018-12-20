<?php
namespace application\modules\comment\controllers;
/**
 * Class ImageController
 * @package application.comment.controllers
 */
class AudioController extends \FrontController
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
            'add' => '\application\modules\comment\actions\audio\Add',
            'delete'  => '\application\modules\comment\actions\audio\Delete',
            'reset'   => '\application\modules\comment\actions\audio\Reset',
            'trunc'   => '\application\modules\comment\actions\audio\Trunc',
        );
    }
}