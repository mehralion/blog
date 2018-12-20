<?php
/**
 * Class PollWidget
 *
 * @package application.widgets
 *
 * @property FrontController $controller
 */
class PollWidget extends CWidget
{

    public function init()
    {
        Yii::app()->clientScript->registerScriptFile('https://www.google.com/jsapi');
        Yii::app()->clientScript->registerScript(uniqid(), 'google.load("visualization", "1", {packages:["corechart"]});', CClientScript::POS_HEAD);
        // this method is called by CController::beginWidget()
    }

    public function run()
    {
        $criteria = new CDbCriteria();
        $criteria->addCondition('`t`.id >= (SELECT FLOOR(MAX(id) * RAND()) from `poll`)');
        $criteria->order = '`t`.id';
        $criteria->scopes = array('enabled');
        $criteria->select = '`t`.question, `t`.post_id';
        $criteria->with = array(
            'post' => array(
                'scopes' => array(
                    'deletedStatus',
                    'moderDeletedStatus',
                    'truncatedStatus',
                    'activatedStatus'
                ),
                'params' => array(
                    ':deletedStatus' => 0,
                    ':moderDeletedStatus' => 0,
                    ':truncatedStatus' => 0,
                    ':activatedStatus' => 1
                ),
                'select' => false,
            ),
            //'owner' => ['select' => false],
            'pollAnswers' => array(
                'select' => '`pollAnswers`.title, `pollAnswers`.value',
                'order' => 'pollAnswers.id desc'
            ),
            'hasAnswer' => ['select' => false]
        );
        $criteria->mergeWith(\Yii::app()->access->GetCriteriaAccess('post'));
        /** @var Poll $Poll */
        $Poll = Poll::model()->find($criteria);
        if(!$Poll)
            return '';

        if($Poll)
            $this->render('widget', array('model' => $Poll, 'results' => $Poll->pollAnswers));
    }
}