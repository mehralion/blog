<?php
namespace application\modules\post\actions\profile;
/**
 * Created by JetBrains PhpStorm.
 * User: Николай
 * Date: 13.06.13
 * Time: 13:13
 * To change this template use File | Settings | File Templates.
 *
 * @package application.post.actions.profile
 */
class Index extends \CAction
{
    public function run()
    {
        $criteria = new \CDbCriteria();
        $criteria->scopes = array('deletedStatus', 'moderDeletedStatus', 'truncatedStatus', 'own', 'notCommunity');
        $criteria->order = '`t`.create_datetime desc';
        $criteria->with = array('user' => array('with' => 'userProfile'));
        $criteria->params = array(
            ':deletedStatus' => 0,
            ':moderDeletedStatus' => 0,
            ':truncatedStatus' => 0
        );

        $dependency = new \CDbCacheDependency('select max(update_datetime) from {{cache_event_item}} where user_id = :user_id and item_type = :item_type');
        $dependency->params = array(':user_id' => \Yii::app()->user->id, ':item_type' => \ItemTypes::ITEM_TYPE_POST);
        $dependency->reuseDependentData = true;

        $pages = new \CPagination(\Post::model()->cache(\Yii::app()->paramsWrap->cache->post, $dependency)->count($criteria));
        $pages->pageSize = \Yii::app()->paramsWrap->pageSize->post;
        $pages->applyLimit($criteria);

        $Posts = \Post::model()->cache(\Yii::app()->paramsWrap->cache->post, $dependency)->findAll($criteria);

        $this->controller->render('index', array(
            'models' => $Posts,
            'pages' => $pages
        ));
    }
}