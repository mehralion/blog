<?php

/**
 * Class Community
 *
 * Relations
 * @property User $user
 * @property CommunityUser $inCommunity
 * @property CommunityRequest $inRequest
 * @property RatingItemCommunity $canRate
 *
 * Params
 * @property integer $is_deleted
 * @property integer $is_moder_deleted
 * @property integer $user_id
 * @property integer $step
 * @property integer $is_comment
 * @property integer $comment_count
 * @property integer $rating
 * @property integer $is_reported
 * @property integer $is_croped
 * @property integer deleted_trunc
 * @property integer user_deleted_id
 *
 * @property integer postCount
 * @property integer imageCount
 * @property integer videoCount
 * @property integer audioCount
 */
class Community extends BaseCommunity
{
    const TYPE_PUBLIC    = 0; //Все могут вступить
    const TYPE_MODER     = 1; //После модерации
    const TYPE_INVITE    = 2; //Только по приглашению

    const SIZE_THUMBS_WIDTH = 98;
    const SIZE_THUMBS_HEIGHT = 98;

	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

    public function rules() {
        return array(
            array('user_id, alias, title, description, update_datetime, create_datetime', 'required'),
            array('category_id, user_id, view_role', 'numerical', 'integerOnly'=>true),
            array('alias, title, image', 'length', 'max'=>255),
            array('image, view_role', 'default', 'setOnEmpty' => true, 'value' => null),
            array('id, user_id, alias, title, description, image, view_role, update_datetime, create_datetime', 'safe', 'on'=>'search'),
            array('alias', 'unique', 'criteria' => array('condition' => 'is_deleted = 0')),
            array('user_id', 'unsafe'),
            array('alias', 'unsafe', 'on' => 'edit'),
            array('alias', 'match', 'pattern' => '/^[a-zA-Z_]+$/u', 'message' => 'Название на латинице. Допустимые символы "a-zA-Z_"'),
        );
    }

    public function attributeLabels() {
        return array(
            'id' => Yii::t('app', 'ID'),
            'category_id' => Yii::t('app', 'Категория'),
            'user_id' => Yii::t('app', 'User Owner'),
            'alias' => Yii::t('app', 'Алиас'),
            'title' => Yii::t('app', 'Название'),
            'description' => Yii::t('app', 'Описание'),
            'image' => Yii::t('app', 'Image'),
            'view_role' => Yii::t('app', 'Тип вступления'),
            'update_datetime' => Yii::t('app', 'Update Datetime'),
            'create_datetime' => Yii::t('app', 'Create Datetime'),
        );
    }

