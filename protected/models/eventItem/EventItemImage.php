<?php

/**
 * Class EventItemImage
 *
 * @package application.event.models
 */
class EventItemImage extends EventItem
{
    /**
     * @param string $className
     * @return EventItemImage
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
            'params' => array(':'.$t.'_item_type' => ItemTypes::ITEM_TYPE_IMAGE)
        );
    }

    public function beforeValidate()
    {
        $this->item_type = ItemTypes::ITEM_TYPE_IMAGE;
        return parent::beforeValidate();
    }
}