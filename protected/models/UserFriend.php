<?php

Yii::import('application.models._base.BaseUserFriend');
/**
 * Class UserFriend
 *
 *
 * @property User $friend
 * @property User $user
 *
 * @method UserFriend own()
 *
 * @package application.user.models
 */
class UserFriend extends BaseUserFriend
{
    /**
     * @param string $className
     * @return UserFriend
     */
    public static function model($className=__CLASS__) {
        return parent::model($className);
    }

    /**
     * @return array
     */
    public function relations() {
        return array(
            'friend' => array(
                self::BELONGS_TO,
                'User',
                'friend_id',
                'joinType' => 'inner join'
            ),
            'user' => array(
                self::BELONGS_TO,
                'User',
                'user_id',
                'joinType' => 'inner join'
            ),
        );
    }

    /**
     * @return array
     */
    public function scopes()
    {
        $t = $this->getTableAlias(false, false);
        return array(
            'own' => array(
                'condition' => $t.'.user_id = :'.$t.'_u_id',
                'params' => array(':'.$t.'_u_id' => Yii::app()->user->id)
            )
        );
    }
    /** @var UserFriend[]  */
    private static $friends;

    /**
     * @param $user_id
     * @param $array
     * @return UserFriend[]|array
     */
    public static function getFriends($user_id, $array = false)
    {
        if(null !== self::$friends && isset(self::$friends[$user_id])) {
            if($array === false)
                return self::$friends[$user_id];
            else {
                $returnedArray = array();
                foreach(self::$friends[$user_id] as $friend)
                    $returnedArray[] = $friend->friend_id;

                return $returnedArray;
            }
        }

        $criteria = new CDbCriteria();
        $criteria->addCondition('user_id = :u_id');
        $criteria->params = array(
            ':u_id' => $user_id,
        );
        $criteria->select = 'friend_id';

        $dependency = new CDbCacheDependency('SELECT COUNT(*) FROM user_friend where user_id = :user_id');
        $dependency->params = array(':user_id' => $user_id);
        $dependency->reuseDependentData = true;
        self::$friends[$user_id] = UserFriend::model()->cache(Yii::app()->paramsWrap->cache->friends, $dependency)->findAll($criteria);
        if($array === false)
            return self::$friends[$user_id];
        else {
            $returnedArray = array();
            foreach(self::$friends[$user_id] as $friend)
                $returnedArray[] = $friend->friend_id;

            return $returnedArray;
        }
    }
}