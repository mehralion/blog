<?php

Yii::import('application.models._base.BaseGalleryVideo');
/**
 * Class GalleryVideo
 *
 * Events
 * @property array $onBefore
 * @property array onSave
 * @property array $onAfter
 * @property array $onDelete
 *
 * Params
 * @property integer comment_count
 * @property integer is_reported
 * @property integer album_id
 * @property integer deleted_trunc
 * @property integer user_update_datetime
 * @property integer community_id
 * @property integer is_community
 * @property integer user_deleted_id
 * @property string community_alias
 *
 * Relations
 * @property User $user
 * @property User $userVideo
 * @property CommentItem[] $commentCount
 * @property ModerLog $moderLog
 * @property RatingItem $canRate
 * @property GalleryAlbumVideo $album
 * @property UserProfile $userProfile
 * @property SubscribeDebate $canSubscribe
 *
 * Behaviors
 * @property RightsBehavior $rights
 *
 * Scopes
 * @method GalleryVideo own()
 * @method GalleryVideo public()
 * @method GalleryVideo activatedStatus()
 * @method GalleryVideo deletedStatus()
 * @method GalleryVideo moderDeletedStatus()
 * @method GalleryVideo canComment()
 *
 * Behaviors methods
 * @method TagsBehavior parseEditor()
 *
 * @package application.gallery.models
 */
class GalleryVideo extends BaseGalleryVideo
{
    const STATUS_DELETED = 1;
    const STATUS_ACTIVATED = 1;

    const TYPE_YOUTUBE = 1;
    const TYPE_VK      = 2;
    const TYPE_COUB    = 3;

    const COMMENT_ENABLE = 1;

    private $_videoTypes = array(
        self::TYPE_YOUTUBE => 'YouTube',
        self::TYPE_VK => 'Vkontakte',
        self::TYPE_COUB => 'Coub.com',
    );

    public $view_role = 1;

