<?php
class EventViewDatetimeComment extends EventViewDatetime
{
    /**
     * @param string $className
     * @return EventViewDatetimeComment
     */
    public static function model($className=__CLASS__) {
		return parent::model($className);
	}

    public function defaultScope()
    {
        $t = $this->getTableAlias(false, false);
        return array(
            'condition' => $t.'.item_type = :'.$t.'_item_type',
            'params' => array(':'.$t.'_item_type' => ItemTypes::ITEM_TYPE_COMMENT)
        );
    }

    public function beforeValidate()
    {
        $this->item_type = ItemTypes::ITEM_TYPE_COMMENT;
        return parent::beforeValidate();
    }
}