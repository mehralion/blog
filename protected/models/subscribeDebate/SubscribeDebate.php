<?php

Yii::import('application.models._base.BaseSubscribeDebate');
/**
 * Class SubscribeDebate
 *
 * Relations
 * @property CommentItem[] comment
 * @property User owner
 * @property ItemInfo info
 *
 * Params
 * @property integer is_deleted
 * @property string update_datetime
 */
class SubscribeDebate extends BaseSubscribeDebate
{
    /**
     * @param string $className
     * @return SubscribeDebate
     */
    public static function model($className=__CLASS__) {
		return parent::model($className);
	}

    public function relations() {
        return array(
            'commentOrder' => array(
                self::HAS_ONE,
                'CommentItem',
                array('item_id' => 'item_id', 'item_type' => 'item_type'),
                'joinType' => 'inner join',
                'scopes' => array(
                    'activatedStatus',
                    'deletedStatus',
                    'moderDeletedStatus',
                    'truncatedStatus',
                ),
                'params' => array(
                    ':activatedStatus' => 1,
                    ':deletedStatus' => 0,
                    ':moderDeletedStatus' => 0,
                    ':truncatedStatus' => 0,
                ),
            ),
            'comment' => array(
                self::HAS_MANY,
                'CommentItem',
                array('item_id' => 'item_id', 'item_type' => 'item_type'),
                'limit' => 10,
                'order' => 'comment.create_datetime desc',
                'scopes' => array(
                    'activatedStatus',
                    'deletedStatus',
                    'moderDeletedStatus',
                    'truncatedStatus',
                ),
                'params' => array(
                    ':activatedStatus' => 1,
                    ':deletedStatus' => 0,
                    ':moderDeletedStatus' => 0,
                    ':truncatedStatus' => 0,
                ),
                'with' => array('info', 'canRate', 'user'),
            ),
            'owner'  => array(self::HAS_ONE, 'User', array('id' => 'owner_item_user_id'), 'joinType' => 'inner join'),
            'info'   => array(self::HAS_ONE, 'ItemInfo', array('item_id' => 'item_id', 'item_type' => 'item_type'), 'joinType' => 'inner join')
        );
    }

    public function scopes()
    {
        $t = $this->getTableAlias(false, false);
        return array(
            'own' => array(
                'condition' => $t.'.subscribe_user_id = :'.$t.'_subscribe_user_id',
                'params' => array(':'.$t.'_subscribe_user_id' => Yii::app()->user->id
                )
            ),
            'deletedStatus' => array(
                'condition' => $t.'.is_deleted = :deletedStatus',
            )
        );
    }

    private $_types = array(
        ItemTypes::ITEM_TYPE_AUDIO_ALBUM => 'Аудиозапись',
        ItemTypes::ITEM_TYPE_VIDEO => 'Видеозапись',
        ItemTypes::ITEM_TYPE_IMAGE => 'Фотография',
        ItemTypes::ITEM_TYPE_POST => 'Заметка',
    );

    public static function getItemTypes()
    {
        return self::model()->_types;
    }

    public function getTitle()
    {
        $title = $this->item_title!=''?$this->item_title:$this->_types[$this->item_type];
        $link = '';
        switch($this->item_type) {
            case ItemTypes::ITEM_TYPE_AUDIO_ALBUM:
                $link = CHtml::link($title, Yii::app()->createUrl('/gallery/album/show_audio', array('album_id' => $this->item_id)), array('target'=>'_blank'));
                break;
            case ItemTypes::ITEM_TYPE_POST:
                $link = CHtml::link($title, Yii::app()->createUrl('/post/index/show', array('id' => $this->item_id)), array('target'=>'_blank'));
                break;
            case ItemTypes::ITEM_TYPE_IMAGE:
                $link = CHtml::link($title, Yii::app()->createUrl('/gallery/image/show', array('id' => $this->item_id)), array('target'=>'_blank'));
                break;
            case ItemTypes::ITEM_TYPE_VIDEO:
                $link = CHtml::link($title, Yii::app()->createUrl('/gallery/video/show', array('id' => $this->item_id)), array('target'=>'_blank'));
                break;

        }

        return $link;
    }

    public function search() {
        $criteria = new CDbCriteria;

        $criteria->compare('`t`.subscribe_user_id', $this->subscribe_user_id);
        $criteria->compare('`t`.item_id', $this->item_id);
        $criteria->compare('`t`.item_type', $this->item_type);
        $criteria->compare('`t`.item_title', $this->item_title, true);
        $criteria->compare('`t`.owner_item_user_id', $this->owner_item_user_id);
        $criteria->compare('`t`.create_datetime', $this->create_datetime, true);
        $criteria->compare('`t`.is_view', $this->is_view);
        $criteria->compare('`t`.view_datetime', $this->view_datetime, true);
        $criteria->compare('`t`.is_deleted', $this->is_deleted);
        $criteria->with = array(
            'info' => array(
                'scopes' => array('moderDeletedStatus', 'truncatedStatus', 'deletedStatus'),
                'params' => array(':moderDeletedStatus' => 0, ':truncatedStatus' => 0, ':deletedStatus' => 0)
            )
        );

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }
}