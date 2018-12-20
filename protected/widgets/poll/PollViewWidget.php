<?php
/**
 * Class PollWidget
 *
 * @package application.widgets
 *
 * @property FrontController $controller
 */
class PollViewWidget extends CWidget
{
    public $pollId = null;

    public function init()
    {
        Yii::app()->clientScript->registerScriptFile('https://www.google.com/jsapi');
        Yii::app()->clientScript->registerScript(uniqid(), 'google.load("visualization", "1", {packages:["corechart"]});', CClientScript::POS_HEAD);
        // this method is called by CController::beginWidget()
    }

    public function run()
    {
        if($this->pollId === null)
            return '';

        $criteria = new CDbCriteria();
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
                )
            ),
            //'owner',
            'pollAnswers',
            //'hasAnswer'
        );
        $criteria->scopes = array('enabled');
        $criteria->addCondition('`t`.id = :id');
        $criteria->params = array(':id' => $this->pollId);
        /** @var Poll $Poll */
        $Poll = Poll::model()->find($criteria);
        if(!$Poll)
            return '';

        $criteria = new CDbCriteria();
        $criteria->addCondition('poll_id = :poll_id');
        $criteria->addCondition('user_id = :user_id');
        $criteria->params = array(':poll_id' => $Poll->id, ':user_id' => Yii::app()->user->id);
        $count = PollUserAnswer::model()->count($criteria);

        if(Yii::app()->user->isGuest || $count || ($Poll->date_end !== null && strtotime($Poll->date_end) < time()))
            $view = 'view';
        else
            $view = 'vote';

        $this->render($view, array('model' => $Poll, 'results' => $Poll->pollAnswers));
    }
}