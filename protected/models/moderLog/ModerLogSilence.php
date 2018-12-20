<?php

/**
 * Class ModerLogSilence
 *
 * @package application.moder.models
 */
class ModerLogSilence extends ModerLog
{
    /**
     * @param string $className
     * @return ModerLogSilence
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
            'params' => array(':'.$t.'_item_type' => ItemTypes::ITEM_TYPE_SILENCE)
        );
    }

    public function beforeValidate()
    {
        $this->item_type = ItemTypes::ITEM_TYPE_SILENCE;
        return parent::beforeValidate();
    }
}