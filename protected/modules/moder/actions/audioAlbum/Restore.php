<?php
namespace application\modules\moder\actions\audioAlbum;
/**
 * Created by JetBrains PhpStorm.
 * User: nnick
 * Date: 11.08.13
 * Time: 22:22
 * To change this template use File | Settings | File Templates.
 */

class Restore extends \CAction
{
    public function run()
    {
        $id = \Yii::app()->request->getParam('id');
        $post = \Yii::app()->request->getParam('ModerLog');
        /** @var \ModerLogAudioAlbum $Log */
        $Log = \ModerLogAudioAlbum::model()->findByPk($id);
        if(!isset($Log) )
            \MyException::ShowError(404, 'Возникла ошибка, повторите позже!');

        $criteria = new \CDbCriteria();
        $criteria->addCondition('id = :id');
        $criteria->scopes = array('moderDeletedStatus');
        $criteria->params = array(
            ':moderDeletedStatus' => 1,
            ':id' => $Log->item_id
        );
        /** @var \GalleryAlbumAudio $GalleryAlbumAudio */
        $GalleryAlbumAudio = \GalleryAlbumAudio::model()->find($criteria);
        if(!isset($GalleryAlbumAudio))
            \MyException::ShowError(404, 'Видео не найдено');

        if($post) {
            if(!isset($post['moder_reason']) || $post['moder_reason'] == '') {
                \Yii::app()->message->setErrors('danger', 'Вы не ввели причину');
                \Yii::app()->message->showMessage();
            }

            $error = false;
            $t = \Yii::app()->db->beginTransaction();
            try {
                if(!$GalleryAlbumAudio->restore()) {
                    $error = true;
                    \Yii::app()->message->setErrors('danger', $GalleryAlbumAudio);
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
                $Log->operation_type = \ModerLogAudioAlbum::ITEM_OPERATION_RESTORE;
                if(!$Log->save()) {
                    $error = true;
                    \Yii::app()->message->setErrors('danger', $Log);
                }

                if(!$error) {
                    $t->commit();
                    \Yii::app()->message->setText('success', 'Видео восстановленно');
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

            $this->controller->renderPartial('ajax.moderRestore', array(
                'model' => $model
            ), false, true);
        }
    }
}