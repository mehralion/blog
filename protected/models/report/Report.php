<?php

/**
 * Class Report
 *
 * @property array onAcceptReport
 * @property array onAfterAcceptReport
 *
 *
 * @property string $update_datetime
 * @property string $title
 * @property User $sender
 * @property User $owner
 * @property User $moder
 * @property Post $post
 * @property GalleryImage $image
 * @property GalleryVideo $video
 * @property CommentItem $comment
 * @property Community $community
 *
 * @method Report open()
 *
 * @package application.report.models
 */
class Report extends BaseReport
{
    const STATUS_PENDING = 0;
    const STATUS_DONE    = 1;
    const STATUS_NOTHING = 2;

    /** @var null | Post | CommentItem | GalleryImage | GalleryVideo */
    public $item = null;

    /**
     * @param string $className
     * @return Report
     */
    public static function model($className=__CLASS__) {
		return parent::model($className);
	}

    /**
     * @return bool
     */
    public function beforeValidate()
    {
        if($this->isNewRecord)
            $this->user_id = Yii::app()->user->id;
        else
            $this->user_moder_id = Yii::app()->user->id;

        return parent::beforeValidate();
    }

    /**
     * @return array
     */
    public function relations() {
        return array(
            'sender' => array(self::BELONGS_TO, 'User', 'user_id', 'joinType' => 'inner join'),
            'owner' => array(self::BELONGS_TO, 'User', 'user_owner_id', 'joinType' => 'inner join'),
            'moder' => array(self::BELONGS_TO, 'User', 'user_moder_id', 'joinType' => 'inner join'),
            'post' => array(
                self::BELONGS_TO,
                'Post',
                'item_id',
                'joinType' => 'inner join',
                'condition' => '`t`.item_type = :item_type',
                'scopes' => array('activatedStatus', 'deletedStatus', 'moderDeletedStatus', 'truncatedStatus'),
                'params' => array(
                    ':item_type' => ItemTypes::ITEM_TYPE_POST,
                    ':activatedStatus' => 1,
                    ':deletedStatus' => 0,
                    ':moderDeletedStatus' => 0,
                    ':truncatedStatus' => 0,
                )
            ),
            'image' => array(
                self::BELONGS_TO,
                'GalleryImage',
                'item_id',
                'joinType' => 'inner join',
                'condition' => '`t`.item_type = :item_type',
                'scopes' => array('activatedStatus', 'deletedStatus', 'moderDeletedStatus', 'truncatedStatus'),
                'params' => array(
                    ':item_type' => ItemTypes::ITEM_TYPE_IMAGE,
                    ':activatedStatus' => 1,
                    ':deletedStatus' => 0,
                    ':moderDeletedStatus' => 0,
                    ':truncatedStatus' => 0,
                )
            ),
            'video' => array(
                self::BELONGS_TO,
                'GalleryVideo',
                'item_id',
                'joinType' => 'inner join',
                'condition' => '`t`.item_type = :item_type',
                'scopes' => array('activatedStatus', 'deletedStatus', 'moderDeletedStatus', 'truncatedStatus'),
                'params' => array(
                    ':item_type' => ItemTypes::ITEM_TYPE_VIDEO,
                    ':activatedStatus' => 1,
                    ':deletedStatus' => 0,
                    ':moderDeletedStatus' => 0,
                    ':truncatedStatus' => 0,
                )
            ),
            'audio' => array(
                self::BELONGS_TO,
                'GalleryAlbumAudio',
                'item_id',
                'joinType' => 'inner join',
                'condition' => '`t`.item_type = :item_type',
                'scopes' => array('activatedStatus', 'deletedStatus', 'moderDeletedStatus', 'truncatedStatus'),
                'params' => array(
                    ':item_type' => ItemTypes::ITEM_TYPE_AUDIO_ALBUM,
                    ':activatedStatus' => 1,
                    ':deletedStatus' => 0,
                    ':moderDeletedStatus' => 0,
                    ':truncatedStatus' => 0,
                )
            ),
            'comment' => array(
                self::BELONGS_TO,
                'CommentItem',
                'item_id',
                'joinType' => 'inner join',
                'condition' => '`t`.item_type = :item_type',
                'scopes' => array('activatedStatus', 'deletedStatus', 'moderDeletedStatus' , 'truncatedStatus'),
                'params' => array(
                    ':item_type' => ItemTypes::ITEM_TYPE_COMMENT,
                    ':activatedStatus' => 1,
                    ':deletedStatus' => 0,
                    ':moderDeletedStatus' => 0,
                    ':truncatedStatus' => 0,
                )
            ),
            'community' => array(
                self::BELONGS_TO,
                'Community',
                'item_id',
                'joinType' => 'inner join',
                'condition' => '`t`.item_type = :item_type',
                'scopes' => array('activatedStatus', 'deletedStatus', 'moderDeletedStatus', 'truncatedStatus'),
                'params' => array(
                    ':item_type' => ItemTypes::ITEM_TYPE_COMMUNITY,
                    ':activatedStatus' => 1,
                    ':deletedStatus' => 0,
                    ':moderDeletedStatus' => 0,
                    ':truncatedStatus' => 0,
                )
            ),
        );
    }

