<?php
namespace application\modules\subscribe\actions\show;
/**
 * Created by JetBrains PhpStorm.
 * User: Николай
 * Date: 13.06.13
 * Time: 13:13
 * To change this template use File | Settings | File Templates.
 *
 * @package application.gallery.actions.image
 */
class Post extends \CAction
{
    public function run()
    {
        $criteria = new \CDbCriteria();
        $criteria->addCondition('`t`.post = 1');
        $criteria->scopes = array('own');

        $dependency = new \CDbCacheDependency('SELECT MAX(update_datetime) FROM {{subscribe}} where subscribe_user_id = :subscribe_user_id');
        $dependency->params = array(':subscribe_user_id' => \Yii::app()->user->id);
        $dependency->reuseDependentData = true;

        $idsUser = array();
        /** @var \Subscribe[] $models */
        $models = \Subscribe::model()->cache(\Yii::app()->paramsWrap->cache->subscribe, $dependency)->findAll($criteria);
        foreach($models as $model)
            $idsUser[] = $model->item_id;


        $criteria = new \CDbCriteria();
        $criteria->with = array(
            'post' => array(
                'scopes' => array('activatedStatus', 'deletedStatus', 'moderDeletedStatus', 'truncatedStatus'),
                'params' => array(
                    ':activatedStatus' => 1,
                    ':deletedStatus' => 0,
                    ':moderDeletedStatus' => 0,
                    ':truncatedStatus' => 0
                ),
                'with' => array(
                    'user' => array(
                        'with' => array('userProfile'),
                        'joinType' => 'inner join'
                    ),
                    'canRate',
                    'info' => array(
                        'scopes' => array('deletedStatus', 'truncatedStatus', 'moderDeletedStatus'),
                        'params' => array(':deletedStatus' => 0, ':truncatedStatus' => 0, ':moderDeletedStatus' => 0),
                    )
                ),
                'order' => '`post`.on_top desc, `post`.create_datetime desc'
            )
        );
        $criteria->addInCondition('`t`.user_id', $idsUser);
        $criteria->mergeWith(\Yii::app()->access->GetCriteriaAccess('post'));

        $dependency = new \CDbCacheDependency('select max(update_datetime) from {{cache_event_item}} where item_type = :item_type');
        $dependency->params = array(':item_type' => \ItemTypes::ITEM_TYPE_POST);
        $dependency->reuseDependentData = true;

        $pages = new \CPagination(\EventItemPost::model()
            ->cache(\Yii::app()->paramsWrap->cache->subscribe, $dependency)
            ->count($criteria));
        $pages->pageSize = \Yii::app()->paramsWrap->pageSize->post;
        $pages->applyLimit($criteria);

        /** @var \EventItemPost $Posts */
        $Posts = \EventItemPost::model()
            ->cache(\Yii::app()->paramsWrap->cache->subscribe, $dependency)
            ->findAll($criteria);

        $this->controller->render('post', array(
            'models' => $Posts,
            'pages' => $pages
        ));
    }
}