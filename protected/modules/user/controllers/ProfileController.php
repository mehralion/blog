<?php
namespace application\modules\user\controllers;
use Aws\CloudFront\Exception\Exception;

/**
 * Class ProfileController
 *
 * @package application.user.controllers
 */
class ProfileController extends \FrontController
{
    public function filters()
    {
        return array(
            'accessControl',
            'ajaxOnly + crop',
        );
    }

    public function accessRules()
    {
        return array(
            array('deny',
                'actions'=>array('update', 'crop'),
                'users' => array('?'),
            )
        );
    }

    public function actionCrop()
    {
        /** @var \UserProfile $UserProfile */
        $UserProfile = \UserProfile::model()->findByPk(\Yii::app()->user->id);
        if(null === $UserProfile)
            return;

        $post = \Yii::app()->request->getParam('crop');
        if(!empty($post)) {
            $t = \Yii::app()->db->beginTransaction();
            try {
                $fileName = \Yii::app()->basePath.'/..'.$UserProfile->getBaseUrl().'/'.$UserProfile->avatar_path;
                if(!\Yii::app()->ih
                    ->load($fileName)
                    ->crop($post['w'], $post['h'], $post['x'], $post['y']) // $width, $height, $startX = false, $startY = false
                    ->resize(98, 98)
                    ->save($fileName))
                    throw new \Exception();

                \Yii::app()->aws->delete($UserProfile->getBaseUrl().'/'.$UserProfile->avatar_path);
                $UserProfile->avatar_path = \Yii::app()->user->id.'_'.md5(time()).'.jpg';
                $UserProfile->is_croped = 1;

                if(!$UserProfile->save())
                    throw new \Exception();

                \Yii::app()->aws->upload($fileName, $UserProfile->getBaseUrl().'/'.$UserProfile->avatar_path);

                if(file_exists($fileName) && !is_dir($fileName))
                    unlink($fileName);

                $t->commit();
                \Yii::app()->message->setOther(array('success' => true));
                \Yii::app()->message->setText('success', 'Аватар изменен');
            } catch (\Exception $ex) {
                $t->rollback();
                \MyException::log($ex);
            }

            \Yii::app()->message->showMessage();
        }
    }

    public function actionUpdate()
    {
        $post = \Yii::app()->request->getParam('UserProfile');
        if(!empty($post)) {
            /** @var \UserProfile $UserProfile */
            $UserProfile = \UserProfile::model()->findByPk(\Yii::app()->user->id);
            if(null === $UserProfile)
                $UserProfile = new \UserProfile();

            $file = \CUploadedFile::getInstance($UserProfile, 'avatar_path');
            if(!empty($file)) {
                $UserProfile->is_croped = 0;
                if($UserProfile->avatar_path === null || $UserProfile->avatar_path == '')
                    $UserProfile->avatar_path = \Yii::app()->user->id.'_'.md5(time()).'.jpg';
            } else
                $UserProfile->attributes = $post;

            $t = \Yii::app()->db->beginTransaction();
            try {
                if($UserProfile->userDj !== null && $UserProfile->bank !== null)
                    \UserDj::model()->updateAll(array('game_bank' => $UserProfile->bank), 'user_id = :user_id', array(':user_id' => $UserProfile->user_id));

                if($UserProfile->save()) {
                    if(!empty($file))  // check if uploaded file is set or not
                    {
                        $origin = \Yii::app()->basePath.'/..'.$UserProfile->getBaseUrl() . '/' . $UserProfile->avatar_path;
                        if(!$file->saveAs($origin))
                            throw new \Exception();
                    }

                    $t->commit();
                    \Yii::app()->message->setText('success', 'Данные обновленны!');
                }
                    \Yii::app()->message->setErrors('danger', $UserProfile);
            } catch (\Exception $ex) {
                $t->rollback();
                \Yii::app()->message->setErrors('danger', 'Не удалось обновить данные!');
            }
        }
        \Yii::app()->message->url = \Yii::app()->createUrl('/user/profile/show', array('gameId' => \Yii::app()->user->getGameId()));
        \Yii::app()->message->showMessage();
    }

