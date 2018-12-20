<?php

Yii::import('application.models._base.BaseCommunityUser');

/**
 * Class CommunityUser
 *
 * Relations
 * @property User $user
 * @property Community $community
 *
 * Params
 * @property integer $is_deleted
 * @property integer $user_type
 * @property string update_datetime
 */
class CommunityUser extends BaseCommunityUser
{
    const TYPE_USER = 0;
    const TYPE_MODER = 1;
    const TYPE_ADMIN = 2;

    /**
     * @param string $className
     * @return CommunityUser
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
            'deletedStatus' => array(
                'condition' => $t.'.is_deleted = :deletedStatus',
            ),
            'moders' => array(
                'condition' => $t.'.user_type > 0'
            ),
            'norModers' => array(
                'condition' => $t.'.user_type = 0'
            ),
        );
    }

    public function relations() {
        return array(
            'user' => array(
                self::BELONGS_TO,
                'User',
                'user_id',
                'joinType' => 'inner join'
            ),
            'community' => array(
                self::BELONGS_TO,
                'Community',
                'community_id',
                'joinType' => 'inner join'
            )
        );
    }

    /** @var CommunityUser[]  */
    private static $communities;

    /**
     * @param $user_id
     * @param $array
     * @return CommunityUser[]|array
     */
    public static function getCommunities($user_id, $array = false)
    {
        if(null !== self::$communities && isset(self::$communities[$user_id])) {
            if($array === false)
                return self::$communities[$user_id];
            else {
                $returnedArray = array();
                foreach(self::$communities[$user_id] as $friend)
                    $returnedArray[] = $friend->friend_id;

                return $returnedArray;
            }
        }

        $criteria = new CDbCriteria();
        $criteria->addCondition('user_id = :u_id');
        $criteria->params = array(
            ':u_id' => $user_id,
        );

        $dependency = new CDbCacheDependency('SELECT MAX(update_datetime) FROM {{community_user}} where user_id = :user_id');
        $dependency->params = array(':user_id' => $user_id);
        $dependency->reuseDependentData = true;
        self::$communities[$user_id] = CommunityUser::model()->cache(Yii::app()->paramsWrap->cache->friends, $dependency)->findAll($criteria);
        if($array === false)
            return self::$communities[$user_id];
        else {
            $returnedArray = array();
            foreach(self::$communities[$user_id] as $community)
                $returnedArray[] = $community->community_id;

            return $returnedArray;
        }
    }
}