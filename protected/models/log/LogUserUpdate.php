<?php

class LogUserUpdate extends Log
{
    const LEVEL_MIN_WRITE = 1;
    const LEVEL_MIN_READ = 1;

    /**
     * @param string $className
     * @return LogRadio
     */
    public static function model($className=__CLASS__) {
		return parent::model($className);
	}

    public function defaultScope()
    {
        $t = $this->getTableAlias(false, false);
        return array(
            'condition' => $t.'.log_type = :'.$t.'_log_type',
            'params' => array(':'.$t.'_log_type' => self::TYPE_USER_UPDATE)
        );
    }

    public function beforeValidate()
    {
        $this->log_type = self::TYPE_USER_UPDATE;
        return parent::beforeValidate();
    }

    public function getUser()
    {
        if($this->owner_user_id)
            return $this->owner->getFullLogin();

        return '-- / -- / --';
    }
}