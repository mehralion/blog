<?php

Yii::import('application.models._base.BaseLog');

/**
 * Class Log
 *
 * @property integer custom_id
 * @property string description4
 *
 * Relations
 * @property User $owner
 */
class Log extends BaseLog
{
    const TYPE_RADIO = 0;
    const TYPE_USER_UPDATE = 1;

    const LEVEL_0 = 0; //DEBUG
    const LEVEL_1 = 1; //INFO
    const LEVEL_2 = 2; //WARNING
    const LEVEL_3 = 3; //CRITICAL

    const LEVEL_MIN_WRITE = 1;
    const LEVEL_MIN_READ = 1;

    /**
     * @param string $className
     * @return Log
     */
    public static function model($className=__CLASS__) {
		return parent::model($className);
	}

    public function beforeSave()
    {
        if($this->log_level < self::LEVEL_MIN_WRITE)
            return false;

        return parent::beforeSave();
    }

    public function relations()
    {
        return array(
            'owner' => array(
                self::BELONGS_TO,
                'User',
                'owner_user_id',
            )
        );
    }
}