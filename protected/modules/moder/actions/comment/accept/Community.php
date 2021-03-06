<?php
/**
 * Created by JetBrains PhpStorm.
 * User: nnick
 * Date: 11.08.13
 * Time: 22:56
 * To change this template use File | Settings | File Templates.
 */

namespace application\modules\moder\actions\comment\accept;


class Community extends \CAction
{
    public function run()
    {
        $id = \Yii::app()->request->getParam('id');
        $silence = \Yii::app()->request->getParam('silence', false);
        /** @var \Report $Report */
        $Report = \ReportComment::model()->findByPk($id);
        if(!isset($Report) || $Report->status == \ReportComment::STATUS_DONE)
            \MyException::ShowError(404, 'Жалоба не существует или уже обработана!');

        if(!\Yii::app()->user->isAdmin())
            $Report->scenario = 'moder';
        $post = \Yii::app()->request->getParam('ModerLog');
        if($post) {
            $criteria = new \CDbCriteria();
            $criteria->addCondition('id = :id');
            $criteria->scopes = array('deletedStatus', 'moderDeletedStatus', 'truncatedStatus');
            $criteria->params = array(':deletedStatus' => 0, ':moderDeletedStatus' => 0, ':truncatedStatus' => 0, ':id' => $Report->item_id);
            /** @var \CommentItem $CommentItem */
            $CommentItem = \CommentItemCommunity::model()->find($criteria);
            $error = false;
            $t = \Yii::app()->db->beginTransaction();
            try {
                $Report->status = \ReportComment::STATUS_DONE;
                $Report->moder_reason = $post['moder_reason'];
                $Report->update_datetime = \DateTimeFormat::format();
                if(!$Report->save()) {
                    $error = true;
                    \Yii::app()->message->setErrors('danger', $Report);
                }

                $CommentItem->is_moder_deleted = 1;
                $CommentItem->user_deleted_id = \Yii::app()->user->id;
                if(!$CommentItem->save()) {
                    $error = true;
                    \Yii::app()->message->setErrors('danger', $CommentItem);
                }

                /** @var \Community $Community */
                $Community = \Community::model()->findByPk($CommentItem->item_id);
                if($Community->comment_count > 0)
                    $Community->comment_count -= 1;
                if(!$Community->mUpdate()) {
                    $error = true;
                    \Yii::app()->message->setErrors('danger', $Community);
                }

                if($silence) {
                    /** @var \User $User */
                    $User = \User::model()->findByPk($Report->user_owner_id);
                    $silenceId = \Silence::Add($User, $post['moder_reason']);
                    if(false === $silenceId)
                        $error = true;
                }

                $Log = new \ModerLogComment();
                $Log->update_datetime = \DateTimeFormat::format();
                $Log->create_datetime = \DateTimeFormat::format();
                if(!\Yii::app()->user->isAdmin())
                    $Log->scenario = 'moder';
                $Log->moder_id = \Yii::app()->user->id;
                $Log->item_id = $Report->item_id;
                $Log->user_owner_id = $Report->user_owner_id;
                $Log->moder_reason = $post['moder_reason'];
                $Log->operation_type = \ModerLogComment::ITEM_OPERATION_DELETE;
                $Log->is_report = true;
                if(!$Log->save()) {
                    $error = true;
                    \Yii::app()->message->setErrors('danger', $Log);
                }

                if(!$error) {
                    $t->commit();
                    \Yii::app()->message->setText('success', 'Жалоба обработана');
                } else
                    $t->rollback();

            } catch (\Exception $ex) {
                $t->rollback();
                \MyException::log($ex);
            }

            \Yii::app()->message->showMessage();
        } else {
            $model = new \ModerLog();
            if(!\Yii::app()->user->isAdmin())
                $model->scenario = 'moder';

            $this->controller->renderPartial('ajax.moderDelete', array(
                'model' => $model
            ), false, true);
        }
    }
}