<?php

class CacheEventItemCommunity extends CacheEventItem
{
    /**
     * @param string $className
     * @return CacheEventItemVideo
     */
    public static function model($className=__CLASS__) {
		return parent::model($className);
	}

    public function defaultScope()
    {
        $t = $this->getTableAlias(false, false);
        return array(
            'condition' => $t.'.item_type = :'.$t.'_item_type',
            'params' => array(
                ':'.$t.'_item_type' => ItemTypes::ITEM_TYPE_COMMUNITY
            )
        );
    }

    public function beforeValidate()
    {
        $this->item_type = ItemTypes::ITEM_TYPE_COMMUNITY;
        return parent::beforeValidate();
    }

    /**
     * @param integer $id
     * @return boolean
     */
    public static function updateAllByCommunityId($id)
    {
        return self::model()->updateAll(
            array('update_datetime' => DateTimeFormat::format()),
            'community_id = :community_id',
            array(':community_id' => $id)
        );
    }
}