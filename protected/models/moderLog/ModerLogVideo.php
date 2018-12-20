<?php

/**
 * Class ModerLogVideo
 *
 * @package application.moder.models
 */
class ModerLogVideo extends ModerLog
{
    /**
     * @param string $className
     * @return ModerLogVideo
     */
    public static function model($className=__CLASS__) {
        return parent::model($className);
    }

    /**
     * @return array
     */
    public function defaultScope() {
        $t = $this->getTableAlias(false, false);
        return array(
            'condition' => $t . '.item_type = :'.$t.'_item_type',
            'params' => array(':'.$t.'_item_type' => ItemTypes::ITEM_TYPE_VIDEO)
        );
    }

    public function beforeValidate()
    {
        $this->item_type = ItemTypes::ITEM_TYPE_VIDEO;
        return parent::beforeValidate();
    }
}