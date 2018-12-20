<?php
/**
 * Created by PhpStorm.
 * User: Николай
 * Date: 28.07.13
 * Time: 15:09
 */

namespace application\modules\post\actions\index;


class Most extends \CAction
{
    public function run()
    {
        $criteria = new \CDbCriteria();
        $criteria->with = array(
            'user' => array(
                'with' => array(
                    'userProfile'
                ),
                'joinType' => 'inner join',
            ),
            'canRate',
            'info' => array(
                'scopes' => array('deletedStatus', 'truncatedStatus', 'moderDeletedStatus'),
                'params' => array(':deletedStatus' => 0, ':truncatedStatus' => 0, ':moderDeletedStatus' => 0),
            )
        );
        $criteria->scopes = array(
            'public',
            'activatedStatus',
            'deletedStatus',
            'moderDeletedStatus',
            'truncatedStatus'
        );
        $criteria->params = array(
            ':activatedStatus' => 1,
            ':deletedStatus' => 0,
            ':moderDeletedStatus' => 0,
            ':truncatedStatus' => 0
        );
        $criteria->order = '`t`.comment_count desc';

        $dependency = new \CDbCacheDependency('select MAX(update_datetime) from {{cache_event_item}} where item_type = :item_type');
        $dependency->params = array(':item_type' => \ItemTypes::ITEM_TYPE_POST);
        $dependency->reuseDependentData = true;

        $pages = new \CPagination(\Post::model()->cache(\Yii::app()->paramsWrap->cache->post, $dependency)->count($criteria));
        $pages->pageSize = \Yii::app()->paramsWrap->pageSize->post;
        $pages->applyLimit($criteria);

        $Posts = \Post::model()->cache(\Yii::app()->paramsWrap->cache->post, $dependency)->findAll($criteria);
        $this->controller->render('most', array(
            'models' => $Posts,
            'pages' => $pages
        ));
    }
} 