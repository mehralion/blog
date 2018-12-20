<?php

class EventViewDatetime extends BaseEventViewDatetime
{
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

    public static function getTypesArray()
    {
        return array(
            ItemTypes::ITEM_TYPE_POST,
            ItemTypes::ITEM_TYPE_IMAGE,
            ItemTypes::ITEM_TYPE_VIDEO,
            ItemTypes::ITEM_TYPE_AUDIO_ALBUM,
            ItemTypes::ITEM_TYPE_COMMUNITY,
            ItemTypes::ITEM_TYPE_COMMENT,
        );
    }
}