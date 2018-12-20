<?php
namespace application\modules\gallery\controllers;
/**
 * Class ImageController
 *
 * @package application.gallery.controllers
 */
class AudioController extends \FrontController
{
    public $ownCategory = true;
    public function filters()
    {
        return array(
            'accessControl',
            'ajaxOnly + update',
        );
    }

    public function accessRules()
    {
        return array(
            array('deny',
                'actions'=>array('add', 'update', 'delete', 'reset', 'index'),
                'users' => array('?'),
            )
        );
    }

    public function actions()
    {
        return array(
            'add'    => '\application\modules\gallery\actions\audio\Add',
            'update'    => '\application\modules\gallery\actions\audio\Update',
            'index'     => array(
                'class' => '\application\modules\gallery\actions\audio\Index',
                'ownProfile' => true
            ),
            'delete'    => '\application\modules\gallery\actions\audio\Delete',
            'reset'     => '\application\modules\gallery\actions\audio\Reset',
            'trunc'     => '\application\modules\gallery\actions\audio\Trunc',
        );
    }
}