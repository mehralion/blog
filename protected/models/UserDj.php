<?php

Yii::import('application.models._base.BaseUserDj');

/**
 * Class UserDj
 *
 *
 * Relations
 * @property User $user
 * @property float $hours_static
 */
class UserDj extends BaseUserDj
{
    /**
     * @param string $className
     * @return UserDj
     */
    public static function model($className=__CLASS__) {
		return parent::model($className);
	}

    public function rules() {
        return array(
            array('user_id, radio_type', 'required'),
            array('user_id, radio_type', 'numerical', 'integerOnly'=>true),
            array('game_bank, icq, skype, login', 'length', 'max'=>255),
            array('game_bank, icq, skype', 'default', 'setOnEmpty' => true, 'value' => null),
            array('user_id, radio_type, game_bank, icq, skype', 'safe', 'on'=>'search'),
        );
    }

    public function relations()
    {
        return array(
            'user' => array(
                self::BELONGS_TO,
                'User',
                'user_id',
                'joinType' => 'inner join',
            ),
        );
    }
}