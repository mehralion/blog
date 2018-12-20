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
class SilenceController extends \FrontController
{
    public function filters()
    {
        return array(
            'accessControl',
            'ajaxOnly + set, set',
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
            'set'  => '\application\modules\moder\actions\silence\Set',
            'restore'   => '\application\modules\moder\actions\silence\Restore',
        );
    }
}