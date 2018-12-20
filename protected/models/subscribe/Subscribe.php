<?php

Yii::import('application.models._base.BaseSubscribe');

/**
 * Class SubscribeUser
 *
 * @property integer subscribe_type
 *
 * @property Community ownerCommunity
 */
class Subscribe extends BaseSubscribe
{
    /**
     * @param string $className
     * @return SubscribeUser
     */
    public static function model($className=__CLASS__) {
		return parent::model($className);
	}

    public function beforeValidate()
    {
        $this->subscribe_type = ItemTypes::SUBSCRIBE_USER;
        return parent::beforeValidate();
    }

    public function attributeLabels() {
        return array(
            'subscribe_user_id' => null,
            'item_id' => null,
            'post' => Yii::t('app', 'Заметки'),
            'image' => Yii::t('app', 'Фотографии'),
            'video' => Yii::t('app', 'Видеозаписи'),
            'audio' => Yii::t('app', 'Аудиозаписи'),
            'comment' => Yii::t('app', 'Комментарии'),
            'update_datetime' => Yii::t('app', 'Update Datetime'),
            'create_datetime' => Yii::t('app', 'Create Datetime'),
            'ownerUser' => null,
            'subscribeUser' => null,
        );
    }

    public function scopes()
    {
        $t = $this->getTableAlias(false, false);
        return array(
            'own' => array(
                'condition' => $t.'.subscribe_user_id = :'.$t.'_user_id',
                'params' => array(
                    ':'.$t.'_user_id' => Yii::app()->user->id
                )
            ),
            'any' => array(
                'condition' => '`'.$t.'`.post = 1 or `'.$t.'`.image = 1 or `'.$t.'`.video = 1 or `'.$t.'`.audio = 1 or `'.$t.'`.comment = 1'
            )
        );
    }

    public function relations() {
        return array(
            'ownerUser' => array(
                self::BELONGS_TO,
                'User',
                'item_id',
                'joinType' => 'inner join'
            ),
            'subscribeUser' => array(
                self::BELONGS_TO,
                'User',
                'subscribe_user_id',
                'joinType' => 'inner join'
            ),
            'ownerCommunity' => array(
                self::BELONGS_TO,
                'Community',
                'item_id',
                'joinType' => 'inner join'
            ),
        );
    }
}