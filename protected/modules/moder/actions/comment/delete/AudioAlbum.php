<?php
/**
 * Created by JetBrains PhpStorm.
 * User: nnick
 * Date: 11.08.13
 * Time: 22:56
 * To change this template use File | Settings | File Templates.
 */

namespace application\modules\moder\actions\comment\delete;


class AudioAlbum extends \CAction
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
        /** @var \CommentItemAudio $CommentItem */
        $CommentItem = \CommentItemAudio::model()->find($criteria);
        if(!isset($CommentItem))
            \MyException::ShowError(404, 'Комментарий не найден');

        if($post) {
            $error = false;
            $t = \Yii::app()->db->beginTransaction();
            try {
                /** @var \ReportComment $Report */
                $Report = \ReportComment::model()->find('item_id = :item_id', array(':item_id' => $CommentItem->id));
                if($Report){
                    $Report->status = \ReportComment::STATUS_DONE;
                    $Report->moder_reason = $post['moder_reason'];
                    $Report->update_datetime = \DateTimeFormat::format();
                    if(!$Report->save()) {
                        $error = true;
                        \Yii::app()->message->setErrors('danger', $Report);
                    }
                }

                $CommentItem->user_deleted_id = \Yii::app()->user->id;
                $CommentItem->is_moder_deleted = 1;
                if(!$CommentItem->delete()) {
                    $error = true;
                    \Yii::app()->message->setErrors('danger', $CommentItem);
                }

                $silenceId = null;
                if($silence) {
                    /** @var \User $User */
                    $User = \User::model()->findByPk($CommentItem->user_id);
                    $silenceId = \Silence::Add($User, $post['moder_reason']);
                    if(false === $silenceId)
                        $error = true;
                }

                /** @var \GalleryAlbumAudio $GalleryAudioAlbum */
                $GalleryAudioAlbum = \GalleryAlbumAudio::model()->findByPk($CommentItem->item_id);
                if($GalleryAudioAlbum->comment_count > 0)
                    $GalleryAudioAlbum->comment_count -= 1;
                $GalleryAudioAlbum->update_datetime = \DateTimeFormat::format();
                if(!$GalleryAudioAlbum->mUpdate()) {
                    $error = true;
                    \Yii::app()->message->setErrors('danger', $GalleryAudioAlbum);
                }

                $Log = new \ModerLogComment();
                $Log->create_datetime = \DateTimeFormat::format();
                $Log->update_datetime = \DateTimeFormat::format();
                if(!\Yii::app()->user->isAdmin())
                    $Log->scenario = 'moder';
                $Log->moder_id = \Yii::app()->user->id;
                $Log->item_id = $CommentItem->id;
                $Log->user_owner_id = $CommentItem->user_id;
                $Log->moder_reason = $post['moder_reason'];
                $Log->operation_type = \ModerLogComment::ITEM_OPERATION_DELETE;
                if(!$Log->save()) {
                    $error = true;
                    \Yii::app()->message->setErrors('danger', $Log);
                }

                if(!$error) {
                    $t->commit();
                    \Yii::app()->message->setText('success', 'Комментарий удален');
                    \Yii::app()->message->url = \Yii::app()->request->urlReferrer;
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