<?php

/**
 * Class ReportPost
 *
 * @package application.report.models
 */
class ReportPost extends Report
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
            'params' => array(':'.$t.'_item_type' => ItemTypes::ITEM_TYPE_POST)
        );
    }

    public function beforeValidate()
    {
        $this->item_type = ItemTypes::ITEM_TYPE_POST;
        return parent::beforeValidate();
    }

    public function search() {
        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('user_id', $this->user_id);
        $criteria->compare('item_id', $this->item_id);
        $criteria->compare('user_owner_id', $this->user_owner_id);
        $criteria->compare('item_type', $this->item_type);
        $criteria->compare('create_datetime', $this->create_datetime, true);
        $criteria->compare('status', $this->status);

        $criteria->with = array('post');

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }
}