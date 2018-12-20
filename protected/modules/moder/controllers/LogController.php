<?php
namespace application\modules\moder\controllers;
/**
 * Created by JetBrains PhpStorm.
 * User: Nick Nikitchenko
 * Skype: quietasice
 * E-mail: quietasice123@gmail.com
 * Date: 08.07.13
 * Time: 17:18
 * To change this template use File | Settings | File Templates.
 *
 * @package application.moder.controllers
 */
class LogController extends \FrontController
{
    public function filters()
    {
        return array(
            'accessControl',
            'ajaxOnly + log',
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

    public function actionIndex()
    {
        $Log = new \ModerLog('search');
        $Log->unsetAttributes();

        if(isset($_REQUEST['ModerLog'])) {
            $Log->setAttributes($_REQUEST['ModerLog']);
        }

        $this->render('index', array(
            'model' => $Log
        ));
    }

    public function actionLog()
    {
        $id = \Yii::app()->request->getParam('id');
        /** @var \ModerLog $ModerLog */
        $ModerLog = \ModerLog::model()->findByPk($id);
        if(!isset($ModerLog))
            return '';

        $Log = new \ModerLog('search');
        $Log->unsetAttributes();
        $Log->item_id = $ModerLog->item_id;

        if(isset($_REQUEST['ModerLog']))
            $Log->setAttributes($_REQUEST['ModerLog']);

        $this->renderPartial('subgrid', array(
            'model' => $Log,
            'parentId' => $ModerLog->id,
        ));
    }
}