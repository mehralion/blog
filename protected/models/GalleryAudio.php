<?php

/**
 * Class GalleryAudio
 *
 *
 * Events
 * @property array $onBefore
 * @property array onSave
 * @property array $onAfter
 * @property array $onDelete
 *
 * Relations
 * @property GalleryAlbumAudio $album
 * @property User $user
 *
 * Params
 * @property integer $view_role
 * @property integer deleted_trunc
 * @property string user_update_datetime
 * @property integer is_community
 * @property integer community_id
 * @property integer user_deleted_id
 * @property string community_alias
 */
class GalleryAudio extends BaseGalleryAudio
{
    /**
     * @param string $className
     * @return GalleryAudio
     */
    public static function model($className=__CLASS__) {
		return parent::model($className);
	}

    public function relations() {
        return array(
            'album' => array(
                self::BELONGS_TO,
                'GalleryAlbumAudio',
                'album_id',
                'joinType' => 'inner join'
            ),
            'user' => array(
                self::BELONGS_TO,
                'User',
                'user_id',
                'joinType' => 'inner join'
            ),
            'userAudio' => array(
                self::BELONGS_TO,
                'User',
                'user_id',
            ),
        );
    }

    /**
     * @return array
     */
    public function scopes()
    {
        $t = $this->getTableAlias(false, false);
        return array(
            'own' => array(
                'condition' => $t.'.user_id = :'.$t.'_u_id',
                'params' => array(':'.$t.'_u_id' => Yii::app()->user->id)
            ),
            'activatedStatus' => array(
                'condition' => $t.'.is_activated = :activatedStatus',
            ),
            'deletedStatus' => array(
                'condition' => $t.'.is_deleted = :deletedStatus',
            ),
            'moderDeletedStatus' => array(
                'condition' => $t.'.is_moder_deleted = :moderDeletedStatus',
            ),
            'public' => array(
                'condition' => $t.'.view_role = :'.$t.'_v_role',
                'params' => array(':'.$t.'_v_role' => Access::VIEW_ROLE_ALL)
            ),
            'truncatedStatus' => array(
                'condition' => $t.'.deleted_trunc = :truncatedStatus',
            ),
            'notCommunity' => array(
                'condition' => $t.'.is_community = 0',
            )
        );
    }

    public function attributeLabels() {
        return array(
            'id' => Yii::t('app', 'ID'),
            'album_id' => 'Альбом',
            'user_id' => null,
            'title' => Yii::t('app', 'Title'),
            'seconds' => Yii::t('app', 'Seconds'),
            'item_type' => Yii::t('app', 'Item Type'),
            'link' => Yii::t('app', 'Link'),
            'create_datetime' => Yii::t('app', 'Create Datetime'),
            'update_datetime' => Yii::t('app', 'Update Datetime'),
            'rating' => Yii::t('app', 'Rating'),
            'is_moder_deleted' => Yii::t('app', 'Is Moder Deleted'),
            'is_reported' => Yii::t('app', 'Is Reported'),
            'is_deleted' => Yii::t('app', 'Is Deleted'),
            'is_activated' => Yii::t('app', 'Is Activated'),
            'album' => null,
            'user' => null,
        );
    }

    public function rules() {
        return array(
            array('album_id, user_id, link, create_datetime', 'required'),
            array('album_id, user_id, seconds, item_type, rating, is_moder_deleted, is_reported, is_deleted, is_activated', 'numerical', 'integerOnly'=>true),
            //array('link', 'length', 'max'=>255),
            array('title', 'length', 'max'=>70),
            array('user_id, view_role, is_community, community_id', 'unsafe'),
            //array('link', 'match', 'pattern' => '/\.mp3$/i', 'message' => 'Допустимы только mp3 файлы', 'on' => 'create'),
            array('title, seconds, item_type, rating, is_moder_deleted, is_reported, is_deleted, is_activated', 'default', 'setOnEmpty' => true, 'value' => null),
            array('id, album_id, user_id, title, seconds, item_type, link, create_datetime, update_datetime, rating, is_moder_deleted, is_reported, is_deleted, is_activated', 'safe', 'on'=>'search'),
        );
    }

