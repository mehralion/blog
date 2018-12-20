<?php

/**
 * Class RatingItemImage
 *
 * @package application.rating.models
 */
class RatingItemAudio extends RatingItem
{
    /**
     * @param string $className
     * @return RatingItemImage
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
            'params' => array(
                ':'.$t.'_item_type' => ItemTypes::ITEM_TYPE_AUDIO_ALBUM
            )
        );
    }

    public function beforeValidate()
    {
        $this->item_type = ItemTypes::ITEM_TYPE_AUDIO_ALBUM;
        return parent::beforeValidate();
    }
}