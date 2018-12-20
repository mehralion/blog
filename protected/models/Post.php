<?php
Yii::import('ext.yiiext-taggable.ETaggableBehavior');
/**
 * Class Post
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
 * @property integer on_top
 * @property integer deleted_trunc
 * @property string user_update_datetime
 * @property integer is_poll
 * @property integer community_id
 * @property integer is_community
 * @property integer user_deleted_id
 * @property string community_alias
 *
 * Relations
 * @property CommentItem[] $commentCount
 * @property RatingItem $canRate
 * @property SubscribeDebate $canSubscribe
 * @property Report $report
 * @property RatingItem $ratingCount
 * @property User $user
 * @property User $userPost
 * @property ModerLog $moderLog
 * @property UserProfile $userProfile
 * @property Poll $poll
 *
 * Behaviors
 * @property ETaggableBehavior $tags
 * @property RightsBehavior $rights
 *
 * Scopes
 * @method Post own()
 * @method Post canComment()
 * @method Post public()
 * @method Post activatedStatus()
 * @method Post deletedStatus()
 * @method Post moderDeletedStatus()
 *
 * Behaviors methods
 * @method TagsBehavior parseEditor()
 *
 * @package application.post.models
 *
 * @property boolean $admin_text
 * @property string $custom
 */
class Post extends BasePost
{
    public $login;

    /**
     * @param string $className
     * @return Post
     */
    public static function model($className=__CLASS__) {
		return parent::model($className);
	}

