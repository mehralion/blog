<?php

Yii::import('application.models._base.BaseCommunityRequest');

/**
 * Class CommunityRequest
 *
 * Relations
 * @property User $user
 * @property Community $community
 *
 * Params
 * @property integer $isModer
 *
 */
class CommunityRequest extends BaseCommunityRequest
{
    const STATUS_PENDING = 0; //Ожидание
    const STATUS_SUCCESS = 1; //Успешный запрос
    const STATUS_FAIL    = 2; //Пользователь отказался
    const STATUS_CANCEL  = 3; //Отозвали запрос

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
            'requestStatus' => array(
                'condition' => $t.'.request_status = :requestStatus',
            ),
            'pending' => array(
                'condition' => $t.'.request_status = :pendingRequestStatus',
                'params' => array(':pendingRequestStatus' => self::STATUS_PENDING)
            )
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
}