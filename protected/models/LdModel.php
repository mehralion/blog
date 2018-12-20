<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Nick Nikitchenko
 * Skype: quietasice
 * E-mail: quietasice123@gmail.com
 * Date: 15.07.13
 * Time: 16:35
 * To change this template use File | Settings | File Templates.
 *
 * @property int $id
 * @property User $user
 * @property User $moder
 * @property string $datetime
 * @property string $comment
 * @property string $datebegin
 * @property string $dateend
 *
 * @package application.moder.models
 */
class LdModel extends ModerLog
{
    public $id;
    public $user;
    public $moder;
    public $datetime;
    public $datebegin;
    public $dateend;
    public $comment;
    public $operation_type;

    /**
     * @param string $className
     * @return LdModel
     */
    public static function model($className=__CLASS__) {
        return parent::model($className);
    }

    /**
     * @return array
     */
    public function attributeNames()
    {
        return array(
            'id' => 'ID',
            'user' => 'Пользователь',
            'moder' => 'Модератор',
            'comment' => 'Причина',
            'datetime' => 'Дата'
        );
    }

    /**
     * @return array
     */
    public function attributeLabels() {
        return array(
            'id' => Yii::t('app', 'ID'),
            'user' => Yii::t('app', 'Пользователь'),
            'moder' => Yii::t('app', 'Модератор'),
            'datetime' => Yii::t('app', 'Дата'),
            'comment' => Yii::t('app', 'Причина'),
        );
    }

    /**
     * @return bool
     */
    public function visibleRestore()
    {
        if($this->operation_type == self::ITEM_OPERATION_SILENCE && $this->user->is_silenced)
            return true;
        elseif($this->operation_type == ModerLog::ITEM_OPERATION_DELETE)
            return true;
        else
            return false;
    }
}