    public function actionShow()
    {
        if(\Yii::app()->userOwn->is_silenced && \Yii::app()->user->isModer()) {
            $date = \DateTimeFormat::format(\Yii::app()->params['dateTime']['community'], \Yii::app()->userOwn->silence_end);
            \Yii::app()->user->setFlash('warning', 'Блогер будет молчать до '.$date);
        }

        $userId = null === \Yii::app()->userOwn->id ? \Yii::app()->user->id : \Yii::app()->userOwn->id;

        $criteria = new \CDbCriteria();
        $criteria->addCondition('`t`.user_id = :user_id');
        $criteria->scopes = array('activatedStatus', 'deletedStatus', 'moderDeletedStatus', 'truncatedStatus');
        $criteria->params = array(
            ':user_id' =>$userId,
            ':activatedStatus' => 1,
            ':deletedStatus' => 0,
            ':moderDeletedStatus' => 0,
            ':truncatedStatus' => 0,
        );
        $criteria->with = array(
            'info' => array(
                'scopes' => 'deletedStatus', 'moderDeletedStatus', 'truncatedStatus',
                'params' => array(
                    ':deletedStatus' => 0,
                    ':moderDeletedStatus' => 0,
                    ':truncatedStatus' => 0
                )
            )
        );
        /** @var integer $comments */
        $comments = \CommentItem::model()->count($criteria);

        $criteria = new \CDbCriteria();
        $criteria->addCondition('`t`.user_id = :user_id');
        $criteria->scopes = array('activatedStatus', 'deletedStatus', 'moderDeletedStatus', 'truncatedStatus');
        $criteria->params = array(
            ':user_id' =>$userId,
            ':activatedStatus' => 1,
            ':deletedStatus' => 0,
            ':moderDeletedStatus' => 0,
            ':truncatedStatus' => 0,
        );

        /** @var integer $posts */
        $posts = \Post::model()->count($criteria);
        /** @var integer $images */
        $images = \GalleryImage::model()->count($criteria);
        /** @var integer $video */
        $video = \GalleryVideo::model()->count($criteria);
        /** @var integer $video */
        $audio = \GalleryAudio::model()->count($criteria);


        $criteria = new \CDbCriteria();
        $criteria->addCondition('`t`.user_id = :user_id');
        $criteria->scopes = array('deletedStatus');
        $criteria->params = array(':user_id' => $userId, ':deletedStatus' => 0);
        /** @var integer $ratings */
        $ratings = \RatingItem::model()->count($criteria);

        $criteria = new \CDbCriteria();
        $criteria->addCondition('`t`.user_id = :user_id');
        $criteria->params = array(':user_id' => $userId);
        $criteria->with = array('friend', 'user');
        $criteria->group = '`t`.user_id, `t`.friend_id';
        /** @var \UserFriend $friends */
        $friends = \UserFriend::model()->findAll($criteria);

        $criteria = new \CDbCriteria();
        $criteria->addCondition('`t`.subscribe_user_id = :user_id');
        $criteria->addCondition('`t`.post = 1 or `t`.image = 1 or `t`.audio = 1 or `t`.video = 1 or `t`.comment = 1');
        $criteria->with = array('ownerUser');
        $criteria->params = array(':user_id' => $userId);
        $readSubscribe = \SubscribeUser::model()->findAll($criteria);

        $criteria = new \CDbCriteria();
        $criteria->addCondition('`t`.item_id = :user_id');
        $criteria->addCondition('`t`.post = 1 or `t`.image = 1 or `t`.audio = 1 or `t`.video = 1 or `t`.comment = 1');
        $criteria->with = array('subscribeUser');
        $criteria->params = array(':user_id' => $userId);
        $readMeSubscribe = \SubscribeUser::model()->findAll($criteria);

        $criteria = new \CDbCriteria();
        $criteria->addCondition('`t`.user_id = :user_id');
        $criteria->scopes = array('deletedStatus');
        $criteria->params = array(':deletedStatus' => 0, ':user_id' => $userId);
        $criteria->with = array(
            'community' => array(
                'scopes' => array('deletedStatus', 'moderDeletedStatus', 'truncatedStatus'),
                'params' => array(':deletedStatus' => 0, ':moderDeletedStatus' => 0, ':truncatedStatus' => 0)
            )
        );
        $communities = \CommunityUser::model()->findAll($criteria);

        $view = 'show';
        $canFriend = false;
        $UserProfile = \UserProfile::model()->find('user_id = :user_id', array(
            ':user_id' => $userId
        ));
        if(\Yii::app()->user->id === \Yii::app()->userOwn->id) {
            $view = 'index';
            /** @var \UserProfile $UserProfile */
            if(null === $UserProfile)
                $UserProfile = new \UserProfile();

            if(!$UserProfile->is_croped)
                \Yii::app()->clientScript->registerPackage('jcrop');
        } elseif(!in_array(\Yii::app()->userOwn->id, \FriendRequest::getFriendsRequested(\Yii::app()->user->id, true)))
            $canFriend = true;

        $this->render($view, array(
            'readSubscribe' => $readSubscribe,
            'readMeSubscribe' => $readMeSubscribe,
            'canFriend' => $canFriend,
            'commentCount' => $comments,
            'ratingCount' => $ratings,
            'friends' => $friends,
            'postCount' => $posts,
            'imageCount' => $images,
            'videoCount' => $video,
            'audioCount' => $audio,
            'rating' => $UserProfile->rating,
            'model' => $UserProfile,
            'communities' => $communities
        ));
    }
}