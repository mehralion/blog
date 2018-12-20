<?php

Yii::import('application.models._base.BaseRights');

/**
 * Class Access
 *
 * @property RightsType $access_type
 * @property User $user
 * @property integer $item_id
 */
class Rights extends BaseRights
{
    public $id;

    /**
     * @param string $className
     * @return Rights
     */
    public static function model($className=__CLASS__) {
		return parent::model($className);
	}

    public function rules() {
        return array(
            array('user_id, item_id', 'required'),
            array('user_id, item_id, ', 'numerical', 'integerOnly'=>true),
            array('user_idm item_id', 'safe', 'on'=>'search'),
        );
    }

    /**
     * @return array
     */
    public function relations() {
        return array(
            'rights_type' => array(
                self::BELONGS_TO,
                'RightsType',
                'item_id',
                'joinType' => 'inner join',
            ),
            'user' => array(
                self::BELONGS_TO,
                'User',
                'user_id',
                'joinType' => 'inner join'
            )
        );
    }
}