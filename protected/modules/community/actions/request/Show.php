<?php
namespace application\modules\community\actions\request;
use application\modules\community\components\CommunityAction;

/**
 * Created by JetBrains PhpStorm.
 * User: Николай
 * Date: 13.06.13
 * Time: 13:13
 * To change this template use File | Settings | File Templates.
 *
 * @package application.gallery.actions.image
 */
class Show extends CommunityAction
{
    public function run()
    {
        $model = \Yii::app()->community->getModel();
        if($model->is_deleted || $model->is_moder_deleted) {
            \Yii::app()->message->setErrors('danger', 'Сообщество удалено');
            \Yii::app()->message->showMessage();
        }

        $criteria = new \CDbCriteria();
        $criteria->addCondition('`t`.community_id = :community_id');
        $criteria->scopes = array('deletedStatus', 'activatedStatus', 'truncatedStatus');
        $criteria->params = array(':deletedStatus' => 0, ':activatedStatus' => 1, ':truncatedStatus' => 0, ':community_id' => \Yii::app()->community->id);

        $postCount = \Post::model()->count($criteria);
        $imageCount = \GalleryImage::model()->count($criteria);
        $audioCount = \GalleryAudio::model()->count($criteria);
        $videoCount = \GalleryVideo::model()->count($criteria);

        $criteria = new \CDbCriteria();
        $criteria->with = array('info');
        $criteria->addCondition('`t`.item_id = :community_id');
        $criteria->scopes = array('deletedStatus', 'activatedStatus', 'truncatedStatus');
        $criteria->params = array(':deletedStatus' => 0, ':activatedStatus' => 1, ':truncatedStatus' => 0, ':community_id' => \Yii::app()->community->id);
        $commentCount = \CommentItemCommunity::model()->count($criteria);

        $criteria = new \CDbCriteria();
        $criteria->scopes = array('deletedStatus');
        $criteria->addCondition('`t`.community_id = :community_id');
        $criteria->params = array(':community_id' => \Yii::app()->community->id, ':deletedStatus' => 0);

        /** @var \CommunityUser[] $models */
        $models = \CommunityUser::model()->findAll($criteria);

        $criteria = new \CDbCriteria();
        $criteria->scopes = array('deletedStatus', 'moders');
        $criteria->addCondition('`t`.community_id = :community_id');
        $criteria->params = array(':community_id' => \Yii::app()->community->id, ':deletedStatus' => 0);

        /** @var \CommunityUser[] $models */
        $moders = \CommunityUser::model()->findAll($criteria);

        $criteria = new \CDbCriteria();
        $criteria->addCondition('`t`.item_id = :user_id');
        $criteria->addCondition('`t`.post = 1 or `t`.image = 1 or `t`.audio = 1 or `t`.video = 1 or `t`.comment = 1');
        $criteria->with = array('subscribeUser');
        $criteria->params = array(':user_id' => \Yii::app()->community->id);
        $readMeSubscribe = \SubscribeCommunity::model()->findAll($criteria);

        $this->controller->render('show', array(
            'model' => \Yii::app()->community->getModel(),
            'comment' => $commentCount,
            'post' => $postCount,
            'image' => $imageCount,
            'audio' => $audioCount,
            'video' => $videoCount,
            'rating' => \Yii::app()->community->rating,
            'users' => $models,
            'moders' => $moders,
            'readMeSubscribe' => $readMeSubscribe,
        ));
    }
}