<?php

class Bug extends BaseBug
{
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

    public function attributeLabels() {
        return array(
            'id' => Yii::t('app', 'ID'),
            'user_id' => Yii::t('app', 'User'),
            'description' => Yii::t('app', 'Текст'),
        );
    }
}