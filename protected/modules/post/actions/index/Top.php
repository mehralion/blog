<?php
namespace application\modules\post\actions\index;
/**
 * Created by JetBrains PhpStorm.
 * User: Николай
 * Date: 13.06.13
 * Time: 13:13
 * To change this template use File | Settings | File Templates.
 *
 * @package application.post.actions.index
 */
class Top extends \CAction
{
    public function run()
    {
        $criteria = new \CDbCriteria();
        $criteria->with = array(
            'user' => array(
                'with' => array('userProfile'),
                'joinType' => 'inner join'
            ),
            'canRate',
            'info' => array(
                'scopes' => array('deletedStatus', 'truncatedStatus', 'moderDeletedStatus'),
                'params' => array(':deletedStatus' => 0, ':truncatedStatus' => 0, ':moderDeletedStatus' => 0),
            )
        );
        $criteria->order = '`t`.rating desc, `t`.create_datetime';
        $criteria->scopes = array(
            'activatedStatus',
            'deletedStatus',
            'moderDeletedStatus',
            'public',
            'truncatedStatus',
        );
        $criteria->params = array(
            ':activatedStatus' => 1,
            ':deletedStatus' => 0,
            ':moderDeletedStatus' => 0,
            ':truncatedStatus' => 0,
        );

        $dependency = new \CDbCacheDependency('SELECT MAX(update_datetime) FROM {{cache_event_item}} where item_type = :item_type');
        $dependency->params = array(':item_type' => \ItemTypes::ITEM_TYPE_POST);
        $dependency->reuseDependentData = true;

        $pages = new \CPagination(\Post::model()
                ->cache(\Yii::app()->paramsWrap->cache->post, $dependency)
                ->count($criteria));
        $pages->pageSize = \Yii::app()->paramsWrap->pageSize->post;
        $pages->applyLimit($criteria);

        $Posts = \Post::model()
            ->cache(\Yii::app()->paramsWrap->cache->post, $dependency)
            ->findAll($criteria);
        $this->controller->render('index', array(
            'models' => $Posts,
            'pages' => $pages
        ));
    }
}