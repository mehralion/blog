<?php
Yii::import('application.models._base.BaseEventFriend');

/**
 * Class EventFriend
 *
 * @package application.event.models
 */
class EventFriend extends BaseEventFriend
{
    /**
     * @param string $className
     * @return EventFriend
     */
    public static function model($className=__CLASS__) {
		return parent::model($className);
	}
}