<?php

/**
 * Class CacheEventItem
 *
 * Params
 * @property integer $user_id
 * @property integer $album_id
 * @property integer $community_id
 */
class CacheEventItem extends BaseCacheEventItem
{
    /**
     * @param string $className
     * @return CacheEventItem
     */
    public static function model($className=__CLASS__) {
		return parent::model($className);
	}
}