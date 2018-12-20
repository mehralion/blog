<?php
namespace application\modules\event\controllers;
/**
 * Class NewsController
 * @package application.event.controllers
 */
class NewsController extends \FrontController
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
        );
    }

	public function actionPost()
	{
        $type = \Yii::app()->request->getParam('type');
        $criteria = new \CDbCriteria();
        if($type == 'friend'){
            $this->layout = 'event';
            $criteriaTemp = new \CDbCriteria();
            $frIds = array();
            /** @var \UserFriend $model */
            foreach(\UserFriend::getFriends(\Yii::app()->user->id) as $model)
                $frIds[] = $model->friend_id;
            $criteriaTemp->addInCondition('`t`.user_id', $frIds);

            $ids = array();
            /** @var \CommunityUser $community */
            foreach(\CommunityUser::getCommunities(\Yii::app()->user->id) as $community)
                $ids[] = $community->community_id;
            $criteriaTemp->addInCondition('`post`.community_id', $ids, 'OR');

            $criteria->mergeWith($criteriaTemp);
        } elseif(\Yii::app()->community->id) {
            $criteria->addCondition('`post`.community_id = :community_id');
            $criteria->params = array(':community_id' => \Yii::app()->community->id);
        } elseif(\Yii::app()->userOwn->id !== null) {
            $criteria->addCondition('`t`.user_id = :user_id');
            $criteria->params = \CMap::mergeArray($criteria->params, array(':user_id' => \Yii::app()->userOwn->id));
        }

        $criteria->order = '`t`.create_datetime desc';
        $criteria->with = array(
            'post' => array(
                'scopes' => array(
                    'activatedStatus',
                    'deletedStatus',
                    'moderDeletedStatus',
                    'truncatedStatus',
                ),
                'params' => array(
                    ':activatedStatus' => 1,
                    ':deletedStatus' => 0,
                    ':moderDeletedStatus' => 0,
                    ':truncatedStatus' => 0,
                ),
                'with' => array(
                    'info' => array(
                        'scopes' => array('deletedStatus', 'truncatedStatus', 'moderDeletedStatus'),
                        'params' => array(':deletedStatus' => 0, ':truncatedStatus' => 0, ':moderDeletedStatus' => 0),
                    )
                )
            )
        );
        $criteria->mergeWith(\Yii::app()->access->GetCriteriaAccess('post'));

        $dependency = new \CDbCacheDependency('SELECT MAX(update_datetime) FROM {{cache_event_item}} where item_type = :item_type');
        $dependency->params = array(':item_type' => \ItemTypes::ITEM_TYPE_POST);
        $dependency->reuseDependentData = true;

        $pages = new \CPagination(\EventItemPost::model()
            ->cache(\Yii::app()->paramsWrap->cache->eventNews, $dependency)
            ->count($criteria));
        $pages->pageSize = \Yii::app()->paramsWrap->pageSize->post;
        $pages->applyLimit($criteria);

        $models = \EventItemPost::model()->cache(\Yii::app()->paramsWrap->cache->eventNews, $dependency)->findAll($criteria);

		$this->render('post', array(
            'models' => $models,
            'pages' => $pages,
        ));
	}

    public function actionImageOld()
    {
        \Yii::app()->clientScript->registerPackage('images', \CClientScript::POS_READY);
        $type = \Yii::app()->request->getParam('type');
        $criteria = new \CDbCriteria();
        if($type == 'friend'){
            $this->layout = 'event';
            $criteriaTemp = new \CDbCriteria();
            $frIds = array();
            /** @var \UserFriend $model */
            foreach(\UserFriend::getFriends(\Yii::app()->user->id) as $model)
                $frIds[] = $model->friend_id;
            $criteriaTemp->addInCondition('`t`.user_id', $frIds);

            $ids = array();
            /** @var \CommunityUser $community */
            foreach(\CommunityUser::getCommunities(\Yii::app()->user->id) as $community)
                $ids[] = $community->community_id;
            $criteriaTemp->addInCondition('`albumInfo`.community_id', $ids, 'OR');

            $criteria->mergeWith($criteriaTemp);
        } elseif(\Yii::app()->community->id) {
            $criteria->addCondition('`albumInfo`.community_id = :community_id');
            $criteria->params = array(':community_id' => \Yii::app()->community->id);
        } elseif(\Yii::app()->userOwn->id !== null) {
            $criteria->addCondition('`t`.user_id = :user_id');
            $criteria->params = array(':user_id' => \Yii::app()->userOwn->id);
        }

        $access = \Yii::app()->access->GetStringAccess('imageAll');
        $criteria->with = array(
            'imageAll' => array(
                'condition' => 'DATE_FORMAT(imageAll.create_datetime, "%Y-%d-%m %H") = DATE_FORMAT(`t`.create_datetime, "%Y-%d-%m %H")',
                'on' => $access['condition'],
                'with' => array(
                    'userImage' => array('joinType' => 'inner join'),
                    'info' => array(
                        'scopes' => array('deletedStatus', 'truncatedStatus', 'moderDeletedStatus'),
                        'params' => array(':deletedStatus' => 0, ':truncatedStatus' => 0, ':moderDeletedStatus' => 0),
                    )
                ),
                'scopes' => array(
                    'activatedStatus',
                    'deletedStatus',
                    'moderDeletedStatus',
                    'truncatedStatus',
                ),
                'params' => \CMap::mergeArray($access['params'], array(
                    ':activatedStatus' => 1,
                    ':deletedStatus' => 0,
                    ':moderDeletedStatus' => 0,
                    ':truncatedStatus' => 0,
                ))
            ),
            'albumInfo',
            'user',
        );

        $criteria->order = '`t`.create_datetime desc';
        $criteria->group = '`t`.user_id, t.album_id, DATE_FORMAT(`t`.create_datetime, "%Y-%d-%m %H")';

        $dependency = new \CDbCacheDependency('SELECT MAX(user_update_datetime) FROM {{gallery_image}}');
        $dependency->reuseDependentData = true;

        $pages = new \CPagination(\EventItemImage::model()
            //->cache(\Yii::app()->paramsWrap->cache->eventNews, $dependency)
            ->count($criteria));
        $pages->pageSize = \Yii::app()->paramsWrap->pageSize->image;
        $pages->applyLimit($criteria);

        $models = \EventItemImage::model()
            //->cache(\Yii::app()->paramsWrap->cache->eventNews, $dependency, 2)
            ->findAll($criteria);

        $this->render('old_image', array(
            'models' => $models,
            'pages' => $pages,
        ));
    }

    public function actionImage()
    {
        \Yii::app()->clientScript->registerPackage('images', \CClientScript::POS_READY);
        $type = \Yii::app()->request->getParam('type');
        $criteria = new \CDbCriteria();
        if($type == 'friend'){
            $this->layout = 'event';
            $criteriaTemp = new \CDbCriteria();
            $frIds = array();
            /** @var \UserFriend $model */
            foreach(\UserFriend::getFriends(\Yii::app()->user->id) as $model)
                $frIds[] = $model->friend_id;
            $criteriaTemp->addInCondition('`t`.user_id', $frIds);

            $ids = array();
            /** @var \CommunityUser $community */
            foreach(\CommunityUser::getCommunities(\Yii::app()->user->id) as $community)
                $ids[] = $community->community_id;
            $criteriaTemp->addInCondition('`albumInfo`.community_id', $ids, 'OR');

            $criteria->mergeWith($criteriaTemp);
        } elseif(\Yii::app()->community->id) {
            $criteria->addCondition('`albumInfo`.community_id = :community_id');
            $criteria->params = array(':community_id' => \Yii::app()->community->id);
        } elseif(\Yii::app()->userOwn->id !== null) {
            $criteria->addCondition('`t`.user_id = :user_id');
            $criteria->params = array(':user_id' => \Yii::app()->userOwn->id);
        }

        $criteria->select = '`t`.create_datetime, `t`.item_type, `t`.user_id';
        $criteria->with = [
            'image' => [
                'select' => false,
                'condition' => 'DATE_FORMAT(image.create_datetime, "%Y-%d-%m %H") = DATE_FORMAT(`t`.create_datetime, "%Y-%d-%m %H")',
                'scopes' => [
                    'activatedStatus',
                    'deletedStatus',
                    'moderDeletedStatus',
                    'truncatedStatus',
                ],
                'params' => [
                        ':activatedStatus' => 1,
                        ':deletedStatus' => 0,
                        ':moderDeletedStatus' => 0,
                        ':truncatedStatus' => 0,
                    ]
            ],
            'albumInfo' => [
                'select' => 'albumInfo.title, albumInfo.is_community'
            ],
            'user' => [
                'select' => 'user.login, user.level, user.align, user.game_id, user.clan'
            ],
        ];
        $criteria->mergeWith(\Yii::app()->access->GetStringAccess('image'));
        $criteria->order = '`t`.create_datetime desc';
        $criteria->group = '`t`.user_id, t.album_id, DATE_FORMAT(`t`.create_datetime, "%Y-%d-%m %H")';

        $dependency = new \CDbCacheDependency('SELECT MAX(user_update_datetime) FROM {{gallery_image}}');
        $dependency->reuseDependentData = true;

        $pages = new \CPagination(\EventItemImage::model()
                ->cache(\Yii::app()->paramsWrap->cache->eventNews, $dependency)
                ->count($criteria));
        $pages->pageSize = \Yii::app()->paramsWrap->pageSize->image;
        $pages->applyLimit($criteria);

        $_arrayToView = [];
        $_userIds = [];
        $_dateTimeIds = [];
        $models = \EventItemImage::model()
            ->cache(\Yii::app()->paramsWrap->cache->eventNews, $dependency)
            ->findAll($criteria);
        /** @var \EventItemImage $model */
        foreach ($models as $model) {
            $_arrayToView[strtotime($model->create_datetime)] = [
                'album_id' => $model->album_id,
                'event' => $model,
                'items' => []
            ];

            if(!in_array($model->user_id, $_userIds))
                $_userIds[] = $model->user_id;
            $date = date('Y-d-m H', strtotime($model->create_datetime));
            if(!in_array($date, $_dateTimeIds))
                $_dateTimeIds[] = $date;
        }

        $criteria = new \CDbCriteria();
        $criteria->addInCondition('user_id', $_userIds);
        $criteria->addCondition('DATE_FORMAT(`t`.create_datetime, "%Y-%d-%m %H") IN ("'.implode('","', $_dateTimeIds).'")');
        $criteria->mergeWith(\Yii::app()->access->GetCriteriaAccess());
        $criteria->scopes = [
            'activatedStatus',
            'deletedStatus',
            'moderDeletedStatus',
            'truncatedStatus',
        ];
        $criteria->params = \CMap::mergeArray($criteria->params, [
                ':activatedStatus' => 1,
                ':deletedStatus' => 0,
                ':moderDeletedStatus' => 0,
                ':truncatedStatus' => 0,
            ]);
        $criteria->order = '`t`.create_datetime desc';
        $models = \GalleryImage::model()
            ->cache(\Yii::app()->paramsWrap->cache->eventNews, $dependency)
            ->findAll($criteria);

        /** @var \GalleryImage $model */
        foreach ($models as $model) {
            foreach ($_arrayToView as $key => $info) {
                if(date('Y-d-m H', $key) == date('Y-d-m H', strtotime($model->create_datetime)) && $info['event']->user_id == $model->user_id) {
                    $_arrayToView[$key]['items'][] = $model;
                    break;
                }
            }
        }

        $this->render('image', ['items' => $_arrayToView, 'pages' => $pages,]);
    }

    public function actionVideo()
    {
        \Yii::app()->clientScript->registerPackage('video', \CClientScript::POS_READY);
        $type = \Yii::app()->request->getParam('type');
        $criteria = new \CDbCriteria();
        if($type == 'friend'){
            $this->layout = 'event';
            $criteriaTemp = new \CDbCriteria();
            $frIds = array();
            /** @var \UserFriend $model */
            foreach(\UserFriend::getFriends(\Yii::app()->user->id) as $model)
                $frIds[] = $model->friend_id;
            $criteriaTemp->addInCondition('`t`.user_id', $frIds);

            $ids = array();
            /** @var \CommunityUser $community */
            foreach(\CommunityUser::getCommunities(\Yii::app()->user->id) as $community)
                $ids[] = $community->community_id;
            $criteriaTemp->addInCondition('`albumInfo`.community_id', $ids, 'OR');

            $criteria->mergeWith($criteriaTemp);
        } elseif(\Yii::app()->community->id) {
            $criteria->addCondition('`albumInfo`.community_id = :community_id');
            $criteria->params = array(':community_id' => \Yii::app()->community->id);
        } elseif(\Yii::app()->userOwn->id !== null) {
            $criteria->addCondition('`t`.user_id = :user_id');
            $criteria->params = array(':user_id' => \Yii::app()->userOwn->id);
        }

        $access = \Yii::app()->access->GetStringAccess('videoAll');
        $criteria->with = array(
            'videoAll' => array(
                'condition' => 'DATE_FORMAT(videoAll.create_datetime, "%Y-%d-%m %H") = DATE_FORMAT(`t`.create_datetime, "%Y-%d-%m %H")',
                'on' => $access['condition'],
                'with' => array(
                    'info' => array(
                        'scopes' => array('deletedStatus', 'truncatedStatus', 'moderDeletedStatus'),
                        'params' => array(':deletedStatus' => 0, ':truncatedStatus' => 0, ':moderDeletedStatus' => 0),
                    )
                ),
                'scopes' => array(
                    'activatedStatus',
                    'deletedStatus',
                    'moderDeletedStatus',
                    'truncatedStatus',
                ),
                'params' => \CMap::mergeArray($access['params'], array(
                    ':activatedStatus' => 1,
                    ':deletedStatus' => 0,
                    ':moderDeletedStatus' => 0,
                    ':truncatedStatus' => 0
                )),
            ),
            'user',
            'albumInfo',
        );
        $criteria->order = '`t`.create_datetime desc';
        $criteria->group = '`t`.user_id, t.album_id, DATE_FORMAT(`t`.create_datetime, "%Y-%d-%m %H")';

        $dependency = new \CDbCacheDependency('SELECT MAX(user_update_datetime) FROM {{gallery_video}}');
        $dependency->params = array(':item_type' => \ItemTypes::ITEM_TYPE_VIDEO);
        $dependency->reuseDependentData = true;

        $pages = new \CPagination(\EventItemVideo::model()
            ->cache(\Yii::app()->paramsWrap->cache->eventNews, $dependency)
            ->count($criteria));
        $pages->pageSize = \Yii::app()->params['page_size']['video'];
        $pages->applyLimit($criteria);

        $this->render('video', array(
            'models' => \EventItemVideo::model()->cache(\Yii::app()->paramsWrap->cache->eventNews, $dependency, 2)->findAll($criteria),
            'pages' => $pages,
        ));
    }

    public function actionAudio()
    {
        $type = \Yii::app()->request->getParam('type');
        $criteria = new \CDbCriteria();
        if($type == 'friend'){
            $this->layout = 'event';
            $criteriaTemp = new \CDbCriteria();
            $frIds = array();
            /** @var \UserFriend $model */
            foreach(\UserFriend::getFriends(\Yii::app()->user->id) as $model)
                $frIds[] = $model->friend_id;
            $criteriaTemp->addInCondition('`t`.user_id', $frIds);

            $ids = array();
            /** @var \CommunityUser $community */
            foreach(\CommunityUser::getCommunities(\Yii::app()->user->id) as $community)
                $ids[] = $community->community_id;
            $criteriaTemp->addInCondition('`albumInfo`.community_id', $ids, 'OR');

            $criteria->mergeWith($criteriaTemp);
        } elseif(\Yii::app()->community->id) {
            $criteria->addCondition('`albumInfo`.community_id = :community_id');
            $criteria->params = array(':community_id' => \Yii::app()->community->id);
        } elseif(\Yii::app()->userOwn->id !== null) {
            $criteria->addCondition('`t`.user_id = :user_id');
            $criteria->params = array(':user_id' => \Yii::app()->userOwn->id);
        }

        $access = \Yii::app()->access->GetStringAccess('audioAll');
        $criteria->with = array(
            'audioAll' => array(
                'condition' => 'DATE_FORMAT(audioAll.create_datetime, "%Y-%d-%m %H") = DATE_FORMAT(`t`.create_datetime, "%Y-%d-%m %H")',
                'on' => $access['condition'],
                'with' => array(
                    'album' => array(
                        'with' => array(
                            'info' => array(
                                'scopes' => array('deletedStatus', 'truncatedStatus', 'moderDeletedStatus'),
                                'params' => array(':deletedStatus' => 0, ':truncatedStatus' => 0, ':moderDeletedStatus' => 0),
                            )
                        )
                    )
                ),
                'scopes' => array(
                    'activatedStatus',
                    'deletedStatus',
                    'moderDeletedStatus',
                    'truncatedStatus',
                ),
                'params' => \CMap::mergeArray($access['params'], array(
                    ':activatedStatus' => 1,
                    ':deletedStatus' => 0,
                    ':moderDeletedStatus' => 0,
                    ':truncatedStatus' => 0,
                ))
            ),
            'albumInfo',
            'user',
        );
        $criteria->order = '`t`.create_datetime desc';
        $criteria->group = '`t`.user_id, t.album_id, DATE_FORMAT(`t`.create_datetime, "%Y-%d-%m %H")';

        $dependency = new \CDbCacheDependency('SELECT MAX(user_update_datetime) FROM {{gallery_audio}}');
        $dependency->params = array(':item_type' => \ItemTypes::ITEM_TYPE_AUDIO_ALBUM);
        $dependency->reuseDependentData = true;

        $pages = new \CPagination(\EventItemAudio::model()
            ->cache(\Yii::app()->paramsWrap->cache->eventNews, $dependency)
            ->count($criteria));
        $pages->pageSize = \Yii::app()->paramsWrap->pageSize->image;
        $pages->applyLimit($criteria);

        $models = \EventItemAudio::model()->cache(\Yii::app()->paramsWrap->cache->eventNews, $dependency, 2)->findAll($criteria);

        $this->render('audio', array(
            'models' => $models,
            'pages' => $pages,
        ));
    }
}