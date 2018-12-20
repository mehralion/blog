<?php

/**
 * Class ModerLog
 * @property array onRestore
 *
 * @property User $moder
 * @property User $user
 * @property UserSilence $silence
 * @property Post $post
 * @property GalleryImage $image
 * @property GalleryVideo $video
 * @property CommentItem $comment
 * @property User $owner
 * @property GalleryAlbumAudio $audio
 *
 * @property string $update_datetime
 *
 * @property integer $is_report
 * @property boolean $group
 * @property boolean $is_last
 * @property integer $silence_id
 * @property integer $user_owner_id
 *
 * @package application.moder.models
 */
class ModerLog extends BaseModerLog
{
    const ITEM_OPERATION_RESTORE         = 1;
    const ITEM_OPERATION_DELETE          = 2;
    const ITEM_OPERATION_SILENCE         = 3;
    const ITEM_OPERATION_SILENCE_RESTORE = 4;
    const ITEM_OPERATION_DECLINE         = 5;

    public $group = false;
    public $date_start = null;
    public $date_end = null;

    /**
     * @param string $className
     * @return ModerLog
     */
    public static function model($className=__CLASS__) {
		return parent::model($className);
	}

    /**
     * @return bool
     */
    public function beforeValidate()
    {
        $this->moder_id = Yii::app()->user->id;
        return parent::beforeValidate();
    }

    /**
     * @return array
     */
    public function rules() {
        return array(
            array('moder_reason', 'required', 'on' => 'moder'),
            array('moder_id, item_id, item_type, create_datetime, operation_type', 'required', 'on' => 'insert'),
            array('moder_id, item_id, item_type, operation_type', 'numerical', 'integerOnly'=>true),
            array('id, moder_id, item_id, item_type, moder_reason, create_datetime, operation_type, group, date_start, date_end, is_last', 'safe', 'on'=>'search'),
        );
    }

    /**
     * @return array
     */
    public function relations()
    {
        return array(
            'moder'      => array(self::HAS_ONE, 'User', array('id' => 'moder_id'), 'joinType' => 'inner join'),
            'post'       => array(self::HAS_ONE, 'Post', array('id' => 'item_id')),
            'image'      => array(self::HAS_ONE, 'GalleryImage', array('id' => 'item_id')),
            'video'      => array(self::HAS_ONE, 'GalleryVideo', array('id' => 'item_id')),
            'comment'    => array(self::HAS_ONE, 'CommentItem', array('id' => 'item_id')),
            'audio'      => array(self::HAS_ONE, 'GalleryAlbumAudio', array('id' => 'item_id')),
            'silence'    => array(self::HAS_ONE, 'UserSilence', array('id' => 'silence_id'), 'with' => array('user')),
            'owner'      => array(self::HAS_ONE, 'User', array('id' => 'user_owner_id'), 'joinType' => 'inner join'),
            'user'       => array(self::HAS_ONE, 'User', array('id' => 'item_id'))
        );
    }

    /**
     * @return array
     */
    public function attributeLabels() {
        return array(
            'id' => Yii::t('app', 'ID'),
            'moder_id' => Yii::t('app', 'Модератор'),
            'item_id' => Yii::t('app', 'Item'),
            'item_type' => Yii::t('app', 'Тип'),
            'moder_reason' => Yii::t('app', 'Причина'),
            'create_datetime' => Yii::t('app', 'Create Datetime'),
            'operation_type' => Yii::t('app', 'Тип операции'),
            'group' => Yii::t('app', 'Группировать'),
            'is_last' => Yii::t('app', 'Только последние действия над материалами'),
            'date_start' => Yii::t('app', 'Начало'),
            'date_end' => Yii::t('app', 'Конец'),
        );
    }

    /**
     *
     */
    public function afterSave()
    {
        $criteria = new CDbCriteria();
        $criteria->addCondition('id != :id');
        $criteria->addCondition('item_id = :item_id');
        $criteria->addCondition('item_type = :item_type');
        $criteria->params = array(
            ':id' => $this->id,
            ':item_id' => $this->item_id,
            ':item_type' => $this->item_type
        );
        self::model()->updateAll(array('is_last' => 0), $criteria);

        return parent::afterSave();
    }

