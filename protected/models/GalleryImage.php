<?php

/**
 * Class GalleryImage
 *
 * Events
 * @property array $onBefore
 * @property array onSave
 * @property array $onAfter
 * @property array $onDelete
 *
 * Params
 * @property string $update_datetime
 * @property integer $comment_count
 * @property integer is_reported
 * @property integer deleted_trunc
 * @property integer user_update_datetime
 * @property integer community_id
 * @property integer is_community
 * @property integer user_deleted_id
 * @property string community_alias
 *
 * Relations
 * @property GalleryAlbumImage $album
 * @property CommentItem[] $commentCount
 * @property User $user
 * @property User $userImage
 * @property RatingItem $canRate
 * @property Report $report
 * @property ModerLog $moderLog
 * @property UserProfile $userProfile
 * @property SubscribeDebate $canSubscribe
 *
 * Behaviors
 * @property RightsBehavior $rights
 *
 * Scopes
 * @method GalleryImage own()
 * @method GalleryImage incomplete()
 * @method GalleryImage completed()
 * @method GalleryImage activatedStatus()
 * @method GalleryImage deletedStatus()
 * @method GalleryImage moderDeletedStatus()
 * @method GalleryImage canComment()
 * @method GalleryImage public()
 *
 * Behaviors methods
 * @method TagsBehavior parseEditor()
 *
 * @package application.gallery.models
 */
