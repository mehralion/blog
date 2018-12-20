<?php

class ItemInfoCommunity extends ItemInfo
{
    /**
     * @param string $className
     * @return ItemInfoCommunity
     */
    public static function model($className=__CLASS__) {
		return parent::model($className);
	}

    public function defaultScope()
    {
        $t = $this->getTableAlias(false, false);
        return array(
            'condition' => $t.'.item_type = :'.$t.'_item_type',
            'params' => array(':'.$t.'_item_type' => ItemTypes::ITEM_TYPE_COMMUNITY)
        );
    }

    public function beforeValidate()
    {
        $this->item_type = ItemTypes::ITEM_TYPE_COMMUNITY;
        return parent::beforeValidate();
    }

    public static function updateAllByCommunityId($attributes, $condition, $params)
    {
        return self::model()->updateAll($attributes, $condition, $params);
    }
}