    public function relations() {
        return array(
            'user' => array(
                self::BELONGS_TO,
                'User',
                'user_id',
                'joinType' => 'inner join'
            ),
            'inCommunity' => array(
                self::HAS_ONE,
                'CommunityUser',
                'community_id',
                'joinType' => 'inner join',
                'on' => '`inCommunity`.user_id = :inCommunity_user_id and `inCommunity`.is_deleted = 0',
                'params' => array(':inCommunity_user_id' => Yii::app()->user->id)
            ),
            'inRequest' => array(
                self::HAS_ONE,
                'CommunityRequest',
                'community_id',
                'joinType' => 'left join',
                'on' => '`inRequest`.user_id = :inRequest_user_id',
                'params' => array(':inRequest_user_id' => Yii::app()->user->id),
                'scopes' => array('pending')
            ),
            'canRate' => array(
                self::HAS_ONE,
                'RatingItemCommunity',
                'item_id',
                'scopes' => array('own')
            ),
            'postCount' => array(
                self::STAT,
                'Post',
                'community_id',
                'condition' => '`t`.is_deleted = 0 and `t`.is_moder_deleted = 0 and `t`.deleted_trunc = 0',
            ),
            'imageCount' => array(
                self::STAT,
                'GalleryImage',
                'community_id',
                'condition' => '`t`.is_deleted = 0 and `t`.is_moder_deleted = 0 and `t`.deleted_trunc = 0',
            ),
            'videoCount' => array(
                self::STAT,
                'GalleryVideo',
                'community_id',
                'condition' => '`t`.is_deleted = 0 and `t`.is_moder_deleted = 0 and `t`.deleted_trunc = 0',
            ),
            'audioCount' => array(
                self::STAT,
                'GalleryAudio',
                'community_id',
                'condition' => '`t`.is_deleted = 0 and `t`.is_moder_deleted = 0 and `t`.deleted_trunc = 0',
            ),
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
                    'class' => '\application\modules\community\behaviors\Rights'
                ),
            )
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
                'condition' => $t.'.user_id = :'.$t.'user_id',
                'params' => array(':'.$t.'user_id' => Yii::app()->user->id)
            ),
            'notMe' => array(
                'condition' => $t.'.user_id != :'.$t.'user_id',
                'params' => array(':'.$t.'user_id' => Yii::app()->user->id)
            ),
            'canComment' => array(
                'condition' => $t.'.is_comment = 1',
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
                if(false === CacheEventItemCommunity::updateAllByCommunityId($this->id)) {
                    $error = true;
                    MyException::logTxt('[Update] Community -> CacheEventItemCommunity '.$this->id);
                }
            }

            if(!$error){
                /** Обновляем информацию о заметке */
                $criteria = new CDbCriteria();
                $criteria->addCondition('item_id = :item_id');
                $criteria->params = array(':item_id' => $this->id);

                /** @var ItemInfoCommunity $ItemInfo */
                $ItemInfo = ItemInfoCommunity::model()->find($criteria);
                $ItemInfo->title = $this->title;
                $ItemInfo->rating = $this->rating;
                if($this->view_role != self::TYPE_PUBLIC)
                    $ItemInfo->view_role = Access::VIEW_ROLE_COMMUNITY;
                else
                    $ItemInfo->view_role = Access::VIEW_ROLE_ALL;
                $ItemInfo->comment_count = $this->comment_count;
                $ItemInfo->community_alias = $this->alias;
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
                if(false === CacheEventItemCommunity::updateAllByCommunityId($this->id)) {
                    $error = true;
                    MyException::logTxt('[Restore] Community -> CacheEventItemCommunity '.$this->id);
                }
            }

            if(!$error) {
                $attributes = array(
                    'update_datetime' => DateTimeFormat::format(),
                    'is_moder_deleted' => 0,
                    'is_deleted' => 0,
                    'deleted_trunc' => 0,
                    'is_parent_delete' => 0
                );
                $condition = 'community_id = :community_id and is_parent_delete = 1';
                $params = array(':community_id' => $this->id);

                if(false === ItemInfoCommunity::updateAllByCommunityId($attributes, $condition, $params)) {
                    $error = true;
                    MyException::logTxt('[Restore] Community -> ItemInfoCommunity '.$this->id);
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
                $Cache = new CacheEventItemCommunity('create');
                $Cache->item_id = $this->id;
                $Cache->community_id = $this->id;
                $Cache->user_id = $this->user_id;
                $Cache->update_datetime = DateTimeFormat::format();
                if(!$Cache->save()) {
                    $error = true;
                    Yii::app()->message->setErrors('danger', $Cache);
                }
            }

            if(!$error) {
                /** Создаем информацию о сообществе */
                $ItemInfo = new ItemInfoCommunity('create');
                $ItemInfo->item_id = $this->id;
                $ItemInfo->user_owner_id = $this->user_id;
                $ItemInfo->title = $this->title;
                if($this->view_role != self::TYPE_PUBLIC)
                    $ItemInfo->view_role = Access::VIEW_ROLE_COMMUNITY;
                else
                    $ItemInfo->view_role = Access::VIEW_ROLE_ALL;
                $ItemInfo->community_id = $this->id;
                $ItemInfo->community_alias = $this->alias;
                $ItemInfo->is_community = 1;
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

            $this->update_datetime = DateTimeFormat::format();
            if(!$this->save($validate, $attributes)) {
                $error = true;
                Yii::app()->message->setErrors('danger', $this);
            }

            if(!$error) {
                /** Обновляем кэш при удалении */
                if(false === CacheEventItemCommunity::updateAllByCommunityId($this->id)) {
                    $error = true;
                    MyException::logTxt('[Delete] Community -> CacheEventItemCommunity '.$this->id);
                }
            }

            if(!$error) {
                $attributes = array(
                    'update_datetime' => DateTimeFormat::format(),
                    'is_moder_deleted' => $this->is_moder_deleted,
                    'is_deleted' => $this->is_deleted,
                    'deleted_trunc' => $this->deleted_trunc,
                    'user_deleted_id' => $this->user_deleted_id,
                    'is_parent_delete' => 1
                );
                $condition = 'community_id = :community_id and is_deleted = 0 and is_moder_deleted = 0 and deleted_trunc = 0';
                $params = array(':community_id' => $this->id);

                if(false === ItemInfoCommunity::updateAllByCommunityId($attributes, $condition, $params)) {
                    $error = true;
                    MyException::logTxt('[Delete] Community -> ItemInfoCommunity '.$this->id);
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

    public static function getTypes() {
        return array(
            self::TYPE_PUBLIC => 'Открытое',
            self::TYPE_MODER  => 'После модерации',
            self::TYPE_INVITE => 'По приглашению'
        );
    }

    public static function getCurrType($type) {
        $types = self::getTypes();
        return isset($types[$type]) ? $types[$type] : 'Неизвестно';
    }

    public function drawSubDescriptionsTextDeleted() {
        return '';
    }

    public static function getTbDependency() {
        $dependency = new \CDbCacheDependency('select max(update_datetime) from {{cache_event_item}} where item_type = :item_type');
        $dependency->params = array(':item_type' => \ItemTypes::ITEM_TYPE_COMMUNITY);
        $dependency->reuseDependentData = true;

        return $dependency;
    }

    /**
     * @param bool $link
     * @return string
     */
    public function canRate($link = true)
    {
        return '<span class="icon" id="like"></span>';
    }

    public function getRateList($limit = 8) {
        $id = 'community_rating_list_'.$this->id.'_'.$this->rating;
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
            $returned['count'] = RatingItemCommunity::model()->count($criteria);

            $criteria->limit = $limit;
            /** @var RatingItemCommunity[] $Ratings */
            $Ratings = RatingItemCommunity::model()->findAll($criteria);
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

    public function getBaseUrl()
    {
        return Yii::app()->theme->uploadAvatar;
    }

    public function getAvatar($local = false)
    {
        if(null === $this->image || $this->image == "")
            return Yii::app()->theme->baseUrl.'/images/'.Yii::app()->params['no_avatar'];

        $file = Yii::app()->basePath.'/../uploads/avatars/'.$this->image;
        if(file_exists($file) && !$this->is_croped && $local)
            return $this->getBaseUrl().'/'.$this->image;
        elseif($this->is_croped)
            return Yii::app()->theme->uploadAvatarLink.'/'.$this->image;
        else
            return Yii::app()->theme->baseUrl.'/images/'.Yii::app()->params['no_avatar'];
    }
}