<?php
namespace application\modules\comment\controllers;
/**
 * Class ImageController
 * @package application.comment.controllers
 */
class ImageController extends \FrontController
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
            'add' => '\application\modules\comment\actions\image\Add',
            'delete'  => '\application\modules\comment\actions\image\Delete',
            'reset'   => '\application\modules\comment\actions\image\Reset',
            'trunc'   => '\application\modules\comment\actions\image\Trunc',
        );
    }
}