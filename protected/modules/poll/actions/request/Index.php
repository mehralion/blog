<?php
namespace application\modules\poll\actions\request;
/**
 * Created by JetBrains PhpStorm.
 * User: Николай
 * Date: 13.06.13
 * Time: 13:13
 * To change this template use File | Settings | File Templates.
 *
 * @package application.post.actions.index
 */
class Index extends \CAction
{
    public function run()
    {
        $criteria = new \CDbCriteria();
        $criteria->with = array(
            'post' => array(
                'scopes' => array(
                    'deletedStatus',
                    'activatedStatus',
                    'moderDeletedStatus',
                    'truncatedStatus'
                ),
                'params' => array(
                    ':deletedStatus' => 0,
                    ':activatedStatus' => 1,
                    ':moderDeletedStatus' => 0,
                    ':truncatedStatus' => 0
                ),
                'with' => array(
                    'user'
                )
            ),
            'pollAnswers' => array(
                'order' => 'pollAnswers.id desc'
            ),
            'pollUserAnswer'
        );
        $criteria->order = '`t`.create_datetime desc';
        $criteria->mergeWith(\Yii::app()->access->GetCriteriaAccess('post'));

        $dependency = new \CDbCacheDependency(
            'select CONCAT(max(p.update_datetime), "_", count(pa.id)) from {{cache_event_item}} p, {{poll_answer}} pa where p.item_type = :item_type'
        );
        $dependency->params = array(':item_type' => \ItemTypes::ITEM_TYPE_POST);
        $dependency->reuseDependentData = true;

        $pages = new \CPagination(\Poll::model()->cache(\Yii::app()->paramsWrap->cache->post, $dependency)->count($criteria));
        $pages->pageSize = \Yii::app()->paramsWrap->pageSize->top_user;
        $pages->applyLimit($criteria);

        /** @var \Poll $Poll */
        $Poll = \Poll::model()->findAll($criteria);

        $this->controller->render('index', array(
            'models' => $Poll,
            'pages' => $pages
        ));
    }
}