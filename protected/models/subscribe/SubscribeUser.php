<?php

Yii::import('application.models._base.BaseSubscribeUser');

/**
 * Class SubscribeUser
 *
 * @property integer subscribe_type
 *
 * @property Community ownerCommunity
 */
class SubscribeUser extends Subscribe
{
    /**
     * @param string $className
     * @return SubscribeUser
     */
    public static function model($className=__CLASS__) {
		return parent::model($className);
	}

    public function defaultScope()
    {
        $t = $this->getTableAlias(false, false);
        return array(
            'condition' => $t.'.subscribe_type = :'.$t.'_subscribe_type',
            'params' => array(':'.$t.'_subscribe_type' => ItemTypes::SUBSCRIBE_USER)
        );
    }

    public function beforeValidate()
    {
        $this->subscribe_type = ItemTypes::SUBSCRIBE_USER;
        return parent::beforeValidate();
    }
}