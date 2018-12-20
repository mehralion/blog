<?php

/**
 * Class UserProfile
 *
 *  @property integer $hide_deleted
 * @property UserDj userDj
 *
 * @package application.user.models
 */
class UserProfile extends BaseUserProfile
{
    const AVATAR_PATH = '';

    /** @var null|CUploadedFile */
    private $_uploadedFile = null;
    public $oldImage = null;

    public $bank = '';

    /**
     * @param string $className
     * @return UserProfile
     */
    public static function model($className=__CLASS__) {
		return parent::model($className);
	}

    /**
     * @return array
     */
    public function attributeLabels() {
        return array(
            'user_id' => null,
            'description' => Yii::t('app', 'Description'),
            'avatar_path' => Yii::t('app', 'Avatar Path'),
            'is_croped' => Yii::t('app', 'Is Croped'),
            'rating' => Yii::t('app', 'Rating'),
            'hide_deleted' => Yii::t('app', 'Скрывать удаленные материалы'),
            'user' => null,
        );
    }

    /**
     * @return array
     */
    public function rules() {
        return array(
            array('user_id', 'required'),
            array('user_id, is_croped, rating', 'numerical', 'integerOnly'=>true),
            array('avatar_path, bank', 'length', 'max'=>255),
            array('description, hide_deleted', 'safe'),
            array('description, avatar_path, is_croped, rating', 'default', 'setOnEmpty' => true, 'value' => null),
            array('user_id, description, avatar_path, is_croped, rating', 'safe', 'on'=>'search'),
            array('avatar_path', 'unsafe'),
            //array('avatar_path', 'file', 'allowEmpty'=>true, 'types'=>'jpg,gif,png,jpeg', 'tooLarge'=>'Максимально разрешенный размер '.IMAGE_SIZE_MB.'MB. Загрузите картинку по размерам.'),
        );
    }

    /**
     * @return array
     */
    public function relations() {
        return array(
            'userDj' => array(
                self::HAS_ONE,
                'UserDj',
                'user_id',
            ),
        );
    }

    /**
     *
     */
    public function afterSave()
    {
        return parent::afterSave();
    }

    public function getBaseUrl()
    {
        return Yii::app()->theme->uploadAvatar;
    }
}