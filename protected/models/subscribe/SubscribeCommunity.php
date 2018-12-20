<?php

class SubscribeCommunity extends Subscribe
{
    /**
     * @param string $className
     * @return SubscribeCommunity
     */
    public static function model($className=__CLASS__) {
		return parent::model($className);
	}

    public function defaultScope()
    {
        $t = $this->getTableAlias(false, false);
        return array(
            'condition' => $t.'.subscribe_type = :'.$t.'_subscribe_type',
            'params' => array(':'.$t.'_subscribe_type' => ItemTypes::SUBSCRIBE_COMMUNITY)
        );
    }

    public function beforeValidate()
    {
        $val = parent::beforeValidate();
        $this->subscribe_type = ItemTypes::SUBSCRIBE_COMMUNITY;

        return $val;
    }
}