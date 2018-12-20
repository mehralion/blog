<?php
namespace application\modules\comment\actions\image;
/**
 * Created by JetBrains PhpStorm.
 * User: nnick
 * Date: 11.08.13
 * Time: 22:33
 * To change this template use File | Settings | File Templates.
 */

class Add extends \CAction
{
    public function run()
    {
        $criteria = new \CDbCriteria();
        $criteria->addCondition('`t`.id = :id');
        $criteria->scopes = array(
            'canComment',
            'activatedStatus',
            'deletedStatus',
            'moderDeletedStatus',
            'truncatedStatus',
        );
        $criteria->params = array(
            ':id' => \Yii::app()->request->getParam('id'),
            ':activatedStatus' => 1,
            ':deletedStatus' => 0,
            ':moderDeletedStatus' => 0,
            ':truncatedStatus' => 0
        );
        $criteria->with = array('user');
        $criteria->mergeWith(\Yii::app()->access->GetCriteriaAccess());

        /** @var \GalleryImage $GalleryImage */
        $GalleryImage = \GalleryImage::model()->find($criteria);
        if(!$GalleryImage) {
            \Yii::app()->message->setErrors('danger', 'Фотография не найдена');
            \Yii::app()->message->showMessage();
        }

        if($GalleryImage->is_community && !\Yii::app()->community->inCommunity() && !\Yii::app()->community->isPublic()) {
            \Yii::app()->message->setErrors('danger', 'Вы не можете оставлять сообщения в этом сообществе');
            \Yii::app()->message->showMessage();
        }

        $post = \Yii::app()->request->getParam('CommentItem');
        if($post) {
            $error = false;
            $t = \Yii::app()->db->beginTransaction();
            try {
                $GalleryImage->comment_count += 1;
                if(!$GalleryImage->mUpdate())
                    $error = true;

                if(!$error) {
                    $Comment = new \CommentItemImage();
                    $Comment->item_id = $GalleryImage->id;
                    $Comment->attributes = $post;
                    $Comment->user_owner_id = $GalleryImage->user_id;
                    $Comment->user_id = \Yii::app()->user->id;
                    if(!$Comment->create()) {
                        $error = true;
                        \Yii::app()->message->setErrors('danger', $Comment);
                    }
                }

                if(!$error) {
                    $t->commit();
                    \Yii::app()->message->setText('success', 'Комментарий добавлен');
                } else
                    $t->rollback();

            } catch (\Exception $ex) {
                $t->rollback();
                \MyException::log($ex);
            }
        }

        \Yii::app()->message->showMessage();
    }
}