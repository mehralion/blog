<?php
/**
 * Created by JetBrains PhpStorm.
 * User: nnick
 * Date: 11.08.13
 * Time: 22:59
 * To change this template use File | Settings | File Templates.
 */

namespace application\modules\moder\actions\audioAlbum;


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
        /** @var \GalleryAlbumAudio $GalleryAlbumAudio */
        $GalleryAlbumAudio = \GalleryAlbumAudio::model()->find($criteria);
        if(!isset($GalleryAlbumAudio))
            \MyException::ShowError(404, 'Видео не найдено');

        if($post) {
            $error = false;
            $t = \Yii::app()->db->beginTransaction();
            try {
                /** @var \ReportAudioAlbum $Report */
                $Report = \ReportAudioAlbum::model()->find('item_id = :item_id', array(':item_id' => $GalleryAlbumAudio->id));
                if($Report){
                    $Report->status = \ReportAudioAlbum::STATUS_DONE;
                    $Report->moder_reason = $post['moder_reason'];
                    $Report->update_datetime = \DateTimeFormat::format();
                    if(!$Report->save()) {
                        $error = true;
                        \Yii::app()->message->setErrors('danger', $Report);
                    }
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
                $Log->update_datetime = \DateTimeFormat::format();
                $Log->create_datetime = \DateTimeFormat::format();
                if(!\Yii::app()->user->isAdmin())
                    $Log->scenario = 'moder';
                $Log->moder_id = \Yii::app()->user->id;
                $Log->item_id = $GalleryAlbumAudio->id;
                $Log->user_owner_id = $GalleryAlbumAudio->user_id;
                $Log->moder_reason = $post['moder_reason'];
                $Log->operation_type = \ModerLogAudioAlbum::ITEM_OPERATION_DELETE;
                if(!$Log->save()) {
                    $error = true;
                    \Yii::app()->message->setErrors('danger', $Log);
                }

                if(!$error) {
                    $t->commit();
                    \Yii::app()->message->setText('success', 'Видео удалено');
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