class GalleryImage extends BaseGalleryImage
{
    /**
     * @param string $className
     * @return GalleryImage
     */
    public static function model($className=__CLASS__) {
		return parent::model($className);
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
            'incomplete' => array(
                'condition' => $t.'.is_completed = 0'
            ),
            'completed' => array(
                'condition' => $t.'.is_completed = 1'
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
            'canComment' => array(
                'condition' => $t.'.is_comment = :is_comment',
                'params' => array(':is_comment' => 1)
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

    /**
     * @return array
     */
    public function relations() {
        return array(
            'userProfile' => array(
                self::HAS_ONE,
                'UserProfile',
                array('user_id' => 'user_id'),
                'joinType' => 'inner join'
            ),
            'report' => array(
                self::HAS_ONE,
                'Report',
                'item_id',
            ),
            'album' => array(
                self::BELONGS_TO,
                'GalleryAlbumImage',
                'album_id',
                'joinType' => 'inner join'
            ),
            'user' => array(
                self::BELONGS_TO,
                'User',
                'user_id',
                'joinType' => 'inner join'
            ),
            'userImage' => array(
                self::BELONGS_TO,
                'User',
                'user_id',
            ),
            'canRate' => array(
                self::HAS_ONE,
                'RatingItemImage',
                'item_id',
                'scopes' => array('own')
            ),
            'canSubscribe' => array(
                self::HAS_ONE,
                'SubscribeDebateImage',
                'item_id',
                'scopes' => array('own')
            ),
            'moderLog' => array(
                self::HAS_ONE,
                'ModerLog',
                'item_id',
                'condition' => 'item_type = :item_type',
                'params' => array(':item_type' => ItemTypes::ITEM_TYPE_IMAGE)
            ),
            'info' => array(
                self::HAS_ONE,
                'ItemInfoImage',
                'item_id',
                'joinType' => 'inner join'
            )
        );
    }
    /** @var int  */
    public $view_role = 1;

    /**
     * @return array
     */
    public function behaviors() {
        return CMap::mergeArray(
            parent::behaviors(),
            array(
                'rights' => array(
                    'class' => 'application.behaviors.models.RightsBehavior'
                ),
            )
        );
    }

    /**
     * @return array
     */
    public function rules() {
        return array(
            array('album_id, user_id, file_name, file_ext, create_datetime', 'required'),
            array('album_id, user_id, is_comment, is_activated, is_deleted, view_role', 'numerical', 'integerOnly'=>true),
            array('title, file_ext', 'length', 'max'=>70),
            array('file_name', 'length', 'max'=>50),
            array('description', 'safe'),
            array('user_id, view_role', 'unsafe'),
            array('description, is_comment, is_activated, is_deleted, view_role', 'default', 'setOnEmpty' => true, 'value' => null),
            array('id, album_id, user_id, title, description, file_name, file_ext, is_comment, is_activated, is_deleted, create_datetime, view_role', 'safe', 'on'=>'search'),
            //array('title, description, file_name, file_ext, is_activated, is_deleted', 'unsafe', 'on' => 'cantEdit'),
            array('file_name, file_ext, is_activated, is_deleted', 'unsafe', 'on' => 'cantEdit'),
        );
    }

    public static function getExt()
    {
        return array('jpg', 'jpeg', 'gif', 'png');
    }

    /**
     * @param string $type
     * @param null|ItemInfo $infoModel
     * @param integer $userOwner
     * @return string
     */
    public function getImageUrl($type = 'thumbs', $infoModel = null, $userOwner = null)
    {
        if(null !== $infoModel) {
            $this->user_id = $userOwner;
            $this->album_id = $infoModel->album_id;
            $this->file_name = $infoModel->file_name;
            $this->file_ext = $infoModel->file_ext;
        }

        if($type !== false)
            return Yii::app()->theme->uploadGalleryLink.'/'.$this->user_id.'/'.$type.'/'.$this->file_name.'.'.$this->file_ext;
        else
            return Yii::app()->theme->uploadGalleryLink.'/'.$this->user_id.'/origin/'.$this->file_name.'.'.$this->file_ext;
    }

    public function getImage($type = 'thumbs')
    {
        return Yii::app()->theme->uploadGallery.'/'.$type.'/'.$this->file_name.'.'.$this->file_ext;
    }

    public function getBaseUrl()
    {
        return Yii::app()->theme->uploadGallery.'/'.$this->user_id;
    }

    /**
     * @return array
     */
    public function attributeLabels() {
        return array(
            'id' => Yii::t('app', 'ID'),
            'album_id' => 'Альбом',
            'user_id' => null,
            'title' => Yii::t('app', 'Название'),
            'description' => Yii::t('app', 'Описание'),
            'file_name' => Yii::t('app', 'File Name'),
            'file_ext' => Yii::t('app', 'File Ext'),
            'is_comment' => Yii::t('app', 'Вкл. комментарии'),
            'is_activated' => Yii::t('app', 'Is Activated'),
            'is_deleted' => Yii::t('app', 'Is Deleted'),
            'create_datetime' => Yii::t('app', 'Create Datetime'),
            'view_role' => Yii::t('app', 'Доступность'),
            'commentImages' => null,
            'eventImage' => null,
            'eventImageComment' => null,
            'album' => null,
            'user' => null,
        );
    }

    public function getRateList($limit = 8) {
        $id = 'image_rating_list_'.$this->id.'_'.$this->rating;
        $returned = Yii::app()->cache->get($id);
        if($returned === false) {
            $returned = array(
                'items' => array(),
                'count' => 0
            );
            $criteria = new CDbCriteria();
            $criteria->addCondition('`t`.item_id = :item_id');
            $criteria->params = array(':item_id' => $this->id);
            $criteria->with = array('user');
            $criteria->order = 'create_datetime desc';
            $returned['count'] = RatingItemImage::model()->count($criteria);

            $criteria->limit = $limit;
            /** @var RatingItemImage[] $Ratings */
            $Ratings = RatingItemImage::model()->findAll($criteria);
            foreach($Ratings as $model)
                $returned['items'][] = $model->user->login;

            Yii::app()->cache->set($id, $returned, 10000);
        }
        $string = '';
        foreach($returned['items'] as $login)
            $string .= $login.'<br>';
        if($returned['count'] > $limit)
            $string .= 'и еще ('.($returned['count']-$limit).')';
        return $string;
    }

    /**
     * @return string
     */
    public function canRate()
    {
        if($this->user_id == Yii::app()->user->id || isset($this->canRate) || Yii::app()->user->isGuest)
            return '<span class="icon" id="like"></span>';
        else
            return CHtml::link(
                '<i class="icon" id="like"></i>',
                Yii::app()->createUrl('/rating/image/add', array('item_id' => $this->id)),
                array('class' => 'addLike')
            );
    }

    /**
     * @param bool $link
     * @return string
     */
    public function canSubscribe($link = true)
    {
        if($this->user_id != Yii::app()->user->id && !Yii::app()->user->isGuest) {
            if(!$link || isset($this->canSubscribe))
                return '<i class="icon" title="Вы уже подписаны" id="subscribeDebate"></i>';
            else
                return CHtml::link(
                    '<span class="icon" id="subscribeDebate" title="Подписаться"></span>',
                    Yii::app()->createUrl('/subscribe/request/image', array('item_id' => $this->id)),
                    array('class' => 'addSubscribe')
                );
        }
    }

    /**
     * @return string
     */
    public function drawSubDescriptionsTextDeleted()
    {
        $returned = '';
        if(Yii::app()->user->isModer() && $this->user_id !== Yii::app()->user->id) {
            switch ($this->view_role) {
                case Access::VIEW_ROLE_FRIEND:
                    $returned .= CHtml::openTag('div', array('class' => 'dark_block', 'style' => 'margin-top: 10px;color: red;'));
                    $returned .= 'Фотогиафия доступна только друзьям';
                    $returned .= CHtml::closeTag('div');
                    break;
                case Access::VIEW_ROLE_ME:
                    $returned .= CHtml::openTag('div', array('class' => 'dark_block', 'style' => 'margin-top: 10px;color: red;'));
                    $returned .= 'Фотогиафия доступна только владельцу';
                    $returned .= CHtml::closeTag('div');
                    break;
            }
        }

        if((Yii::app()->user->id !== $this->user_id && !Yii::app()->user->isModer()) || (!$this->is_moder_deleted && !$this->is_deleted))
            return $returned;

        $returned .= CHtml::openTag('div', array('class' => 'dark_block', 'style' => 'margin-top: 10px;color: red;'));
        if($this->is_moder_deleted) {
            $returned .= 'Фотография удалена модератором '.$this->moderLog->moder->getFullLogin().'<br>
                Причина: '.$this->moderLog->moder_reason.'. ';
            $returned .= CHtml::link(
                'Удалить навсегда',
                Yii::app()->createUrl('/gallery/image/trunc', array('id' => $this->id, 'gameId' => $this->user->game_id)),
                array('confirm' => "Вы уверены, что хотите удалить эту фотографию навсегда?")
            );
        } elseif($this->is_deleted) {
            $returned .= 'Фотография удалена, но вы можете восстановить ее. '
                .CHtml::link('Восстановить', Yii::app()->createUrl('/gallery/image/reset', array('id' => $this->id, 'gameId' => $this->user->game_id)));
            $returned .= ' / ' .CHtml::link(
                'Удалить навсегда',
                Yii::app()->createUrl('/gallery/image/trunc', array('id' => $this->id, 'gameId' => $this->user->game_id)),
                array('confirm' => "Вы уверены, что хотите удалить эту фотографию навсегда?")
            );
        }
        $returned .= CHtml::closeTag('div');
        return $returned;
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
                if(false === CacheEventItemImage::updateByItemId($this->id)) {
                    $error = true;
                    MyException::logTxt('[Update] GalleryImage -> CacheEventItemImage '.$this->id);
                }
            }

            if(!$error){
                /** Обновляем информацию о заметке */
                $criteria = new CDbCriteria();
                $criteria->addCondition('item_id = :item_id');
                $criteria->params = array(':item_id' => $this->id);

                /** @var ItemInfoImage $ItemInfo */
                $ItemInfo = ItemInfoImage::model()->find($criteria);
                $ItemInfo->album_id = $this->album_id;
                $ItemInfo->image = $this->file_name.'.'.$this->file_ext;
                $ItemInfo->file_name = $this->file_name;
                $ItemInfo->file_ext = $this->file_ext;
                $ItemInfo->title = $this->title;
                $ItemInfo->view_role = $this->view_role;
                $ItemInfo->update_datetime = DateTimeFormat::format();
                if(!$ItemInfo->save()) {
                    $error = true;
                    Yii::app()->message->setErrors('danger', $ItemInfo);
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
                if(false === CacheEventItemImage::updateByItemId($this->id)) {
                    $error = true;
                    MyException::logTxt('[Restore] GalleryImage -> CacheEventItemImage '.$this->id);
                }
            }

            /**
             * Восстанавливаем рейтинг
             */
            if(!$error) {
                $rating = $this->rating;

                if($rating > 0) {
                    /** @var \UserProfile $UserProfile */
                    $UserProfile = \UserProfile::model()->find('user_id = :user_id', array(':user_id' => $this->user_id));
                    $UserProfile->rating += $rating;
                    if(!$UserProfile->save()) {
                        $error = true;
                        Yii::app()->message->setErrors('danger', $UserProfile);
                    }

                    if($this->is_community) {
                        /** @var \Community $Community */
                        $Community = \Community::model()->findByPk($this->community_id);
                        $Community->rating += $rating;
                        if(!$Community->mUpdate()) {
                            $error = true;
                            Yii::app()->message->setErrors('danger', $Community);
                        }
                    }
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
                $Cache = new CacheEventItemImage('create');
                $Cache->item_id = $this->id;
                $Cache->album_id = $this->album_id;
                $Cache->community_id = $this->community_id;
                $Cache->user_id = $this->user_id;
                $Cache->update_datetime = DateTimeFormat::format();
                if(!$Cache->save()) {
                    $error = true;
                    Yii::app()->message->setErrors('danger', $Cache);
                }
            }

            if(!$error) {
                /** Создаем событие */
                $Event = new EventItemImage('create');
                $Event->item_id = $this->id;
                $Event->album_id = $this->album_id;
                $Event->user_id = $this->user_id;
                $Event->create_datetime = DateTimeFormat::format();
                if(!$Event->save()) {
                    $error = true;
                    Yii::app()->message->setErrors('danger', $Event);
                }
            }

            if(!$error) {
                /** Создаем информацию о фотографии */
                $ItemInfo = new ItemInfoImage('create');
                $ItemInfo->item_id = $this->id;
                $ItemInfo->user_owner_id = $this->user_id;
                $ItemInfo->album_id = $this->album_id;
                $ItemInfo->image = $this->file_name.'.'.$this->file_ext;
                $ItemInfo->file_name = $this->file_name;
                $ItemInfo->file_ext = $this->file_ext;
                $ItemInfo->title = $this->title;
                $ItemInfo->view_role = $this->view_role;
                $ItemInfo->community_id = $this->community_id;
                $ItemInfo->community_alias = $this->community_alias;
                $ItemInfo->is_community = $this->is_community;
                $ItemInfo->gameId = Yii::app()->user->getGameId();
                $ItemInfo->create_datetime = $this->create_datetime;
                if(!$ItemInfo->save()) {
                    $error = true;
                    Yii::app()->message->setErrors('danger', $ItemInfo);
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

            var_dump($ex->getMessage());
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

            $this->user_deleted_datetime = DateTimeFormat::format();
            $this->update_datetime = DateTimeFormat::format();
            if(!$this->save($validate, $attributes)) {
                $error = true;
                Yii::app()->message->setErrors('danger', $this);
            }

            if(!$error) {
                /** Обновляем кэш при удалении */
                if(false === CacheEventItemImage::updateByItemId($this->id)) {
                    $error = true;
                    MyException::logTxt('[Delete] GalleryImage -> CacheEventItemImage '.$this->id);
                }
            }

            if(!$error){
                /** Обновляем информацию о фотографии */
                $criteria = new CDbCriteria();
                $criteria->addCondition('item_id = :item_id');
                $criteria->params = array(':item_id' => $this->id);

                /** @var ItemInfoImage $ItemInfo */
                $ItemInfo = ItemInfoImage::model()->find($criteria);
                $ItemInfo->is_deleted = $this->is_deleted;
                $ItemInfo->is_moder_deleted = $this->is_moder_deleted;
                $ItemInfo->deleted_trunc = $this->deleted_trunc;
                $ItemInfo->update_datetime = DateTimeFormat::format();
                $ItemInfo->user_deleted_id = $this->user_deleted_id;
                if(!$ItemInfo->save()) {
                    $error = true;
                    Yii::app()->message->setErrors('danger', $ItemInfo);
                }
            }

            if(!$error) {
                /** Снимаем рейтинг при удалении */
                $rating = $this->rating;

                if($rating > 0) {
                    /** @var UserProfile $UserProfile */
                    $UserProfile = UserProfile::model()->find('user_id = :user_id', array(':user_id' => $this->user_id));
                    $UserProfile->rating -= $rating;
                    if(!$UserProfile->save()) {
                        $error = true;
                        Yii::app()->message->setErrors('danger', $UserProfile);
                    }

                    if($this->is_community) {
                        /** @var \Community $Community */
                        $Community = \Community::model()->findByPk($this->community_id);
                        $Community->rating -= $rating;
                        if(!$Community->mUpdate()) {
                            $error = true;
                            Yii::app()->message->setErrors('danger', $Community);
                        }
                    }
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

    public $ratingSum = 0;
    public static function getRatingByAlbum($albumId)
    {
        $criteria = new CDbCriteria();
        $criteria->select = 'sum(rating) as ratingSum';
        $criteria->scopes = array('deletedStatus', 'moderDeletedStatus', 'truncatedStatus');
        $criteria->addCondition('album_id = :album_id');
        $criteria->params = array(':album_id' => $albumId, ':deletedStatus' => 0, ':moderDeletedStatus' => 0, ':truncatedStatus' => 0);

        /** @var GalleryImage $model */
        $model = self::model()->find($criteria);
        if($model)
            return $model->ratingSum;
        else
            return 0;
    }

    public function getPreview()
    {
        return Yii::app()->getController()->renderPartial(
            'webroot.themes.'.Yii::app()->theme->name.'.views.modules.gallery.common.template.image',
            array(
                'model' => $this
            ), true, false
        );
    }
}