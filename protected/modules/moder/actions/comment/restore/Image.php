<?php
/**
 * Created by JetBrains PhpStorm.
 * User: nnick
 * Date: 11.08.13
 * Time: 22:56
 * To change this template use File | Settings | File Templates.
 */

namespace application\modules\moder\actions\comment\restore;


class Image extends \CAction
{
    public function run()
    {
        $id = \Yii::app()->request->getParam('id');
        $post = \Yii::app()->request->getParam('ModerLog');
        /** @var \ModerLog $Log */
        $Log = \ModerLogComment::model()->findByPk($id);
        if(!isset($Log) )
            \MyException::ShowError(404, 'Возникла ошибка, повторите позже!');

        $criteria = new \CDbCriteria();
        $criteria->addCondition('id = :id');
        $criteria->scopes = array('moderDeletedStatus', 'truncatedStatus', 'deletedStatus');
        $criteria->params = array(
            ':moderDeletedStatus' => 1,
            ':truncatedStatus' => 0,
            ':deletedStatus' => 0,
            ':id' => $Log->item_id
        );
        /** @var \CommentItem $CommentItem */
        $CommentItem = \CommentItemImage::model()->find($criteria);
        if(!isset($CommentItem))
            \MyException::ShowError(404, 'Комментарий не найден');

        if($post) {
            $error = false;
            $t = \Yii::app()->db->beginTransaction();
            try {
                $CommentItem->is_moder_deleted = 0;
                if(!$CommentItem->restore()) {
                    $error = true;
                    \Yii::app()->message->setErrors('danger', $CommentItem);
                }

                /** @var \GalleryImage $GalleryImage */
                $GalleryImage = \GalleryAlbumAudio::model()->findByPk($CommentItem->item_id);
                $GalleryImage->comment_count += 1;
                if(!$GalleryImage->mUpdate()) {
                    $error = true;
                    \Yii::app()->message->setErrors('danger', $GalleryImage);
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
                $Log->operation_type = \ModerLogComment::ITEM_OPERATION_RESTORE;
                if(!$Log->save()) {
                    $error = true;
                    \Yii::app()->message->setErrors('danger', $Log);
                }

                if(!$error) {
                    $t->commit();
                    \Yii::app()->message->setText('success', 'Комментарий восстановлен');
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