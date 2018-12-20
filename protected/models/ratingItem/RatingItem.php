<?php

/**
 * Class RatingItem
 *
 * Params
 * @property integer value_type
 * @property integer user_owner_id
 *
 * @package application.rating.models
 */
class RatingItem extends BaseRatingItem
{
    const VALUE_TYPE_ADD     = 0;
    const VALUE_TYPE_TAKE    = 1;

    public $cnt;

    /**
     * @param string $className
     * @return RatingItem
     */
    public static function model($className=__CLASS__) {
        return parent::model($className);
    }

    /**
     * @return array
     */
    public function scopes()
    {
        $t = $this->getTableAlias(false, false);
        return array(
            'post' => array(
                'condition' => $t.'.item_type = :'.$t.'_item_type',
                'params' => array(':'.$t.'_item_type' => ItemTypes::ITEM_TYPE_POST)
            ),
            'image' => array(
                'condition' => $t.'.item_type = :'.$t.'_item_type',
                'params' => array(':'.$t.'_item_type' => ItemTypes::ITEM_TYPE_IMAGE)
            ),
            'video' => array(
                'condition' => $t.'.item_type = :'.$t.'_item_type',
                'params' => array(':'.$t.'_item_type' => ItemTypes::ITEM_TYPE_VIDEO)
            ),
            'own' => array(
                'condition' => $t.'.user_id = :'.$t.'_u_id',
                'params' => array(':'.$t.'_u_id' => Yii::app()->user->id)
            ),
            'deletedStatus' =>  array(
                'condition' => $t.'.is_deleted = :deletedStatus',
            )
        );
    }

    public function relations() {
        return array(
            'user'           => array(self::BELONGS_TO, 'User', 'user_id', 'joinType' => 'inner join'),
            'userProfile'    => array(self::HAS_ONE, 'UserProfile', array('user_id' => 'user_id'), 'joinType' => 'inner join'),
        );
    }
}