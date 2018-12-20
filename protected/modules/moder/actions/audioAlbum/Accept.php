<?php
/**
 * Created by JetBrains PhpStorm.
 * User: nnick
 * Date: 12.08.13
 * Time: 1:02
 * To change this template use File | Settings | File Templates.
 */

namespace application\modules\moder\actions\audioAlbum;


class Accept extends \CAction
{
    public function run()
    {
        $id = \Yii::app()->request->getParam('id');
        $silence = \Yii::app()->request->getParam('silence', false);
        /** @var \Report $Report */
        $Report = \ReportAudioAlbum::model()->findByPk($id);
        if(!isset($Report) || $Report->status == \ReportAudioAlbum::STATUS_DONE)
            \MyException::ShowError(404, 'Жалоба не существует или уже обработана!');

        if(!\Yii::app()->user->isAdmin())
            $Report->scenario = 'moder';
        $post = \Yii::app()->request->getParam('ModerLog');

        if($post) {
            $criteria = new \CDbCriteria();
            $criteria->addCondition('id = :id');
            $criteria->scopes = array('deletedStatus', 'moderDeletedStatus', 'truncatedStatus');
            $criteria->params = array(':deletedStatus' => 0, ':moderDeletedStatus' => 0, ':truncatedStatus' => 0, ':id' => $Report->item_id);
            /** @var \GalleryAlbumAudio $GalleryAlbumAudio */
            $GalleryAlbumAudio = \GalleryAlbumAudio::model()->find($criteria);

            $error = false;
            $t = \Yii::app()->db->beginTransaction();
            try {
                $Report->attributes = $post;
                $Report->status = \ReportAudioAlbum::STATUS_DONE;
                $Report->moder_reason = $post['moder_reason'];
                $Report->update_datetime = \DateTimeFormat::format();
                if(!$Report->save()) {
                    $error = true;
                    \Yii::app()->message->setErrors('danger', $Report);
                }

                $GalleryAlbumAudio->user_deleted_id = \Yii::app()->user->id;
                $GalleryAlbumAudio->is_moder_deleted = 1;
                if(!$GalleryAlbumAudio->delete()) {
                    $error = true;
                    \Yii::app()->message->setErrors('danger', $GalleryAlbumAudio);
                }

                $silenceId = null;
                if($silence) {
                    /** @var \User $User */
                    $User = \User::model()->findByPk($GalleryAlbumAudio->user_id);
                    $silenceId = \Silence::Add($User, $post['moder_reason']);
                    if(false === $silenceId)
                        $error = true;
                }

                $Log = new \ModerLogAudioAlbum();
                $Log->create_datetime = \DateTimeFormat::format();
                $Log->update_datetime = \DateTimeFormat::format();
                if(!\Yii::app()->user->isAdmin())
                    $Log->scenario = 'moder';
                $Log->moder_id = \Yii::app()->user->id;
                $Log->item_id = $GalleryAlbumAudio->id;
                $Log->user_owner_id = $Report->user_owner_id;
                $Log->moder_reason = $post['moder_reason'];
                $Log->operation_type = \ModerLogAudioAlbum::ITEM_OPERATION_DELETE;
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