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
class ListRate extends \CAction
{
    private $_page = 10;
    public function run()
    {
        $id = \Yii::app()->request->getParam('id');
        $page = \Yii::app()->request->getParam('page', 1);
        $criteria = new \CDbCriteria();
        $criteria->with = array('user');
        $criteria->addCondition('`t`.item_id = :item_id');
        $criteria->params = array(
            ':item_id' => $id
        );
        $criteria->offset = ($page - 1) * $this->_page;
        $criteria->limit = $this->_page;

        $dependency = new \CDbCacheDependency('select max(update_datetime) from {{cache_event_item}} where item_type = :item_type and item_id = :item_id');
        $dependency->params = array(':item_type' => \ItemTypes::ITEM_TYPE_POST, ':item_id' => $id);
        $dependency->reuseDependentData = true;

        $Rates = \RatingItemPost::model()->cache(\Yii::app()->paramsWrap->cache->listRate, $dependency)->findAll($criteria);
        $friendsArray = array();
        /** @var \UserFriend[] $Friends */
        $Friends = \UserFriend::getFriends(\Yii::app()->user->id);
        foreach($Friends as $friend)
            $friendsArray[] = $friend->friend_id;

        $this->controller->renderPartial('postModule.index.post_rate_list', array(
            'models' => $Rates,
            'friend' => $friendsArray
        ));
    }
}