    /**
     * @return array
     */
    public function behaviors() {
        return CMap::mergeArray(
            parent::behaviors(),
            array(
                'tags' => array(
                    'class' => 'ext.yiiext-taggable.ETaggableBehavior',
                    // Имя таблицы для хранения тегов
                    'tagTable' => 'tag',
                    // Имя кросс-таблицы, связывающей тег с моделью.
                    // По умолчанию выставляется как Имя_таблицы_моделиTag
                    'tagBindingTable' => 'post_tag',
                    // Имя внешнего ключа модели в кроcc-таблице.
                    // По умолчанию равно имя_таблицы_моделиId
                    'modelTableFk' => 'post_id',
                    // Имя первичного ключа тега
                    'tagTablePk' => 'id',
                    // Имя поля названия тега
                    'tagTableName' => 'title',
                    // Имя поля счетчика тега
                    // Если устанвовлено в null (по умолчанию), то не сохраняется в базе
                    'tagTableCount' => 'count',
                    // ID тега в таблице-связке
                    'tagBindingTableTagId' => 'tag_id',
                    // ID компонента, реализующего кеширование. Если false кеширование не происходит.
                    // По умолчанию ID равен false.
                    //'cacheID' => 'cache',
                    // Создавать несуществующие теги автоматически.
                    // При значении false сохранение выкидывает исключение если добавляемый тег не существует.
                    'createTagsAutomatically' => true,
                ),
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
            'poll' => array(
                self::HAS_ONE,
                'Poll',
                'post_id'
            ),
            'sTag' => array(
                self::HAS_ONE,
                'PostTag',
                'post_id',
                'joinType' => 'inner join',
                'with' => array('tag'),
                'condition' => 'tag.title = :tagname'
            ),
            'userProfile' => array(
                self::HAS_ONE,
                'UserProfile',
                array('user_id' => 'user_id'),
                'joinType' => 'inner join'
            ),
            'canRate' => array(
                self::HAS_ONE,
                'RatingItemPost',
                'item_id',
                'scopes' => array('own')
            ),
            'canSubscribe' => array(
                self::HAS_ONE,
                'SubscribeDebatePost',
                'item_id',
                'scopes' => array('own')
            ),
            'report' => array(
                self::HAS_ONE,
                'Report',
                'item_id',
            ),
            'ratingCount'=>array(
                self::HAS_ONE,
                'RatingItem',
                'item_id',
                'scopes' => array(
                    'post'
                ),
                'select' => 'count(`ratingCount`.item_id) as cnt',
            ),
            'user' => array(
                self::BELONGS_TO,
                'User',
                'user_id',
                'joinType' => 'inner join',
                'together' => true,
            ),
            'userPost' => array(
                self::BELONGS_TO,
                'User',
                'user_id',
            ),
            'moderLog' => array(
                self::HAS_ONE,
                'ModerLog',
                'item_id',
                'condition' => 'item_type = :item_type',
                'params' => array(':item_type' => ItemTypes::ITEM_TYPE_POST)
            ),
            'info' => array(
                self::HAS_ONE,
                'ItemInfoPost',
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
            array('user_id, title, description', 'required', 'on' => 'create, edit, canEdit'),
            array('user_id, is_like, is_comment, is_activated, is_deleted, view_role, is_poll', 'numerical', 'integerOnly'=>true),
            array('title', 'length', 'max'=>90, 'on' => 'create, edit'),
            array('is_like, is_comment, is_activated, is_deleted, view_role', 'default', 'setOnEmpty' => true, 'value' => null),
            array('id, user_id, title, description, post_type, is_like, is_comment, is_activated, is_deleted, create_datetime, update_datetime, rating, view_role, is_moder_deleted, user_deleted_datetime, is_reported, comment_count', 'safe', 'on'=>'search'),
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
            'canComment' => array(
                'condition' => $t.'.is_comment = 1',
            ),
            'public' => array(
                'condition' => $t.'.view_role = :v_role',
                'params' => array(
                    ':v_role' => 1
                )
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
            'truncatedStatus' => array(
                'condition' => $t.'.deleted_trunc = :truncatedStatus',
            ),
            'communityStatus' => array(
                'condition' => $t.'.is_community = :communityStatus',
            ),
            'notCommunity' => array(
                'condition' => $t.'.is_community = 0',
            )
        );
    }

    /**
     * @return array
     */
    public function attributeLabels() {
        return array(
            'id' => Yii::t('app', 'ID'),
            'user_id' => null,
            'title' => Yii::t('app', 'Название'),
            'description' => Yii::t('app', 'Текст'),
            'post_type' => Yii::t('app', 'Post Type'),
            'is_like' => Yii::t('app', 'Лайки'),
            'is_comment' => Yii::t('app', 'Комментарии'),
            'is_activated' => Yii::t('app', 'Is Activated'),
            'is_deleted' => Yii::t('app', 'Is Deleted'),
            'create_datetime' => Yii::t('app', 'Создан'),
            'view_role' => Yii::t('app', 'Доступность'),
            'commentPosts' => null,
            'eventPost' => null,
            'eventPostComment' => null,
            'oproses' => null,
            'user' => null,
            'tags' => null,
            'is_poll' => 'Вкл. опрос'
        );
    }

    /**
     * @param bool $link
     * @return string
     */
    public function canRate($link = true)
    {
        if($this->user_id == Yii::app()->user->id || !$link || Yii::app()->user->isGuest)
            return '<span class="icon" id="like"></span>';
        elseif(isset($this->canRate))
            return '<span class="icon" id="like_tuskl"></span>';
        else
            return CHtml::link(
                '<i class="icon" id="like"></i>',
                Yii::app()->createUrl('/rating/post/add', array('item_id' => $this->id)),
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
                    Yii::app()->createUrl('/subscribe/request/post', array('item_id' => $this->id)),
                    array('class' => 'addSubscribe')
                );
        }
    }

    public function getRateList($limit = 8) {
        $id = 'post_rating_list_'.$this->id.'_'.$this->rating;
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
            $returned['count'] = RatingItemPost::model()->count($criteria);

            $criteria->limit = $limit;
            /** @var RatingItemPost[] $Ratings */
            $Ratings = RatingItemPost::model()->findAll($criteria);
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
        if(Yii::app()->user->isModer() || $this->user_id == Yii::app()->user->id) {
            switch ($this->view_role) {
                case Access::VIEW_ROLE_FRIEND:
                    $returned .= CHtml::openTag('div', array('class' => 'dark_block', 'style' => 'margin-top: 10px;color: red;'));
                    $returned .= 'Заметка доступна только друзьям';
                    $returned .= CHtml::closeTag('div');
                    break;
                case Access::VIEW_ROLE_ME:
                    $returned .= CHtml::openTag('div', array('class' => 'dark_block', 'style' => 'margin-top: 10px;color: red;'));
                    $returned .= 'Заметка доступна только владельцу';
                    $returned .= CHtml::closeTag('div');
                    break;
                case Access::VIEW_ROLE_COMMUNITY:
                    $returned .= CHtml::openTag('div', array('class' => 'dark_block', 'style' => 'margin-top: 10px;color: red;'));
                    $returned .= 'Заметка доступна только сообществу';
                    $returned .= CHtml::closeTag('div');
                    break;
            }
        }

        if(Yii::app()->user->id !== $this->user_id || (!$this->is_moder_deleted && !$this->is_deleted))
            return $returned;

        $returned .= CHtml::openTag('div', array('class' => 'dark_block', 'style' => 'margin-top: 10px;color: red;'));
        if($this->is_moder_deleted) {
            $returned .= 'Заметка удалена модератором '.$this->moderLog->moder->getFullLogin().'<br>
                Причина: '.$this->moderLog->moder_reason.'. ';

            $returned .= CHtml::link(
                'Удалить навсегда',
                Yii::app()->createUrl('/post/profile/trunc', array('id' => $this->id, 'gameId' => $this->user->game_id)),
                array('confirm' => "Вы уверены, что хотите удалить эту заметку навсегда?")
            );
        } elseif($this->is_deleted) {
            if($this->is_community)
                $linkRestore = Yii::app()->createUrl('/community/post/reset', array('id' => $this->id, 'community_alias' => $this->community_alias));
            else
                $linkRestore = Yii::app()->createUrl('/post/profile/reset', array('id' => $this->id, 'gameId' => $this->user->game_id));

            if($this->is_community)
                $linkTrunc = Yii::app()->createUrl('/community/post/trunc', array('id' => $this->id, 'community_alias' => $this->community_alias));
            else
                $linkTrunc = Yii::app()->createUrl('/post/profile/trunc', array('id' => $this->id, 'gameId' => $this->user->game_id));

            $returned .= 'Заметка удалена, но вы можете восстановить ее. '.CHtml::link('Восстановить', $linkRestore);
            $returned .= ' / '.CHtml::link('Удалить навсегда', $linkTrunc, array('confirm' => "Вы уверены, что хотите удалить эту заметку навсегда?"));
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
                if(false === CacheEventItemPost::updateByItemId($this->id)) {
                    $error = true;
                    MyException::logTxt('[Update] Post -> CacheEventItemPost '.$this->id);
                }
            }

            if(!$error){
                /** Обновляем информацию о заметке */
                $criteria = new CDbCriteria();
                $criteria->addCondition('item_id = :item_id');
                $criteria->params = array(':item_id' => $this->id);

                /** @var ItemInfoPost $ItemInfo */
                $ItemInfo = ItemInfoPost::model()->find($criteria);
                $ItemInfo->title = $this->title;
                $ItemInfo->rating = $this->rating;
                $ItemInfo->view_role = $this->view_role;
                $ItemInfo->comment_count = $this->comment_count;
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
                if(false === CacheEventItemPost::updateByItemId($this->id)) {
                    $error = true;
                    MyException::logTxt('[Restore] Post -> CacheEventItemPost '.$this->id);
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
                $Cache = new CacheEventItemPost('create');
                $Cache->item_id = $this->id;
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
                $Event = new EventItemPost();
                $Event->item_id = $this->id;
                $Event->user_id = $this->user_id;
                $Event->create_datetime = DateTimeFormat::format();
                if(!$Event->save()) {
                    $error = true;
                    Yii::app()->message->setErrors('danger', $Event);
                }
            }

            if(!$error) {
                /** Создаем информацию о заметке */
                $ItemInfo = new ItemInfoPost('create');
                $ItemInfo->item_id = $this->id;
                $ItemInfo->user_owner_id = $this->user_id;
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
                if(false === CacheEventItemPost::updateByItemId($this->id)) {
                    $error = true;
                    MyException::logTxt('[Delete] Post -> CacheEventItemPost '.$this->id);
                }
            }

            if(!$error){
                /** Обновляем информацию о заметке */
                $criteria = new CDbCriteria();
                $criteria->addCondition('item_id = :item_id');
                $criteria->params = array(':item_id' => $this->id);

                /** @var ItemInfoPost $ItemInfo */
                $ItemInfo = ItemInfoPost::model()->find($criteria);
                $ItemInfo->is_deleted = $this->is_deleted;
                $ItemInfo->is_moder_deleted = $this->is_moder_deleted;
                $ItemInfo->deleted_trunc = $this->deleted_trunc;
                $ItemInfo->update_datetime = DateTimeFormat::format();
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
}