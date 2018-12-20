<?php
/**
 * Created by JetBrains PhpStorm.
 * User: nnick
 * Date: 11.08.13
 * Time: 22:42
 * To change this template use File | Settings | File Templates.
 */

namespace application\modules\comment\actions\video;


class Reset extends \CAction
{
    public function run()
    {
        $id = \Yii::app()->request->getParam('id');
        $criteria = new \CDbCriteria();
        $criteria->addCondition('`t`.id = :id');
        //$criteria->addCondition('`t`.user_deleted_id = :user_id');
        $criteria->scopes = array(
            'truncatedStatus',
            'deletedStatus',
            'moderDeletedStatus',
        );
        $criteria->params = array(
            ':id' => $id,
            ':truncatedStatus' => 0,
            ':deletedStatus' => 1,
            ':moderDeletedStatus' => 0,
            //':user_id' => \Yii::app()->user->id,
        );
        $criteria->with = array('video', 'info');

        /** @var \CommentItemVideo $Comment */
        $Comment = \CommentItemVideo::model()->find($criteria);
        if(!$Comment) {
            \Yii::app()->message->setErrors('danger', 'Подходящий комментарий не найден');
            \Yii::app()->message->showMessage();
        }

        if($Comment->video->is_deleted || $Comment->video->is_moder_deleted || $Comment->video->deleted_trunc) {
            \Yii::app()->message->setErrors('danger', 'Видеозапись этого комментария удалена, невозможно восстановить комментарий');
            \Yii::app()->message->showMessage();
        } elseif($Comment->info->is_community && !\Yii::app()->community->isModer()) {
            \Yii::app()->message->setErrors('danger', 'Вы не можете восстановить комментарии в этом сообществе');
            \Yii::app()->message->showMessage();
        } elseif(!$Comment->info->is_community && $Comment->user_id != \Yii::app()->user->id) {
            \Yii::app()->message->setErrors('danger', 'Вы не можете восстановить этот комментарий');
            \Yii::app()->message->showMessage();
        }

        $error = false;
        $t = \Yii::app()->db->beginTransaction();
        try {
            $Video = $Comment->video;
            $Video->comment_count += 1;
            if(!$Video->mUpdate())
                $error = true;

            if(!$Comment->restore())
                $error = true;

            if(!$error) {
                $t->commit();
            } else
                $t->rollback();

        } catch (\Exception $ex) {
            $t->rollback();
            \MyException::log($ex);
        }

        \Yii::app()->message->showMessage();
    }
}