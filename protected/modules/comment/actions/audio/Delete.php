<?php
namespace application\modules\comment\actions\audio;
/**
 * Created by JetBrains PhpStorm.
 * User: nnick
 * Date: 11.08.13
 * Time: 22:35
 * To change this template use File | Settings | File Templates.
 */

class Delete extends \CAction
{
    public function run()
    {
        $id = \Yii::app()->request->getParam('id');
        $criteria = new \CDbCriteria();
        $criteria->addCondition('`t`.id = :id');
        $criteria->with = array(
            'audio' => array(
                'joinType' => 'inner join',
                'scopes' => array(
                    'activatedStatus',
                    'deletedStatus',
                    'moderDeletedStatus',
                    'truncatedStatus'
                ),
                'params' => array(
                    ':activatedStatus' => 1,
                    ':deletedStatus' => 0,
                    ':moderDeletedStatus' => 0,
                    ':truncatedStatus' => 0
                )
            ),
            'info'
        );
        $criteria->scopes = array(
            'activatedStatus',
            'deletedStatus',
            'moderDeletedStatus',
            'truncatedStatus',
        );
        $criteria->params = array(
            ':id' => $id,
            ':activatedStatus' => 1,
            ':deletedStatus' => 0,
            ':moderDeletedStatus' => 0,
            ':truncatedStatus' => 0
        );

        /** @var \CommentItemAudio $Comment */
        $Comment = \CommentItemAudio::model()->find($criteria);
        if(!isset($Comment)) {
            \Yii::app()->message->setErrors('danger', 'Комментарий не найден');
            \Yii::app()->message->showMessage();
        }

        if($Comment->audio->is_community && !\Yii::app()->community->isModer() && $Comment->user_id != \Yii::app()->user->id) {
            \Yii::app()->message->setErrors('danger', 'Вы не можете удалять комментарии в этом сообществе');
            \Yii::app()->message->showMessage();
        }

        if(!$Comment->audio->is_community && $Comment->user_id != \Yii::app()->user->id && $Comment->user_owner_id != \Yii::app()->user->id) {
            \Yii::app()->message->setErrors('danger', 'Вы не можете удалить этот комментарий');
            \Yii::app()->message->showMessage();
        }

        $error = false;
        $t = \Yii::app()->db->beginTransaction();
        try {
            if($Comment->audio->comment_count > 0)
                $Comment->audio->comment_count -= 1;

            if(!$Comment->audio->mUpdate())
                $error = true;

            $Comment->user_deleted_id = \Yii::app()->user->id;
            $Comment->is_deleted = 1;
            if(!$error && !$Comment->delete())
                $error = true;

            if(!$error) {
                $t->commit();
                \Yii::app()->message->setText('success', 'Комментарий удален');
            } else
                $t->rollback();

        } catch (\Exception $ex) {
            $t->rollback();
            \MyException::log($ex);
        }

        \Yii::app()->message->showMessage();
    }
}