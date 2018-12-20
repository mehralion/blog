<?php
/**
 * Created by JetBrains PhpStorm.
 * User: nnick
 * Date: 11.08.13
 * Time: 22:39
 * To change this template use File | Settings | File Templates.
 */

namespace application\modules\comment\actions\post;


class Trunc extends \CAction
{
    public function run()
    {
        $id = \Yii::app()->request->getParam('id');
        $criteria = new \CDbCriteria();
        $criteria->addCondition('`t`.id = :id');
        $criteria->addCondition('`t`.user_id = :user_id');
        $criteria->addCondition('`t`.is_deleted = 1 or `t`.is_moder_deleted = 1');
        $criteria->scopes = array(
            'truncatedStatus',
        );
        $criteria->params = array(
            ':id' => $id,
            ':truncatedStatus' => 0,
            ':user_id' => \Yii::app()->user->id,
        );
        $criteria->with = array('info');

        /** @var \CommentItemPost $Comment */
        $Comment = \CommentItemPost::model()->find($criteria);
        if(!$Comment) {
            \Yii::app()->message->setErrors('danger', 'Подходящий комментарий не найден');
            \Yii::app()->message->showMessage();
        } elseif($Comment->info->is_community && !\Yii::app()->community->isModer()) {
            \Yii::app()->message->setErrors('danger', 'Вы не можете удалить комментарии в этом сообществе');
            \Yii::app()->message->showMessage();
        } elseif(!$Comment->info->is_community && $Comment->user_id != \Yii::app()->user->id) {
            \Yii::app()->message->setErrors('danger', 'Вы не можете удалить этот комментарий');
            \Yii::app()->message->showMessage();
        }

        $Comment->update_datetime = \DateTimeFormat::format();
        $Comment->deleted_trunc = 1;
        if(!$Comment->save())
            \Yii::app()->message->setErrors('danger', $Comment);
        else
            \Yii::app()->message->setText('success', 'Комментарий удален навсегда');

        \Yii::app()->message->showMessage();
    }
}