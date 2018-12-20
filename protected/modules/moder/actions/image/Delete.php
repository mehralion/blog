<?php
namespace application\modules\moder\actions\image;
/**
 * Created by JetBrains PhpStorm.
 * User: nnick
 * Date: 11.08.13
 * Time: 22:22
 * To change this template use File | Settings | File Templates.
 */

class Delete extends \CAction
{
    public function run()
    {
        $id = \Yii::app()->request->getParam('id');
        $post = \Yii::app()->request->getParam('ModerLog');
        $silence = \Yii::app()->request->getParam('silence', false);

        $criteria = new \CDbCriteria();
        $criteria->addCondition('id = :id');
        $criteria->scopes = array('moderDeletedStatus', 'deletedStatus', 'truncatedStatus');
        $criteria->params = array(
            ':moderDeletedStatus' => 0,
            ':deletedStatus' => 0,
            ':truncatedStatus' => 0,
            ':id' => $id
        );
        /** @var \GalleryImage $GalleryImage */
        $GalleryImage = \GalleryImage::model()->find($criteria);
        if(!isset($GalleryImage))
            \MyException::ShowError(404, 'Фотография не найдена');

        if($post) {
            $error = false;
            $t = \Yii::app()->db->beginTransaction();
            try {
                /** @var \ReportImage $Report */
                $Report = \ReportImage::model()->find('item_id = :item_id', array(':item_id' => $GalleryImage->id));
                if($Report){
                    $Report->status = \ReportImage::STATUS_DONE;
                    $Report->moder_reason = $post['moder_reason'];
                    $Report->update_datetime = \DateTimeFormat::format();
                    if(!$Report->save()) {
                        $error = true;
                        \Yii::app()->message->setErrors('danger', $Report);
                    }
                }

                $GalleryImage->user_deleted_id = \Yii::app()->user->id;
                $GalleryImage->is_moder_deleted = 1;
                if(!$GalleryImage->delete()) {
                    $error = true;
                    \Yii::app()->message->setErrors('danger', $GalleryImage);
                }

                $silenceId = null;
                if($silence) {
                    /** @var \User $User */
                    $User = \User::model()->findByPk($GalleryImage->user_id);
                    $silenceId = \Silence::Add($User, $post['moder_reason']);
                    if(false === $silenceId)
                        $error = true;
                }

                $Log = new \ModerLogImage();
                $Log->update_datetime = \DateTimeFormat::format();
                $Log->create_datetime = \DateTimeFormat::format();
                if(!\Yii::app()->user->isAdmin())
                    $Log->scenario = 'moder';
                $Log->moder_id = \Yii::app()->user->id;
                $Log->item_id = $GalleryImage->id;
                $Log->user_owner_id = $GalleryImage->user_id;
                $Log->moder_reason = $post['moder_reason'];
                $Log->operation_type = \ModerLogImage::ITEM_OPERATION_DELETE;
                if(!$Log->save()) {
                    $error = true;
                    \Yii::app()->message->setErrors('danger', $Log);
                }

                if(!$error) {
                    $t->commit();
                    \Yii::app()->message->setText('success', 'Фотография удалена');
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