    /**
     * @param null $parentId
     * @return CActiveDataProvider
     */
    public function search($parentId = null) {
        $params = array(
            'pagination' => array(
                'pageSize' => 10
            )
        );

        $criteria = new CDbCriteria;

        $criteria->compare('`t`.id', $this->id);
        $criteria->compare('`t`.moder_id', $this->moder_id);
        $criteria->compare('`t`.item_id', $this->item_id);
        $criteria->compare('`t`.item_type', $this->item_type);
        $criteria->compare('`t`.moder_reason', $this->moder_reason, true);
        $criteria->compare('`t`.create_datetime', $this->create_datetime, true);
        $criteria->compare('`t`.operation_type', $this->operation_type);
        $criteria->order = '`t`.create_datetime desc';
        $criteria->with = array(
            'moder',
            'silence',
            'image',
            'post',
            'video',
            'comment',
            'user' => array('alias' => 'itemUser')
        );

        if(!empty($this->date_start)) {
            $criteria->addCondition('`t`.create_datetime >= :date_start');
            $criteria->params = CMap::mergeArray($criteria->params, array(
                ':date_start' => date(Yii::app()->params['dbTimeFormat'], strtotime($this->date_start.' 00:00:00'))
            ));
        }
        if(!empty($this->date_end)) {
            $criteria->addCondition('`t`.create_datetime <= :date_end');
            $criteria->params = CMap::mergeArray($criteria->params, array(
                ':date_end' => date(Yii::app()->params['dbTimeFormat'], strtotime($this->date_end.' 23:59:59'))
            ));
        }


        if(null !== $parentId) {
            $criteria->addCondition('`t`.id != :i_id');
            $criteria->params = CMap::mergeArray($criteria->params, array(':i_id' => $parentId));
            $params['pagination']['pageSize'] = 0;
        } elseif($this->is_last) {
            $criteria->addCondition('`t`.is_last = :is_last');
            $criteria->params = CMap::mergeArray($criteria->params, array(':is_last' => 1)); //instead of grouping
        }

        $dependency = new CDbCacheDependency('SELECT MAX(update_datetime) FROM moder_log');
        $dependency->reuseDependentData = true;

        return new CActiveDataProvider(self::model()->cache(Yii::app()->paramsWrap->cache->moderLog, $dependency, 2), CMap::mergeArray($params, array(
            'criteria' => $criteria,
        )));
    }

    /**
     * @return array
     */
    public static function getItemTypes()
    {
        return array(
            ItemTypes::ITEM_TYPE_POST     => 'Заметки',
            ItemTypes::ITEM_TYPE_IMAGE    => 'Фотографии',
            ItemTypes::ITEM_TYPE_VIDEO    => 'Видео',
            ItemTypes::ITEM_TYPE_COMMENT  => 'Комментарии',
            ItemTypes::ITEM_TYPE_SILENCE  => 'Молчанки',
        );
    }

    /**
     * @return array
     */
    public static function getOperationTypes()
    {
        return array(
            self::ITEM_OPERATION_DELETE          => 'Удаление',
            self::ITEM_OPERATION_RESTORE         => 'Восстановление',
            self::ITEM_OPERATION_SILENCE         => 'Молчанка',
            self::ITEM_OPERATION_SILENCE_RESTORE => 'Снятие молчанок',
        );
    }

    /**
     * @var array
     */
    private $_viewDescription = array(
        ItemTypes::ITEM_TYPE_SILENCE     => 'silence',
        ItemTypes::ITEM_TYPE_IMAGE       => 'image',
        ItemTypes::ITEM_TYPE_POST        => 'post',
        ItemTypes::ITEM_TYPE_VIDEO       => 'video',
        ItemTypes::ITEM_TYPE_COMMENT     => 'comment',
        ItemTypes::ITEM_TYPE_AUDIO_ALBUM => 'audio',
        ItemTypes::ITEM_TYPE_COMMUNITY   => 'community',
    );

    /**
     * @return string
     */
    public function getDescriptionString()
    {
        return Yii::app()->getController()->renderPartial(
            'themePath.views.modules.moder.common.'.$this->_viewDescription[$this->item_type], array(
                'model' => $this
            ),
            true
        );
    }

    /**
     * @return bool
     */
    public function visibleRestore()
    {
        return $this->is_last && ($this->operation_type == self::ITEM_OPERATION_DELETE || $this->operation_type == self::ITEM_OPERATION_SILENCE);
    }

    /** @var array  */
    private $_restoreRouts = array(
        ItemTypes::ITEM_TYPE_VIDEO    => '/moder/video/restore',
        ItemTypes::ITEM_TYPE_COMMENT  => '/moder/comment/restore',
        ItemTypes::ITEM_TYPE_IMAGE    => '/moder/image/restore',
        ItemTypes::ITEM_TYPE_POST     => '/moder/post/restore',
        ItemTypes::ITEM_TYPE_SILENCE  => '/moder/silence/restore',
    );

    /**
     * @return string
     */
    public function getRestoreUrl()
    {
        return Yii::app()->createUrl($this->_restoreRouts[$this->item_type], array("id" => $this->id));
    }
}