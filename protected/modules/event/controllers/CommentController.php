<?php
namespace application\modules\event\controllers;
/**
 * Class CommentController
 * @package application.event.controllers
 */
class CommentController extends \FrontController
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
                'actions'=>array('own', 'friend'),
                'users' => array('?'),
            )
        );
    }

    public $menuFilter = array();
    public function actionIndex()
    {
        $criteria = new \CDbCriteria();
        $criteria->with = array(
            'userOwner' => ['joinType' => 'inner join'],
            'user' => ['joinType' => 'inner join'],
            'info' => array(
                'scopes' => array(
                    'activatedStatus',
                    'truncatedStatus',
                    'deletedStatus',
                    'moderDeletedStatus',
                ),
                'params' => array(
                    ':activatedStatus' => 1,
                    ':truncatedStatus' => 0,
                    ':deletedStatus' => 0,
                    ':moderDeletedStatus' => 0,
                ),
                'joinType' => 'inner join',
                'alias' => 'item_info'
            ),
            'comment' => array(
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
                'joinType' => 'inner join',
                'with' => [
                    'canRate' => ['joinType' => 'left join'],
                    'info' => ['joinType' => 'inner join'],
                ]
            )
        );

        if(\Yii::app()->userOwn->id !== null) {
            $criteria->addCondition('`t`.user_id = :user_id');
            $criteria->params = array(':user_id' => \Yii::app()->userOwn->id);
        } elseif(\Yii::app()->community->id) {
            $criteria->addCondition('`info`.community_id = :community_id');
            $criteria->params = array(':community_id' => \Yii::app()->community->id);
        }
        $criteria->mergeWith(\Yii::app()->access->GetCriteriaAccess('info', true));
        $criteria->order = 't.create_datetime desc';

        $dependency = new \CDbCacheDependency('select max(update_datetime) from {{cache_event_item}}');
        $dependency->reuseDependentData = true;

        //\VarDumper::dump($criteria);die;

        $pages = new \CPagination(\EventComment::model()
            ->cache(\Yii::app()->paramsWrap->cache->eventComment, $dependency)
            ->count($criteria));
        $pages->pageSize = \Yii::app()->params['page_size']['post'];
        $pages->applyLimit($criteria);

        $model = \EventComment::model()
            ->cache(\Yii::app()->paramsWrap->cache->eventComment, $dependency)
            ->findAll($criteria);

        $this->render('index', array(
            'models' => $model,
            'pages' => $pages
        ));
    }

    public function actionOwn()
    {
        $criteria = new \CDbCriteria();
        $criteria->addCondition('`t`.user_owner_id = :user_id');
        $criteria->addCondition('`info`.is_community = 0');
        $criteria->params = array(
            ':user_id' => \Yii::app()->user->id,
        );
        $criteria->with = array(
            'info' => array(
                'scopes' => array(
                    'truncatedStatus',
                    'deletedStatus',
                    'moderDeletedStatus',
                    'activatedStatus'
                ),
                'params' => array(
                    ':truncatedStatus' => 0,
                    ':deletedStatus' => 0,
                    ':moderDeletedStatus' => 0,
                    ':activatedStatus' => 1
                ),
            ),
            'comment' => array(
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
                )
            ));
        $criteria->order = 't.create_datetime desc';
        $criteria->scopes = array(
            'notMe',
        );
        $criteria->mergeWith(\Yii::app()->access->GetCriteriaAccess('info', true));

        $dependency = new \CDbCacheDependency('select max(update_datetime) from {{cache_event_item}} where user_id = :user_id');
        $dependency->params = array(':user_id' => \Yii::app()->user->id);
        $dependency->reuseDependentData = true;

        $pages = new \CPagination(\EventComment::model()
            ->cache(\Yii::app()->paramsWrap->cache->eventComment, $dependency)
            ->count($criteria));
        $pages->pageSize = \Yii::app()->paramsWrap->pageSize->comment;
        $pages->applyLimit($criteria);

        $EventView = \EventViewDatetimeComment::model()->find('user_id = :user_id', array(':user_id' => \Yii::app()->user->id));
        if(null === $EventView)
            $EventView = new \EventViewDatetimeComment();

        $EventView->update_datetime = \DateTimeFormat::format();
        $EventView->user_id = \Yii::app()->user->id;
        $EventView->save();

        $models = \EventComment::model()->cache(\Yii::app()->paramsWrap->cache->eventComment, $dependency)->findAll($criteria);

        $this->render('index', array(
            'models' => $models,
            'pages' => $pages
        ));
    }

    public function actionFriend()
    {
        $this->layout = 'event';
        $criteria = new \CDbCriteria();

        $criteriaTemp = new \CDbCriteria();
        $ids = array();
        /** @var \UserFriend $friend */
        foreach(\UserFriend::getFriends(\Yii::app()->user->id) as $friend)
            $ids[] = $friend->friend_id;
        $criteriaTemp->addInCondition('`t`.user_id', $ids);

        $ids = array();
        /** @var \CommunityUser $community */
        foreach(\CommunityUser::getCommunities(\Yii::app()->user->id) as $community)
            $ids[] = $community->community_id;
        $criteriaTemp->addInCondition('`info`.community_id', $ids, 'OR');

        $criteria->mergeWith($criteriaTemp);

        $criteria->with = array(
            'info' => array(
                'scopes' => array(
                    'truncatedStatus',
                    'deletedStatus',
                    'moderDeletedStatus',
                    'activatedStatus'
                ),
                'params' => array(
                    ':truncatedStatus' => 0,
                    ':deletedStatus' => 0,
                    ':moderDeletedStatus' => 0,
                    ':activatedStatus' => 1
                ),
            ),
            'comment' => array(
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
                    ':truncatedStatus' => 0
                )
            ));
        $criteria->order = '`t`.create_datetime desc';
        $criteria->mergeWith(\Yii::app()->access->GetCriteriaAccess('info', true));

        $dependency = new \CDbCacheDependency("select max(update_datetime) from {{cache_event_item}} where user_id IN (':user_id')");
        $dependency->params = array(':user_id' => implode("'",$ids));
        $dependency->reuseDependentData = true;

        $pages = new \CPagination(\EventComment::model()
            ->cache(\Yii::app()->paramsWrap->cache->eventComment, $dependency)
            ->count($criteria));
        $pages->pageSize = \Yii::app()->paramsWrap->pageSize->post;
        $pages->applyLimit($criteria);

        $models = \EventComment::model()->cache(\Yii::app()->paramsWrap->cache->eventComment, $dependency)->findAll($criteria);

        $this->render('index', array(
            'models' => $models,
            'pages' => $pages
        ));
    }
}