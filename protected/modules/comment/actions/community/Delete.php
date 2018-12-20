<?php
/**
 * Created by JetBrains PhpStorm.
 * User: nnick
 * Date: 11.08.13
 * Time: 22:39
 * To change this template use File | Settings | File Templates.
 */

namespace application\modules\comment\actions\community;


class Delete extends \CAction
{
    public function run()
    {
        $criteria = new \CDbCriteria();
        $criteria->addCondition('`t`.id = :id');
        $criteria->with = array(
            'community' => array(
                'joinType' => 'inner join',
                'scopes' => array(
                    'activatedStatus',
                    'deletedStatus',
                    'moderDeletedStatus',
                    'truncatedStatus',
                ),
                'params' => array(
                    ':activatedStatus' => 1,
                    ':deletedStatus' => 0,
                    ':moderDeletedStatus' => 0,
                    ':truncatedStatus' => 0
                ),
                'with' => array(
                    'inCommunity'
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
            ':id' => \Yii::app()->request->getParam('id'),
            ':activatedStatus' => 1,
            ':deletedStatus' => 0,
            ':moderDeletedStatus' => 0,
            ':truncatedStatus' => 0
        );

        /** @var \CommentItemCommunity $Comment */
        $Comment = \CommentItemCommunity::model()->find($criteria);
        if(!$Comment) {
            \Yii::app()->message->setErrors('danger', 'Комментарий не найден');
            \Yii::app()->message->showMessage();
        }

        if(!\Yii::app()->community->isModer() && $Comment->user_id != \Yii::app()->user->id) {
            \Yii::app()->message->setErrors('danger', 'Вы не можете удалять сообщения в этом сообществе');
            \Yii::app()->message->showMessage();
        }

        $error = false;
        $t = \Yii::app()->db->beginTransaction();
        try {
            $Community = \Yii::app()->community->getModel();

            if($Community->comment_count > 0)
                $Community->comment_count -= 1;
            if(!$Community->mUpdate())
                $error = true;

            $Comment->is_deleted = 1;
            $Comment->user_deleted_id = \Yii::app()->user->id;
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