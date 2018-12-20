<?php
/**
 * Created by JetBrains PhpStorm.
 * User: nnick
 * Date: 11.08.13
 * Time: 22:58
 * To change this template use File | Settings | File Templates.
 */

namespace application\modules\moder\actions\community;


class Delete extends \CAction
{
    public function run()
    {
        $post = \Yii::app()->request->getParam('ModerLog');
        $silence = \Yii::app()->request->getParam('silence', false);

        $criteria = new \CDbCriteria();
        $criteria->addCondition('id = :id');
        $criteria->scopes = array('moderDeletedStatus', 'deletedStatus', 'truncatedStatus');
        $criteria->params = array(
            ':moderDeletedStatus' => 0,
            ':deletedStatus' => 0,
            ':truncatedStatus' => 0,
            ':id' => \Yii::app()->community->id
        );
        /** @var \Community $Community */
        $Community = \Community::model()->find($criteria);
        if(!isset($Community))
            \MyException::ShowError(404, 'Сообщество не найдено');

        if($post) {
            $error = false;
            $t = \Yii::app()->db->beginTransaction();
            try {
                /** @var \ReportCommunity $Report */
                $Report = \ReportCommunity::model()->find('item_id = :item_id', array(':item_id' => $Community->id));
                if($Report){
                    $Report->status = \ReportCommunity::STATUS_DONE;
                    $Report->moder_reason = $post['moder_reason'];
                    $Report->update_datetime = \DateTimeFormat::format();
                    if(!$Report->save()) {
                        $error = true;
                        \Yii::app()->message->setErrors('danger', $Report);
                    }
                }

                $Community->user_deleted_id = \Yii::app()->user->id;
                $Community->is_moder_deleted = 1;
                if(!$Community->delete()) {
                    $error = true;
                    \Yii::app()->message->setErrors('danger', $Community);
                }

                $silenceId = null;
                if($silence) {
                    /** @var \User $User */
                    $User = \User::model()->findByPk($Community->user_id);
                    $silenceId = \Silence::Add($User, $post['moder_reason']);
                    if(false === $silenceId)
                        $error = true;
                }

                $Log = new \ModerLogCommunity();
                $Log->create_datetime = \DateTimeFormat::format();
                $Log->update_datetime = \DateTimeFormat::format();
                if(!\Yii::app()->user->isAdmin())
                    $Log->scenario = 'moder';
                $Log->moder_id = \Yii::app()->user->id;
                $Log->item_id = $Community->id;
                $Log->user_owner_id = $Community->user_id;
                $Log->moder_reason = $post['moder_reason'];
                $Log->operation_type = \ModerLogCommunity::ITEM_OPERATION_DELETE;
                if(!$Log->save()) {
                    $error = true;
                    \Yii::app()->message->setErrors('danger', $Log);
                }

                if(!$error) {
                    $t->commit();
                    \Yii::app()->message->setText('success', 'Сообщество удалена');
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