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
class Comment extends \CAction
{
    public function run()
    {
        $criteria = new \CDbCriteria();
        $criteria->addCondition('`t`.comment = 1');
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
            'userOwner',
            'user',
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
                )
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
                'with' => array('canRate')
            )
        );
        $criteria->addInCondition('`t`.user_id', $idsUser);
        $criteria->mergeWith(\Yii::app()->access->GetCriteriaAccess('info', true));
        $criteria->order = 't.create_datetime desc';

        $dependency = new \CDbCacheDependency('select max(update_datetime) from {{cache_event_item}}');
        $dependency->reuseDependentData = true;

        $pages = new \CPagination(\EventComment::model()
            ->cache(\Yii::app()->paramsWrap->cache->subscribe, $dependency)
            ->count($criteria));
        $pages->pageSize = \Yii::app()->paramsWrap->pageSize->comment;
        $pages->applyLimit($criteria);

        $models = \EventComment::model()->cache(\Yii::app()->paramsWrap->cache->subscribe, $dependency)->findAll($criteria);

        $this->controller->render('comment', array(
            'models' => $models,
            'pages' => $pages
        ));
    }
}