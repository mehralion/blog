<?php

Yii::import('application.models._base.BasePollAnswer');

class PollAnswer extends BasePollAnswer
{
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

    public function relations() {
        return array(
            'poll' => array(self::BELONGS_TO, 'Poll', 'poll_id'),
        );
    }
}