<?php

/**
 * Class EventItemPost
 *
 * @package application.event.models
 */
class EventItemPost extends EventItem
{
    /**
     * @param string $className
     * @return EventItemPost
     */
    public static function model($className=__CLASS__) {
        return parent::model($className);
    }

    /**
     * @return array
     */
    public function defaultScope() {
        $t = $this->getTableAlias(false, false);
        return array(
            'condition' => $t . '.item_type = :'.$t.'_item_type',
            'params' => array(':'.$t.'_item_type' => ItemTypes::ITEM_TYPE_POST)
        );
    }

    public function beforeValidate()
    {
        $this->item_type = ItemTypes::ITEM_TYPE_POST;
        return parent::beforeValidate();
    }
}