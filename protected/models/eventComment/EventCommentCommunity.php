<?php

/**
 * Class EventCommentVideo
 *
 * @package application.event.models
 */
class EventCommentCommunity extends EventComment
{
    /**
     * @param string $className
     * @return EventCommentVideo
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
            'params' => array(':'.$t.'_item_type' => ItemTypes::ITEM_TYPE_COMMUNITY)
        );
    }

    public function beforeValidate()
    {
        $this->item_type = ItemTypes::ITEM_TYPE_COMMUNITY;
        return parent::beforeValidate();
    }
}