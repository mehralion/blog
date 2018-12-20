<?php

Yii::import('application.models._base.BaseCommunityCategory');

/**
 * Class CommunityCategory
 *
 * Relations
 * @property Community[] $community
 * @property Community $hasCommunity
 */
class CommunityCategory extends BaseCommunityCategory
{
    const LIMIT = 10;
    /**
     * @param string $className
     * @return CommunityCategory
     */
    public static function model($className=__CLASS__) {
		return parent::model($className);
	}

    public function relations() {
        return array(
            'community' => array(
                self::HAS_MANY,
                'Community',
                'category_id',
                'joinType' => 'inner join',
                'limit' => self::LIMIT,
                'order' => 'community.create_datetime desc',
                'scopes' => array('deletedStatus', 'moderDeletedStatus', 'truncatedStatus'),
                'params' => array(':deletedStatus' => 0, ':moderDeletedStatus' => 0, ':truncatedStatus' => 0)
            ),
            'hasCommunity' => array(
                self::HAS_ONE,
                'Community',
                'category_id',
                'joinType' => 'inner join',
                'scopes' => array('deletedStatus', 'moderDeletedStatus', 'truncatedStatus'),
                'params' => array(':deletedStatus' => 0, ':moderDeletedStatus' => 0, ':truncatedStatus' => 0)
            )
        );
    }
}