    /**
     * @param string $className
     * @return GalleryVideo
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
                'params' => array(
                    ':'.$t.'_u_id' => Yii::app()->user->id
                )
            ),
            'canComment' => array(
                'condition' => $t.'.is_comment = :'.$t.'_is_comment',
                'params' => array(':'.$t.'_is_comment' => self::COMMENT_ENABLE)
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
                'params' => array(':'.$t.'_v_role' => 1)
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
    public function relations() {
        return array(
            'userProfile' => array(
                self::HAS_ONE,
                'UserProfile',
                array('user_id' => 'user_id'),
                'joinType' => 'inner join'
            ),
            'album' => array(
                self::BELONGS_TO,
                'GalleryAlbumVideo',
                'album_id',
                'joinType' => 'inner join'
            ),
            'user' => array(
                self::BELONGS_TO,
                'User',
                'user_id',
                'joinType' => 'inner join'
            ),
            'userVideo' => array(
                self::BELONGS_TO,
                'User',
                'user_id',
            ),
            'commentCount'=>array(
                self::STAT,
                'CommentItem',
                'item_id',
                'condition' => 'is_deleted = 0 and is_moder_deleted = 0 and item_type = :item_type',
                'params' => array(':item_type' => ItemTypes::ITEM_TYPE_VIDEO)
            ),
            'canRate' => array(
                self::HAS_ONE,
                'RatingItemVideo',
                'item_id',
                'scopes' => array('own')
            ),
            'canSubscribe' => array(
                self::HAS_ONE,
                'SubscribeDebateVideo',
                'item_id',
                'scopes' => array('own')
            ),
            'moderLog' => array(
                self::HAS_ONE,
                'ModerLog',
                'item_id',
                'condition' => 'item_type = :item_type',
                'params' => array(':item_type' => ItemTypes::ITEM_TYPE_VIDEO)
            ),
            'info' => array(
                self::HAS_ONE,
                'ItemInfoVideo',
                'item_id',
                'joinType' => 'inner join'
            )
        );
    }

    /**
     * @return array
     */
    public function rules() {
        return array(
            array('user_id, title, album_id', 'required'),
            array('user_id, video_type, view_role', 'numerical', 'integerOnly'=>true),
            array('title, video_id', 'length', 'max'=>255),
            array('description', 'safe'),
            array('user_id, view_role, album_id', 'unsafe'),
            array('description, link, video_id, video_type, view_role', 'default', 'setOnEmpty' => true, 'value' => null),
            array('id, user_id, link, video_id, title, description, video_type, view_role', 'safe', 'on'=>'search'),
            array('link, video_id, video_type', 'unsafe', 'on' => 'cantEdit'),
        );
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
            'video_type' => Yii::t('app', 'Видео хостинг'),
            'is_comment' => Yii::t('app', 'Комментарии разрешены'),
            'link' => Yii::t('app', 'Ссылка'),
            'video_id' => Yii::t('app', 'id видео'),
            'view_role' => Yii::t('app', 'Доступность'),
            'commentVideos' => null,
            'eventVideo' => null,
            'eventVideoComment' => null,
            'user' => null,
        );
    }

    /**
     * @return bool
     */
    public function beforeValidate()
    {
        /**
         * http://youtu.be/dQw4w9WgXcQ ...
         * http://www.youtube.com/embed/dQw4w9WgXcQ ...
         * http://www.youtube.com/watch?v=dQw4w9WgXcQ ...
         * http://www.youtube.com/?v=dQw4w9WgXcQ ...
         * http://www.youtube.com/v/dQw4w9WgXcQ ...
         * http://www.youtube.com/e/dQw4w9WgXcQ ...
         * http://www.youtube.com/user/username#p/u/11/dQw4w9WgXcQ ...
         * http://www.youtube.com/sandalsResorts#p/c/54B8C800269D7C1B/0/dQw4w9WgXcQ ...
         * http://www.youtube.com/watch?feature=player_embedded&v=dQw4w9WgXcQ ...
         * http://www.youtube.com/?feature=player_embedded&v=dQw4w9WgXcQ ...
         */
        if($this->isNewRecord) {
            if ($this->video_type == self::TYPE_YOUTUBE && preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $this->link, $match))
                $this->video_id = $match[1];
            elseif($this->video_type == self::TYPE_VK && preg_match('/vk.com/ui', $this->link, $match))
                $this->video_id = null;
            elseif($this->video_type == self::TYPE_COUB && preg_match('/coub\.com\/view\/([^\/]+)/ui', $this->link, $match))
                $this->video_id = $match[1];
            else
                $this->addError('link', 'Некорректная ссылка');
        }

        if($this->isNewRecord)
            $this->user_id = Yii::app()->user->id;
        return parent::beforeValidate();
    }

    /**
     * @return array
     */
    public function getVideoTypes()
    {
        return $this->_videoTypes;
    }

    /**
     * @param string $type
     * @return string
     */
    public function getImageUrl($type = 'small')
    {
        $image = '';
        switch ($this->video_type) {
            case self::TYPE_YOUTUBE:
                $image = 'https://img.youtube.com/vi/'.$this->video_id.'/2.jpg';
                break;
            case self::TYPE_VK:
                $image = Yii::app()->theme->baseUrl.'/images/video/vk.jpg';
                break;
            case self::TYPE_COUB:
                $image = Yii::app()->theme->baseUrl.'/images/video/vk.jpg';
                break;
        }
        return $image;
    }

    /**
     * @return string
     */
    public function getVideoCode()
    {
        if(!isset($this->_videoTypes[$this->video_type]))
            return '';
        return Yii::app()->getController()->renderPartial(
            'webroot.themes.'.Yii::app()->theme->name.'.views.modules.gallery.common.template.'.$this->video_type.'_type',
            array(
                'model' => $this
            ), true, false
        );
    }

    public function getPreview()
    {
        return Yii::app()->getController()->renderPartial(
            'webroot.themes.'.Yii::app()->theme->name.'.views.modules.gallery.common.template.video',
            array(
                'model' => $this,
                'code' => $this->getVideoCode()
            ), true, false
        );
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
                Yii::app()->createUrl('/rating/video/add', array('item_id' => $this->id)),
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
                    Yii::app()->createUrl('/subscribe/request/video', array('item_id' => $this->id)),
                    array('class' => 'addSubscribe')
                );
        }
    }

    public function getRateList($limit = 8) {
        $id = 'video_rating_list_'.$this->id.'_'.$this->rating;
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
            $returned['count'] = RatingItemVideo::model()->count($criteria);

            $criteria->limit = $limit;
            /** @var RatingItemVideo[] $Ratings */
            $Ratings = RatingItemVideo::model()->findAll($criteria);
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
    public function drawSubDescriptionsTextDeleted()
    {
        $returned = '';
        if(Yii::app()->user->isModer() && $this->user_id !== Yii::app()->user->id) {
            switch ($this->view_role) {
                case Access::VIEW_ROLE_FRIEND:
                    $returned .= CHtml::openTag('div', array('class' => 'dark_block', 'style' => 'margin-top: 10px;color: red;'));
                    $returned .= 'Видеозапись доступна только друзьям';
                    $returned .= CHtml::closeTag('div');
                    break;
                case Access::VIEW_ROLE_ME:
                    $returned .= CHtml::openTag('div', array('class' => 'dark_block', 'style' => 'margin-top: 10px;color: red;'));
                    $returned .= 'Видеозапись доступна только владельцу';
                    $returned .= CHtml::closeTag('div');
                    break;
            }
        }

        if((Yii::app()->user->id !== $this->user_id && !Yii::app()->user->isModer()) || (!$this->is_moder_deleted && !$this->is_deleted))
            return $returned;

        $returned .= CHtml::openTag('div', array('class' => 'dark_block', 'style' => 'margin-top: 10px;color: red;'));
        if($this->is_moder_deleted) {
            $returned .= 'Видео удалено модератором '.$this->moderLog->moder->getFullLogin().'<br>
                Причина: '.$this->moderLog->moder_reason;

            $returned .= CHtml::link(
                'Удалить навсегда',
                Yii::app()->createUrl('/gallery/video/trunc', array('id' => $this->id, 'gameId' => $this->user->game_id)),
                array('confirm' => "Вы уверены, что хотите удалить эту видеозапись навсегда?")
            );
        } elseif($this->is_deleted) {
            $returned .= 'Видео удалено, но вы можете восстановить ее. '
                .CHtml::link('Восстановить', Yii::app()->createUrl('/gallery/video/reset', array('id' => $this->id, 'gameId' => $this->user->game_id)));
            $returned .= ' / ' .CHtml::link(
                    'Удалить навсегда',
                    Yii::app()->createUrl('/gallery/video/trunc', array('id' => $this->id, 'gameId' => $this->user->game_id)),
                    array('confirm' => "Вы уверены, что хотите удалить эту видеозапись навсегда?")
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
                if(false === CacheEventItemVideo::updateByItemId($this->id)) {
                    $error = true;
                    MyException::logTxt('[Update] GalleryVideo -> CacheEventItemVideo '.$this->id);
                }
            }

            if(!$error){
                /** Обновляем информацию о видеозаписи */
                $criteria = new CDbCriteria();
                $criteria->addCondition('item_id = :item_id');
                $criteria->params = array(':item_id' => $this->id);

                /** @var ItemInfoVideo $ItemInfo */
                $ItemInfo = ItemInfoVideo::model()->find($criteria);
                $ItemInfo->album_id = $this->album_id;
                $ItemInfo->title = $this->title;
                $ItemInfo->video_type = $this->video_type;
                $ItemInfo->file_name = $this->video_id;
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
                if(false === CacheEventItemVideo::updateByItemId($this->id)) {
                    $error = true;
                    MyException::logTxt('[Restore] GalleryVideo -> CacheEventItemVideo '.$this->id);
                }
            }

            /** Восстанавливаем рейтинг */
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
                $Cache = new CacheEventItemVideo('create');
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
                $Event = new EventItemVideo('create');
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
                /** Создаем информацию о видеозаписи */
                $ItemInfo = new ItemInfoVideo('create');
                $ItemInfo->item_id = $this->id;
                $ItemInfo->user_owner_id = $this->user_id;
                $ItemInfo->album_id = $this->album_id;
                $ItemInfo->file_name = $this->video_id;
                $ItemInfo->video_type = $this->video_type;
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
                if(false === CacheEventItemVideo::updateByItemId($this->id)) {
                    $error = true;
                    MyException::logTxt('[Delete] GalleryVideo -> CacheEventItemVideo '.$this->id);
                }
            }

            if(!$error){
                /** Обновляем информацию о видеозаписи */
                $criteria = new CDbCriteria();
                $criteria->addCondition('item_id = :item_id');
                $criteria->params = array(':item_id' => $this->id);

                /** @var ItemInfoVideo $ItemInfo */
                $ItemInfo = ItemInfoVideo::model()->find($criteria);
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

        /** @var GalleryVideo $model */
        $model = self::model()->find($criteria);
        if($model)
            return $model->ratingSum;
        else
            return 0;
    }
}