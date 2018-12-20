<?php
namespace application\modules\friend\controllers;
/**
 * Class ListController
 * @package application.friend.controllers
 */
class ListController extends \FrontController
{
    public function filters()
    {
        return array(
            'accessControl',
        );
    }

    public function accessRules()
    {
        return array(
            array('deny',
                'users' => array('?'),
            )
        );
    }

    public function actionFriend()
    {
        $gameId =  \Yii::app()->request->getParam('gameId', \Yii::app()->user->getGameId());
        $criteria = new \CDbCriteria();
        $criteria->addCondition('game_id = :game_id');
        $criteria->params = array(':game_id' => $gameId);

        if($gameId != \Yii::app()->user->getGameId()) {
            /** @var \User $User */
            $User = \User::model()->find($criteria);
            if(!isset($User)) {
                \Yii::app()->message->setErrors('danger', 'Персонаж не найден');
                \Yii::app()->message->showMessage();
            }
        } else
            $User = \Yii::app()->user;

        $criteria = new \CDbCriteria();
        $criteria->addCondition('`t`.user_id = :u_id');
        $criteria->with = array('friend' => array('with' => array('userProfile')), 'user'); //user - владелец, friend - друзья
        $criteria->params = array(':u_id' => $User->id);
        $criteria->group = '`t`.user_id, `t`.friend_id';

        $dependency = new \CDbCacheDependency('select AVG(create_datetime) from {{user_friend}} where user_id = :user_id or friend_id = :user_id');
        $dependency->params = array(':user_id' => \Yii::app()->user->id);
        $dependency->reuseDependentData = true;

        $pages = new \CPagination(\UserFriend::model()
            ->cache(\Yii::app()->paramsWrap->cache->friends, $dependency)
            ->count($criteria));
        $pages->pageSize = \Yii::app()->paramsWrap->pageSize->friend;
        $pages->applyLimit($criteria);

        $models =  \UserFriend::model()->cache(\Yii::app()->paramsWrap->cache->friends, $dependency)->findAll($criteria);

        $dependency = new \CDbCacheDependency('select max(update_datetime) from {{friend_request}} where user_id = :user_id or friend_id = :user_id');
        $dependency->params = array(':user_id' => \Yii::app()->user->id);
        $dependency->reuseDependentData = true;

        $criteria = new \CDbCriteria();
        $criteria->addCondition('`t`.friend_id = :f_id');
        $criteria->with = array('user'); //user = кто кинул мне запрос, я friend
        $criteria->params = array(':f_id' => \Yii::app()->user->id);
        $criteria->scopes = array('pending');
        $count = \FriendRequest::model()
            ->cache(\Yii::app()->paramsWrap->cache->friends, $dependency)
            ->count($criteria);
        $params = array('warning' => false);
        if($count)
            $params = array('warning' => true);

        $menu = '';
        if($User->id == \Yii::app()->user->id)
            $menu = $this->renderPartial('menu', $params, true);

        $this->render('friend_list', array(
            'models' =>$models,
            'pages' => $pages,
            'type' => 'friend',
            'menu' => $menu
        ));
    }

    public function actionPending()
    {
        $criteria = new \CDbCriteria();
        $criteria->addCondition('`t`.friend_id = :f_id');
        $criteria->with = array('user'); //user = кто кинул мне запрос, я friend
        $criteria->params = array(':f_id' => \Yii::app()->user->id);
        $criteria->scopes = array('pending');
        $criteria->group = '`t`.user_id';

        $dependency = new \CDbCacheDependency('select max(update_datetime) from {{friend_request}} where user_id = :user_id or friend_id = :user_id');
        $dependency->params = array(':user_id' => \Yii::app()->user->id);
        $dependency->reuseDependentData = true;

        $pages = new \CPagination(\FriendRequest::model()
            ->cache(\Yii::app()->paramsWrap->cache->friends, $dependency)
            ->count($criteria));
        $pages->pageSize = \Yii::app()->paramsWrap->pageSize->friend;
        $pages->applyLimit($criteria);

        $models = \FriendRequest::model()->cache(\Yii::app()->paramsWrap->cache->friends, $dependency)->findAll($criteria);

        $criteria = new \CDbCriteria();
        $criteria->addCondition('`t`.friend_id = :f_id');
        $criteria->with = array('user'); //user = кто кинул мне запрос, я friend
        $criteria->params = array(':f_id' => \Yii::app()->user->id);
        $criteria->scopes = array('pending');
        $count = \FriendRequest::model()
            ->cache(\Yii::app()->paramsWrap->cache->friends, $dependency)
            ->count($criteria);
        $params = array('warning' => false);
        if($count)
            $params = array('warning' => true);

        $this->render('friend_list', array(
            'models' => $models,
            'pages' => $pages,
            'type' => 'pending',
            'menu' => $this->renderPartial('menu', $params, true),
        ));
    }

    public function actionOwn()
    {
        $criteria = new \CDbCriteria();
        $criteria->addCondition('`t`.user_id = :u_id');
        $criteria->with = array('friend'); //user = кто кинул мне запрос, я friend
        $criteria->params = array(':u_id' => \Yii::app()->user->id);
        $criteria->scopes = array('pending');
        $criteria->group = '`t`.friend_id';

        $dependency = new \CDbCacheDependency('select max(update_datetime) from {{friend_request}} where user_id = :user_id or friend_id = :user_id');
        $dependency->params = array(':user_id' => \Yii::app()->user->id);
        $dependency->reuseDependentData = true;

        $pages = new \CPagination(\FriendRequest::model()
            ->cache(\Yii::app()->paramsWrap->cache->friends, $dependency)
            ->count($criteria));
        $pages->pageSize = \Yii::app()->paramsWrap->pageSize->friend;
        $pages->applyLimit($criteria);

        $models = \FriendRequest::model()->cache(\Yii::app()->paramsWrap->cache->friends, $dependency)->findAll($criteria);

        $criteria = new \CDbCriteria();
        $criteria->addCondition('`t`.friend_id = :f_id');
        $criteria->with = array('user'); //user = кто кинул мне запрос, я friend
        $criteria->params = array(':f_id' => \Yii::app()->user->id);
        $criteria->scopes = array('pending');
        $count = \FriendRequest::model()
            ->cache(\Yii::app()->paramsWrap->cache->friends, $dependency)
            ->count($criteria);
        $params = array('warning' => false);
        if($count)
            $params = array('warning' => true);

        $this->render('friend_list', array(
            'models' => $models,
            'pages' => $pages,
            'type' => 'own',
            'menu' => $this->renderPartial('menu', $params, true),
        ));
    }
}