    /**
     * @return array
     */
    public function rules() {
        return array(
            array('user_id, item_id, user_owner_id, item_type, create_datetime', 'required'),
            array('user_id, item_id, user_owner_id, item_type, status', 'numerical', 'integerOnly'=>true),
            array('status', 'default', 'setOnEmpty' => true, 'value' => null),
            array('id, user_id, item_id, user_owner_id, item_type, create_datetime, status', 'safe', 'on'=>'search'),
            array('user_moder_id, moder_reason', 'required', 'on' => 'moder'),
        );
    }

    /**
     * @return array
     */
    public function attributeLabels() {
        return array(
            'id' => Yii::t('app', 'ID'),
            'user_id' => Yii::t('app', 'User'),
            'item_id' => Yii::t('app', 'Item'),
            'user_owner_id' => Yii::t('app', 'User Owner'),
            'item_type' => Yii::t('app', 'Item Type'),
            'create_datetime' => Yii::t('app', 'Create Datetime'),
            'status' => Yii::t('app', 'Status'),
            'user_moder_id' => Yii::t('app', 'Модератор'),
            'moder_reason' => Yii::t('app', 'Комментарий'),
        );
    }

    /**
     * @return array
     */
    public function scopes()
    {
        $t = $this->getTableAlias(false, false);
        return array(
            'open' => array(
                'condition' => $t.'.status = :'.$t.'_status',
                'params' => array(':'.$t.'_status' => self::STATUS_PENDING)
            )
        );
    }

    /**
     * @return CActiveDataProvider
     */
    public function search() {
        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('user_id', $this->user_id);
        $criteria->compare('item_id', $this->item_id);
        $criteria->compare('user_owner_id', $this->user_owner_id);
        $criteria->compare('item_type', $this->item_type);
        $criteria->compare('create_datetime', $this->create_datetime, true);
        $criteria->compare('status', $this->status);
        $criteria->with = array(
            'sender', 'owner'
        );

        $dependency = new CDbCacheDependency('select max(update_datetime) from {{report}}');
        $dependency->reuseDependentData = true;

        return new CActiveDataProvider(self::model()->cache(Yii::app()->paramsWrap->cache->report, $dependency, 2), array(
            'criteria' => $criteria,
        ));
    }

    /**
     * @param CModelEvent $event
     * @return boolean
     */
    public function onBeforeAcceptReport($event)
    {
        $this->raiseEvent('onBeforeAcceptReport', $event);
        return $event->isValid;
    }

    /**
     * @param CModelEvent $event
     * @return boolean
     */
    public function onAcceptReport($event)
    {
        $this->raiseEvent('onAcceptReport', $event);
        return $event->isValid;
    }

    /**
     * @param CModelEvent $event
     * @return boolean
     */
    public function onAfterAcceptReport($event)
    {
        $this->raiseEvent('onAfterAcceptReport', $event);
        return $event->isValid;
    }

    /**
     * @param bool $validate
     * @param null $params
     * @return bool
     */
    public function acceptReport($validate = true, $params = null)
    {
        /** @var CDbTransaction $t */
        $t = Yii::app()->db->beginTransaction();
        $error = false;
        try {
            $this->status = self::STATUS_DONE;
            if(!$this->save($validate))
                $error = true;
            $params = CMap::mergeArray($params, array(
                'report' => true,
                'moder_reason' => $this->moder_reason,
                'report_id' => $this->id,
            ));
            $event = new CModelEvent($this, $params);
            if($this->hasEventHandler('onBeforeAcceptReport') && false === $this->onBeforeAcceptReport($event))
                $error = true;
            if($this->hasEventHandler('onAcceptReport') && false === $this->onAcceptReport($event))
                $error = true;
            if($this->hasEventHandler('onAfterAcceptReport') && false === $this->onAfterAcceptReport($event))
                $error = true;

            if(!$error) {
                $t->commit();
                return true;
            } else {
                $t->rollback();
                return false;
            }
        } catch(Exception $ex) {
            $t->rollback();
            MyException::log($ex);
            return false;
        }
    }
    /** @var array  */
    private static $_stringTypes = array(
        ItemTypes::ITEM_TYPE_IMAGE    => 'image',
        ItemTypes::ITEM_TYPE_POST     => 'post',
        ItemTypes::ITEM_TYPE_VIDEO    => 'video',
        ItemTypes::ITEM_TYPE_COMMENT  => 'comment'
    );

    /**
     * @param $type
     * @return mixed
     */
    public static function getStringType($type)
    {
        return self::$_stringTypes[$type];
    }
}