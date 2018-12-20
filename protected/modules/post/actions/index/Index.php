<?php
namespace application\modules\post\actions\index;
use application\modules\post\components\PostAction;

/**
 * Created by JetBrains PhpStorm.
 * User: Николай
 * Date: 13.06.13
 * Time: 13:13
 * To change this template use File | Settings | File Templates.
 *
 * @package application.post.actions.index
 */
class Index extends PostAction
{
    public $viewName = 'index';

    public function run()
    {
        $criteriaType = new \CDbCriteria();
        if($this->isCommunity) {
            $criteriaType->addCondition('`t`.community_id = :community_id');
            $criteriaType->params = array(':community_id' => \Yii::app()->community->id);
        } else {
            if(null !== $this->userId) {
                $criteriaType->addCondition('`t`.user_id = :user_id');
                $criteriaType->params = array(':user_id' => $this->userId);
                $criteriaType->scopes = array('notCommunity');
            }
        }

        $criteria = new \CDbCriteria();
        $criteria->with = array(
            'user' => array('with' => array('userProfile')),
            'canRate',
            'info' => array(
                'scopes' => array('deletedStatus', 'truncatedStatus', 'moderDeletedStatus'),
                'params' => array(':deletedStatus' => 0, ':truncatedStatus' => 0, ':moderDeletedStatus' => 0),
            )
        );
        $criteria->order = '`t`.on_top desc, `t`.create_datetime desc';
        $criteria->scopes = array('activatedStatus', 'deletedStatus', 'moderDeletedStatus', 'truncatedStatus');
        $criteria->params = array(
            ':activatedStatus' => 1,
            ':deletedStatus' => 0,
            ':moderDeletedStatus' => 0,
            ':truncatedStatus' => 0
        );
        $criteria->mergeWith($criteriaType);
        $criteria->mergeWith(\Yii::app()->access->GetCriteriaAccess());
        $tags = \Yii::app()->request->getParam('tag', array());
        if($tags) {
            $criteriaTags = new \CDbCriteria();
            $criteriaTags->with = array('sTag');
            $criteriaTags->params = array(':tagname' => $tags);
            $criteria->mergeWith($criteriaTags);
        }

        $dependency = new \CDbCacheDependency('select max(update_datetime) from {{cache_event_item}} where item_type = :item_type');
        $dependency->params = array(':item_type' => \ItemTypes::ITEM_TYPE_POST);
        $dependency->reuseDependentData = true;

        $pages = new \CPagination(\Post::model()->cache(\Yii::app()->paramsWrap->cache->post, $dependency)->count($criteria));
        $pages->pageSize = \Yii::app()->paramsWrap->pageSize->post;
        $pages->applyLimit($criteria);
        /** @var \Post $Posts */
        $Posts = \Post::model()->cache(\Yii::app()->paramsWrap->cache->post, $dependency)->findAll($criteria);

        $this->controller->render($this->viewName, array(
            'models' => $Posts,
            'pages' => $pages
        ));
    }
}