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
class Debate extends \CAction
{
    public function run()
    {
        $criteria = new \CDbCriteria();
        $criteria->scopes = array('activatedStatus', 'deletedStatus', 'moderDeletedStatus', 'truncatedStatus');
        $criteria->params = array(
            ':activatedStatus' => 1,
            ':deletedStatus' => 0,
            ':moderDeletedStatus' => 0,
            ':truncatedStatus' => 0
        );
        $criteria->select .= ', MAX(create_datetime) as DATEORDER';
        $criteria->group = 'item_id, item_type';
        $subQuery = \CommentItem::model()->getCommandBuilder()->createFindCommand(\CommentItem::model()->getTableSchema(),$criteria)->getText();

        $criteria = new \CDbCriteria();
        $criteria->scopes = array('own', 'deletedStatus');
        $criteria->params = array(':deletedStatus' => 0);
        $criteria->with = array(
            'owner',
            'info' => array(
                'scopes' => array('deletedStatus', 'truncatedStatus', 'moderDeletedStatus'),
                'params' => array(':deletedStatus' => 0, ':truncatedStatus' => 0, ':moderDeletedStatus' => 0),
            )
        );
        $criteria->join = 'INNER JOIN ('.$subQuery.') `commentOrder`  ON (`commentOrder`.`item_id`= `t`.item_id) AND (`commentOrder`.`item_type`=`t`.`item_type`)';
        $criteria->order = 'commentOrder.DATEORDER desc';
        $criteria->mergeWith(\Yii::app()->access->GetCriteriaAccess('info', true));

        $dependency = new \CDbCacheDependency('select max(update_datetime) from {{subscribe_debate}}');
        $dependency->reuseDependentData = true;

        $pages = new \CPagination(\SubscribeDebate::model()
            ->cache(\Yii::app()->paramsWrap->cache->subscribe, $dependency)
            ->count($criteria));
        $pages->pageSize = \Yii::app()->paramsWrap->pageSize->comment;
        $pages->applyLimit($criteria);

        /** @var \SubscribeDebate[] $models */
        $models = \SubscribeDebate::model()->cache(\Yii::app()->paramsWrap->cache->subscribe, $dependency)->findAll($criteria);

        $this->controller->render('debate', array(
            'models' => $models,
            'pages' => $pages
        ));
    }
}