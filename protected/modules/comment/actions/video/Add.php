<?php
/**
 * Created by JetBrains PhpStorm.
 * User: nnick
 * Date: 11.08.13
 * Time: 22:40
 * To change this template use File | Settings | File Templates.
 */

namespace application\modules\comment\actions\video;


class Add extends \CAction
{
    public function run()
    {
        /** @var integer $id $_GET['id'] GalleryVideo ID */
        $id = \Yii::app()->request->getParam('id');
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
            ':id' => $id,
            ':activatedStatus' => 1,
            ':deletedStatus' => 0,
            ':moderDeletedStatus' => 0,
            ':truncatedStatus' => 0
        );
        $criteria->with = array('user');
        $criteria->mergeWith(\Yii::app()->access->GetCriteriaAccess());

        /** @var \GalleryVideo $Video */
        $Video = \GalleryVideo::model()->find($criteria);
        if(!$Video) {
            \Yii::app()->message->setErrors('danger', 'Видеозапись не найдена');
            \Yii::app()->message->showMessage();
        }

        if($Video->is_community && !\Yii::app()->community->inCommunity() && !\Yii::app()->community->isPublic()) {
            \Yii::app()->message->setErrors('danger', 'Вы не можете оставлять сообщения в этом сообществе');
            \Yii::app()->message->showMessage();
        }

        $post = \Yii::app()->request->getParam('CommentItem');
        if($post) {
            $error = false;
            $t = \Yii::app()->db->beginTransaction();
            try {
                $Video->comment_count += 1;
                if(!$Video->mUpdate())
                    $error = true;

                if(!$error) {
                    $Comment = new \CommentItemVideo();
                    $Comment->item_id = $Video->id;
                    $Comment->attributes = $post;
                    $Comment->user_owner_id = $Video->user_id;
                    $Comment->user_id = \Yii::app()->user->id;
                    if(!$Comment->create())
                        $error = true;
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