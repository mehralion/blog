<?php
namespace application\modules\moder\controllers;
/**
 * Created by JetBrains PhpStorm.
 * User: Nick Nikitchenko
 * Skype: quietasice
 * E-mail: quietasice123@gmail.com
 * Date: 15.07.13
 * Time: 17:42
 * To change this template use File | Settings | File Templates.
 *
 * @package application.moder.controllers
 */
class VideoController extends \FrontController
{
    public function filters()
    {
        return array(
            'accessControl',
            'ajaxOnly + delete',
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
            'accept'     => '\application\modules\moder\actions\video\Accept',
            'delete'  => '\application\modules\moder\actions\video\Delete',
            'reset'   => '\application\modules\moder\actions\video\Reset',
            'index'   => '\application\modules\moder\actions\video\Index',
            'restore'   => '\application\modules\moder\actions\video\Restore',
        );
    }
}