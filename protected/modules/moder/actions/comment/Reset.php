<?php
/**
 * Created by JetBrains PhpStorm.
 * User: nnick
 * Date: 12.08.13
 * Time: 1:00
 * To change this template use File | Settings | File Templates.
 */

namespace application\modules\moder\actions\comment;


class Reset extends \CAction
{
    public function run()
    {
        $id = \Yii::app()->request->getParam('id');
        /** @var \Report $Report */
        $Report = \ReportComment::model()->findByPk($id);
        if(!isset($Report) || $Report->status == \ReportComment::STATUS_DONE)
            \MyException::ShowError(404, 'Жалоба не существует или уже обработана!');

        $error = false;
        $t = \Yii::app()->db->beginTransaction();
        try {
            $Report->status = \ReportComment::STATUS_NOTHING;
            $Report->update_datetime = \DateTimeFormat::format();
            if(!$Report->save()) {
                $error = true;
                \Yii::app()->message->setErrors('success', $Report);
            }

            $Log = new \ModerLogComment();
            $Log->create_datetime = \DateTimeFormat::format();
            $Log->update_datetime = \DateTimeFormat::format();
            $Log->moder_id = \Yii::app()->user->id;
            $Log->item_id = $Report->item_id;
            $Log->user_owner_id = $Report->user_owner_id;
            $Log->operation_type = \ModerLogComment::ITEM_OPERATION_DECLINE;
            $Log->is_report = true;
            if(!$Log->save()) {
                $error = true;
                \Yii::app()->message->setErrors('danger', $Log);
            }

            if(!$error) {
                $t->commit();
                \Yii::app()->message->setText('success', 'Жалоба отклонена');
            } else
                $t->rollback();

        } catch (\Exception $ex) {
            $t->rollback();
            \MyException::log($ex);
        }

        \Yii::app()->message->url = \Yii::app()->request->getUrlReferrer();
        \Yii::app()->message->showMessage();
    }
}