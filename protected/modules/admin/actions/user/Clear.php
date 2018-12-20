<?php
namespace application\modules\admin\actions\user;
set_time_limit(0);
/**
 * Created by JetBrains PhpStorm.
 * User: Николай
 * Date: 13.06.13
 * Time: 13:13
 * To change this template use File | Settings | File Templates.
 *
 * @package application.post.actions.index
 */
class Clear extends \CAction
{
    public function run()
    {
        $user_id = \Yii::app()->request->getParam('user_id');
        /** @var \User $model */
        $model = \User::model()->findByPk($user_id);
        if(!$model) {
            \Yii::app()->message->setErrors('danger', 'Блогер не найден');
            \Yii::app()->message->showMessage();
        }


        $error = false;
        $t = \Yii::app()->db->beginTransaction();
        try {

            $criteria = new \CDbCriteria();
            $criteria->addCondition('user_id = :user_id');
            //$criteria->scopes = array('activatedStatus', 'truncatedStatus');
            $criteria->params = array(
                ':user_id' => $model->id,
                //':activatedStatus' => 1,
                //':truncatedStatus' => 0
            );
            \Post::model()->updateAll(array('deleted_trunc' => 1), $criteria);
            \GalleryAlbumImage::model()->updateAll(array('deleted_trunc' => 1), $criteria);
            \GalleryAlbumVideo::model()->updateAll(array('deleted_trunc' => 1), $criteria);
            \GalleryAlbumAudio::model()->updateAll(array('deleted_trunc' => 1), $criteria);
            \GalleryAudio::model()->updateAll(array('deleted_trunc' => 1), $criteria);
            \GalleryImage::model()->updateAll(array('deleted_trunc' => 1), $criteria);
            \GalleryVideo::model()->updateAll(array('deleted_trunc' => 1), $criteria);
            \CommentItem::model()->updateAll(array('deleted_trunc' => 1, 'user_deleted_id' => $model->id), $criteria);

            $criteria = new \CDbCriteria();
            $criteria->addCondition('user_owner_id = :user_id');
            //$criteria->scopes = array('activatedStatus', 'truncatedStatus');
            $criteria->params = array(
                ':user_id' => $model->id,
                //':activatedStatus' => 1,
                //':truncatedStatus' => 0
            );
            \ItemInfo::model()->updateAll(array('deleted_trunc' => 1), $criteria);

            $criteria = new \CDbCriteria();
            $criteria->addCondition('user_id = :u_id or friend_id = :u_id');
            $criteria->params = array(
                ':u_id' => $model->id,
            );
            \UserFriend::model()->deleteAll($criteria);

            $criteria = new \CDbCriteria();
            $criteria->addCondition('user_id = :u_id or friend_id = :u_id');
            $criteria->addCondition('reciver_status = :reciver_status');
            $criteria->params = array(
                ':u_id' => $model->id,
                ':reciver_status' => \FriendRequest::STATUS_PENDING
            );
            \FriendRequest::model()->updateAll(array('reciver_status' => \FriendRequest::STATUS_FAIL), $criteria);

            $criteria = new \CDbCriteria();
            $criteria->addCondition('user_id = :u_id');
            $criteria->params = array(':u_id' => $model->id,);
            \RatingItem::model()->updateAll(array('is_deleted' => 1), $criteria);

            $criteria = new \CDbCriteria();
            $criteria->addCondition('subscribe_user_id = :subscribe_user_id');
            $criteria->params = array(':subscribe_user_id' => $model->id);
            \SubscribeUser::model()->updateAll(array('post' => 0, 'image' => 0, 'video' => 0, 'audio' => 0, 'comment' => 0), $criteria);

            $criteria = new \CDbCriteria();
            $criteria->addCondition('subscribe_user_id = :subscribe_user_id');
            //$criteria->scopes = array('deletedStatus');
            $criteria->params = array(
                ':subscribe_user_id' => $model->id,
                //':deletedStatus' => 0
            );
            \SubscribeDebate::model()->updateAll(array('is_deleted' => 1), $criteria);


            if(!$error) {
                $model->userProfile->rating = 0;
                $model->userProfile->is_croped = 0;
                $model->userProfile->description = '';
                $model->userProfile->avatar_path = '';
                if(!$model->userProfile->save())
                    $error = true;
            }

            if(!$error) {
                $model->is_silenced = 0;
                $model->silence_end = \DateTimeFormat::format();
                if(!$model->save())
                    $error = true;
            }

            if(!$error) {
                $t->commit();
                \Yii::app()->message->setText('success', 'Успешно почистили блогера');
            } else {
                $t->rollback();
                \Yii::app()->message->setErrors('danger', 'Возникли проблемы');
            }
        } catch (\Exception $ex) {
            $t->rollback();
            \MyException::log($ex);
            \Yii::app()->message->setErrors('danger', 'Возникли проблемы, подробности в логе');
        }

        \Yii::app()->message->showMessage();
    }
}