    /**
     * Удаляем альбом и все аудиозаписи в нем
     */
    public function mUpdate($validate = true, $attributes = null, $withCache = true)
    {
        /** @var CDbTransaction $t */
        $t = null;
        if(null === Yii::app()->db->getCurrentTransaction())
            $t = Yii::app()->db->beginTransaction();

        $error = false;
        try {

            $this->update_datetime = DateTimeFormat::format();
            if(!$this->save($validate, $attributes)) {
                $error = true;
                Yii::app()->message->setErrors('danger', $this);
            }

            if(!$error && $withCache) {
                /** Обновляем кэш */
                if(false === CacheEventItemAudio::updateAllByAlbumId($this->album_id)) {
                    $error = true;
                    MyException::logTxt('[Update] GalleryAudio -> CacheEventItemAudio '.$this->id);
                }
            }

            if(null !== $t) {
                if(!$error)
                    $t->commit();
                else
                    $t->rollback();
            }

            return !$error;

        } catch (Exception $ex) {
            if(null !== $t)
                $t->rollback();
            MyException::log($ex);
            return false;
        }
    }

    public function restore($validate = true, $attributes = null, $withCache = true)
    {
        /** @var CDbTransaction $t */
        $t = null;
        if(null === Yii::app()->db->getCurrentTransaction())
            $t = Yii::app()->db->beginTransaction();

        $error = false;
        try {
            $this->is_deleted = 0;
            $this->is_moder_deleted = 0;
            $this->update_datetime = \DateTimeFormat::format();
            if(!$this->save($validate, $attributes)) {
                $error = true;
                Yii::app()->message->setErrors('danger', $this);
            }

            if(!$error && $withCache) {
                /** Обновляем кэш */
                if(false === CacheEventItemAudio::updateByItemId($this->id)) {
                    $error = true;
                    MyException::logTxt('[Restore] GalleryAudio -> CacheEventItemAudio '.$this->id);
                }
            }

            if(null !== $t) {
                if(!$error)
                    $t->commit();
                else
                    $t->rollback();
            }

            return !$error;

        } catch (Exception $ex) {
            if(null !== $t)
                $t->rollback();
            MyException::log($ex);
            return false;
        }
    }

    public function create($validate = true, $attributes = null)
    {
        /** @var CDbTransaction $t */
        $t = null;
        if(null === Yii::app()->db->getCurrentTransaction())
            $t = Yii::app()->db->beginTransaction();

        $error = false;
        try {

            $this->create_datetime = DateTimeFormat::format();
            if(!$this->save($validate, $attributes)) {
                $error = true;
                Yii::app()->message->setErrors('danger', $this);
            }

            if(!$error) {
                /** Создаем кэш */
                $Cache = new CacheEventItemAudio('create');
                $Cache->item_id = $this->id;
                $Cache->album_id = $this->album_id;
                $Cache->community_id = $this->album->community_id;
                $Cache->user_id = $this->user_id;
                $Cache->update_datetime = DateTimeFormat::format();
                if(!$Cache->save()) {
                    $error = true;
                    Yii::app()->message->setErrors('danger', $Cache);
                }
            }

            if(!$error) {
                /** Создаем событие */
                $Event = new EventItemAudio('create');
                $Event->item_id = $this->id;
                $Event->album_id = $this->album_id;
                $Event->user_id = $this->user_id;
                $Event->create_datetime = DateTimeFormat::format();
                if(!$Event->save()) {
                    $error = true;
                    Yii::app()->message->setErrors('danger', $Event);
                }
            }

            if(null !== $t) {
                if(!$error)
                    $t->commit();
                else
                    $t->rollback();
            }

            return !$error;

        } catch (Exception $ex) {
            if(null !== $t)
                $t->rollback();

            MyException::log($ex);
            return false;
        }
    }

    /**
     * Удаляем альбом и все аудиозаписи в нем
     */
    public function delete($validate = true, $attributes = null)
    {
        /** @var CDbTransaction $t */
        $t = null;
        if(null === Yii::app()->db->getCurrentTransaction())
            $t = Yii::app()->db->beginTransaction();

        $error = false;
        try {

            $this->update_datetime = DateTimeFormat::format();
            if(!$this->save($validate, $attributes)) {
                $error = true;
                Yii::app()->message->setErrors('danger', $this);
            }

            if(!$error) {
                /** Обновляем кэш при удалении */
                if(false === CacheEventItemAudio::updateByItemId($this->id)) {
                    $error = true;
                    MyException::logTxt('[Delete] GalleryAudio -> CacheEventItemAudio '.$this->id);
                }
            }

            if(null !== $t) {
                if(!$error)
                    $t->commit();
                else
                    $t->rollback();
            }

            return !$error;

        } catch (Exception $ex) {
            if(null !== $t)
                $t->rollback();
            MyException::log($ex);
            return false;
        }
    }

    public static function updateAllByAlbumId($attributes, $condition = '', $params = array())
    {
        return self::model()->updateAll($attributes, $condition, $params);
    }
}