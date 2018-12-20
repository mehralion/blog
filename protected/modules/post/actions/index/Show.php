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
class Show extends PostAction
{
    public $viewName = 'show';

    public function run()
    {
        $criteriaType = new \CDbCriteria();
        if($this->isCommunity) {
            $criteriaType->addCondition('`t`.community_id = :community_id');
            $criteriaType->params = array(':community_id' => \Yii::app()->community->id);
        }


        $id = \Yii::app()->request->getParam('id');
        $criteria = new \CDbCriteria();
        $criteria->addCondition('`t`.id = :id');
        $criteria->addCondition('`t`.is_deleted = 0 or (`t`.is_deleted = 1 and `t`.user_id = :user_id)');
        $criteria->addCondition('`t`.is_moder_deleted = 0 or (`t`.is_moder_deleted = 1 and `t`.user_id = :user_id)');
        $criteria->scopes = array('truncatedStatus');

        $criteria->params = \CMap::mergeArray($criteria->params, array(
            ':id' => $id,
            ':user_id' => \Yii::app()->user->id,
            ':truncatedStatus' => 0
        ));
        $criteria->with =  array(
            'user' => array('with' => array('userProfile')),
            'canRate',
            'canSubscribe',
            'poll',
            'info' => array(
                'scopes' => array('deletedStatus', 'truncatedStatus', 'moderDeletedStatus'),
                'params' => array(':deletedStatus' => 0, ':truncatedStatus' => 0, ':moderDeletedStatus' => 0),
            )
        );
        $criteria->mergeWith($criteriaType);
        $criteria->mergeWith(\Yii::app()->access->GetCriteriaAccess());

        $dependency = new \CDbCacheDependency('SELECT MAX(update_datetime) FROM {{cache_event_item}} where item_id = :item_id and item_type = :item_type');
        $dependency->params = array(':item_id' => $id, ':item_type' => \ItemTypes::ITEM_TYPE_POST);
        $dependency->reuseDependentData = true;

        /** @var \Post $Post */
        $Post = \Post::model()->cache(\Yii::app()->paramsWrap->cache->post, $dependency)->find($criteria);
        if(!isset($Post))
            \MyException::ShowError(403, 'Заметка не найдена');

        $pollId = null;
        if($Post->poll !== null) {
            $pollId = $Post->poll->id;
        }

        $this->controller->render($this->viewName, array(
            'model' => $Post,
            'pollId' => $pollId
        ));
    }
}