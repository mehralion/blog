<?php

Yii::import('application.models._base.BaseUserSilence');
/**
 * Class UserSilence
 *
 * @property integer silence_type
 * @property User $user
 * @property User $moder
 * @property Report $report
 *
 * @package application.user.models
 */
class UserSilence extends BaseUserSilence
{

    const SILENCE_DAY = 7;

    const SILENCE_TYPE_REPORT = 0;
    const SILENCE_TYPE_BASIC = 1;

    /**
     * @param string $className
     * @return UserSilence
     */
    public static function model($className=__CLASS__) {
		return parent::model($className);
	}

    /**
     * @return array
     */
    public function attributeLabels() {
        return array(
            'id' => Yii::t('app', 'ID'),
            'user_id' => null,
            'sender_id' => null,
            'report_id' => Yii::t('app', 'Report'),
            'comment' => Yii::t('app', 'Comment'),
            'moder_reason' => Yii::t('app', 'Комментарий'),
            'create_datetime' => Yii::t('app', 'Create Datetime'),
            'end_datetime' => Yii::t('app', 'End Datetime'),
            'sender' => null,
            'user' => null,
        );
    }

    /**
     * @return array
     */
    public function relations()
    {
        return array(
            'user' => array(
                self::HAS_ONE,
                'User',
                array('id' => 'user_id'),
            ),
            'moder' => array(
                self::HAS_ONE,
                'User',
                array('id' => 'sender_id')
            ),
            'report' => array(
                self::HAS_ONE,
                'Report',
                array('id' => 'report_id')
            )
        );
    }

    public function rules() {
        return array(
            array('user_id, sender_id, create_datetime, end_datetime', 'required'),
            array('moder_reason', 'required', 'on' => 'moder'),
            array('user_id, sender_id, report_id', 'numerical', 'integerOnly'=>true),
            array('comment', 'safe'),
            array('report_id, comment', 'default', 'setOnEmpty' => true, 'value' => null),
            array('id, user_id, sender_id, report_id, comment, moder_reason, create_datetime, end_datetime', 'safe', 'on'=>'search'),
        );
    }
}