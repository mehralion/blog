<?php

Yii::import('application.models._base.BaseFriendRequest');
/**
 * Class FriendRequest
 *
 * Params
 * @property string $update_datetime
 *
 * Relations
 * @property User $friend
 * @property User $user
 *
 * Scopes
 * @method FriendRequest pending()
 * @method FriendRequest own()
 *
 * @package application.friend.models
 */
class FriendRequest extends BaseFriendRequest
{
    const STATUS_PENDING    = 1;
    const STATUS_ACCEPTED   = 2;
    const STATUS_FAIL       = 3;
    const STATUS_CANCEL     = 4;
    const STATUS_DELETED    = 5;

    /**
     * @param string $className
     * @return FriendRequest
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
            'pending' => array(
                'condition' => $t.'.reciver_status = :'.$t.'_r_s and '.$t.'.sender_fail = 0',
                'params' => array(':'.$t.'_r_s' => self::STATUS_PENDING)
            ),
            'own' => array(
                'condition' => $t.'.friend_id = :'.$t.'_u_id',
                'params' => array(':'.$t.'_u_id' => Yii::app()->user->id)
            )
        );
    }

    /** @var UserFriend[]  */
    private static $friendsRequested;

    /**
     * @param $user_id
     * @param $array
     * @return UserFriend[]|array
     */
    public static function getFriendsRequested($user_id, $array = false)
    {
        if(null !== self::$friendsRequested && isset(self::$friendsRequested[$user_id])) {
            if($array === false)
                return self::$friendsRequested[$user_id];
            else {
                $returnedArray = array();
                foreach(self::$friendsRequested[$user_id] as $friend) {
                    if($friend->user_id == $user_id)
                        $returnedArray[] = $friend->friend_id;
                    else
                        $returnedArray[] = $friend->user_id;
                }

                return $returnedArray;
            }
        }

        $criteria = new CDbCriteria();
        $criteria->addCondition('user_id = :u_id or friend_id = :u_id');
        $criteria->addCondition('reciver_status = :accept or reciver_status = :pending');
        $criteria->params = array(
            ':u_id' => $user_id,
            ':accept' => self::STATUS_ACCEPTED,
            ':pending' => self::STATUS_PENDING
        );

        $dependency = new CDbCacheDependency('SELECT MAX(update_datetime) FROM friend_request where user_id = :user_id or friend_id = :user_id');
        $dependency->params = array(':user_id' => $user_id);
        $dependency->reuseDependentData = true;
        self::$friendsRequested[$user_id] = FriendRequest::model()->cache(Yii::app()->paramsWrap->cache->friends, $dependency)->findAll($criteria);
        if($array === false)
            return self::$friendsRequested[$user_id];
        else {
            $returnedArray = array();
            foreach(self::$friendsRequested[$user_id] as $friend) {
                if($friend->user_id == $user_id)
                    $returnedArray[] = $friend->friend_id;
                else
                    $returnedArray[] = $friend->user_id;
            }

            return $returnedArray;
        }
    }
}