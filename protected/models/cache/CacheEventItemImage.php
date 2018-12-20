<?php
class CacheEventItemImage extends CacheEventItem
{
    /**
     * @param string $className
     * @return CacheEventItemImage
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
                ':'.$t.'_item_type' => ItemTypes::ITEM_TYPE_IMAGE
            )
        );
    }

    public function beforeValidate()
    {
        $this->item_type = ItemTypes::ITEM_TYPE_IMAGE;
        return parent::beforeValidate();
    }

    /**
     * @param integer $id
     * @return boolean
     */
    public static function updateByItemId($id)
    {
        return self::model()->updateAll(
            array('update_datetime' => DateTimeFormat::format()),
            'item_id = :item_id and item_type = :item_type',
            array(':item_id' => $id, ':item_type' => ItemTypes::ITEM_TYPE_IMAGE)
        );
    }

    /**
     * @param integer $id
     * @return boolean
     */
    public static function updateAllByAlbumId($id)
    {
        return self::model()->updateAll(
            array('update_datetime' => DateTimeFormat::format()),
            'album_id = :album_id and item_type = :item_type',
            array(':album_id' => $id, ':item_type' => ItemTypes::ITEM_TYPE_IMAGE)
        );
    }
}