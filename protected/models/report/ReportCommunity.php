<?php

/**
 * Class ReportPost
 *
 * @package application.report.models
 */
class ReportCommunity extends Report
{
    /**
     * @param string $className
     * @return ReportPost
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
            'params' => array(':'.$t.'_item_type' => ItemTypes::ITEM_TYPE_COMMUNITY)
        );
    }

    public function beforeValidate()
    {
        $this->item_type = ItemTypes::ITEM_TYPE_COMMUNITY;
        return parent::beforeValidate();
    }

    public function search() {
        $criteria = new CDbCriteria;

        $criteria->compare('`t`.id', $this->id);
        $criteria->compare('`t`.user_id', $this->user_id);
        $criteria->compare('`t`.item_id', $this->item_id);
        $criteria->compare('`t`.user_owner_id', $this->user_owner_id);
        $criteria->compare('`t`.item_type', $this->item_type);
        $criteria->compare('`t`.create_datetime', $this->create_datetime, true);
        $criteria->compare('`t`.status', $this->status);

        $criteria->with = array('community');

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }
}