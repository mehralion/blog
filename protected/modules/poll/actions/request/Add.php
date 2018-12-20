<?php
namespace application\modules\poll\actions\request;
/**
 * Created by JetBrains PhpStorm.
 * User: Николай
 * Date: 13.06.13
 * Time: 13:13
 * To change this template use File | Settings | File Templates.
 *
 * @package application.post.actions.index
 */
class Add extends \CAction
{
    public $userId;

    public function run()
    {
        $pollId = \Yii::app()->request->getParam('id');
        $answerId = \Yii::app()->request->getParam('vote_id');

        $criteria = new \CDbCriteria();
        $criteria->with = array(
            'post' => array(
                'scopes' => array(
                    'deletedStatus',
                    'activatedStatus',
                    'moderDeletedStatus',
                    'truncatedStatus'
                ),
                'params' => array(
                    ':deletedStatus' => 0,
                    ':activatedStatus' => 1,
                    ':moderDeletedStatus' => 0,
                    ':truncatedStatus' => 0
                ),
            ),
        );
        $criteria->addCondition('`t`.id = :poll_id');
        $criteria->params = array(':poll_id' => $pollId);
        $criteria->mergeWith(\Yii::app()->access->GetCriteriaAccess('post'));
        /** @var \Poll $Poll */
        $Poll = \Poll::model()->find($criteria);
        if(!$Poll) {
            \Yii::app()->message->setErrors('danger', 'Опрос не найден');
            \Yii::app()->message->showMessage();
        }

        /*if($Poll->user_owner_id == \Yii::app()->user->id) {
            \Yii::app()->message->setErrors('danger', 'Вы не можете голосовать в своих опросах');
            \Yii::app()->message->showMessage();
        }*/

        $criteria = new \CDbCriteria();
        $criteria->addCondition('poll_id = :poll_id');
        $criteria->addCondition('id = :answer_id');
        $criteria->params = array(':poll_id' => $Poll->id, ':answer_id' => $answerId);
        /** @var \PollAnswer $PollAnswer */
        $PollAnswer = \PollAnswer::model()->find($criteria);
        if(!$PollAnswer) {
            \Yii::app()->message->setErrors('danger', 'Данный вариант ответа не найден');
            \Yii::app()->message->showMessage();
        }
        $PollAnswer->value += 1;

        $criteria = new \CDbCriteria();
        $criteria->addCondition('poll_id = :poll_id');
        $criteria->addCondition('user_id = :user_id');
        $criteria->params = array(':poll_id' => $Poll->id, ':user_id' => \Yii::app()->user->id);
        /** @var \PollUserAnswer $UserAnswer */
        $UserAnswer = \PollUserAnswer::model()->find($criteria);
        if($UserAnswer) {
            \Yii::app()->message->setErrors('danger', 'Вы уже оставляли свой голос!');
            \Yii::app()->message->showMessage();
        }

        $error = false;
        $t = \Yii::app()->db->beginTransaction();
        try {
            if(!$PollAnswer->save()) {
                $error = true;
                \Yii::app()->message->setErrors('danger', $PollAnswer);
            }

            $UserAnswer = new \PollUserAnswer();
            $UserAnswer->create_datetime = \DateTimeFormat::format();
            $UserAnswer->poll_id = $Poll->id;
            $UserAnswer->answer_id = $PollAnswer->id;
            $UserAnswer->user_id = \Yii::app()->user->id;

            if(!$UserAnswer->save()) {
                $error = true;
                \Yii::app()->message->setErrors('danger', $UserAnswer);
            }

            $criteria = new \CDbCriteria();
            $criteria->addCondition('poll_id = :poll_id');
            $criteria->params = array(':poll_id' => $Poll->id);
            /** @var \PollAnswer[] $Results */
            $Results = \PollAnswer::model()->findAll($criteria);

            $options = array();
            $options[] = array('title' => 'Task', 'value' => $Poll->question);
            foreach($Results as $answer)
                $options[] = array('title' => $answer->title, 'value' => $answer->value);

            if(!$error) {
                $t->commit();
                \Yii::app()->message->setOther(array('ok' => true));
                \Yii::app()->message->setOther(array('title' => $Poll->question));
                \Yii::app()->message->setOther(array('values' => $options));
                \Yii::app()->message->setText('success', 'Вы успешно проголосовали');
            } else {
                $t->rollback();
                \Yii::app()->message->setErrors('danger', 'Возникли неполадки, попробуйте позже!');
            }
        } catch (\Exception $ex) {
            $t->rollback();
            \MyException::log($ex);
            \Yii::app()->message->setErrors('danger', 'Возникли неполадки, попробуйте позже');
        }

        \Yii::app()->message->showMessage();
    }
}