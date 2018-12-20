<?php
/**
 * Class GalleryAlbumAudio
 *
 * Relations
 * @property SubscribeDebate $canSubscribe
 *
 * @package application.gallery.models
 */
class GalleryAlbumAudio extends GalleryAlbum
{
    public $oldImage;
    public $oldViewRole;

    /**
     * @param string $className
     * @return GalleryAlbumAudio
     */
    public static function model($className=__CLASS__) {
        return parent::model($className);
    }

    public function defaultScope()
    {
        $t = $this->getTableAlias(false, false);
        return array(
            'condition' => $t.'.album_type = :'.$t.'_album_type',
            'params' => array(':'.$t.'_album_type' => ItemTypes::ITEM_TYPE_AUDIO_ALBUM)
        );
    }

    public function afterFind()
    {
        $this->oldViewRole = $this->view_role;
        parent::afterFind();
    }

    public function beforeValidate()
    {
        $this->album_type = ItemTypes::ITEM_TYPE_AUDIO_ALBUM;
        return parent::beforeValidate();
    }

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

    public function relations() {
        $parent = parent::relations();
        $parent['canRate'] = array(
            self::HAS_ONE,
            'RatingItemAudio',
            'item_id',
            'scopes' => array('own')
        );
        $parent['canSubscribe'] = array(
            self::HAS_ONE,
            'SubscribeDebateAudio',
            'item_id',
            'scopes' => array('own')
        );
        $parent['info'] = array(
            self::HAS_ONE,
            'ItemInfoAudioAlbum',
            'item_id',
            'joinType' => 'inner join'
        );
        return $parent;
    }

    /**
     * Укл в папку альбом пользователя
     * @return string
     */
    public function getBaseUrl()
    {
        return Yii::app()->theme->uploadAlbum.'/audio/'.$this->user_id;
    }

    public function getImageUrl($local = false)
    {
        if($local)
            return $this->getBaseUrl().'/'.$this->image_front;
        elseif($this->image_front !== null && $this->image_front != '' && $this->is_croped)
            return Yii::app()->theme->uploadAlbumLink.'/audio/'.$this->user_id.'/'.$this->image_front;
        elseif(isset($this->imagePreview))
            return $this->imagePreview->getImageUrl();
        else
            return Yii::app()->theme->baseUrl.'/images/albums/audio.jpg';
    }

    public function getImage($local = false)
    {
        if($local)
            return CHtml::image($this->getBaseUrl().'/'.$this->image_front, $this->title);
        elseif($this->image_front !== null && $this->image_front != '' && $this->is_croped)
            return CHtml::image(Yii::app()->theme->uploadAlbumLink.'/audio/'.$this->user_id.'/'.$this->image_front, $this->title);
        elseif(isset($this->imagePreview))
            return CHtml::image($this->imagePreview->getImageUrl(), $this->title);
        else
            return CHtml::image(Yii::app()->theme->baseUrl.'/images/albums/audio.jpg', $this->title);
    }

    public function canRate()
    {
        if($this->user_id == Yii::app()->user->id || isset($this->canRate) || Yii::app()->user->isGuest)
            return '<span class="icon" id="like"></span>';
        else
            return CHtml::link(
                '<i class="icon" id="like"></i>',
                Yii::app()->createUrl('/rating/audio/add', array('album_id' => $this->id)),
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
                    Yii::app()->createUrl('/subscribe/request/audio', array('item_id' => $this->id)),
                    array('class' => 'addSubscribe')
                );
        }
    }

    public function drawSubDescriptionsTextDeleted()
    {
        $returned = '';
        if(Yii::app()->user->isModer() && $this->user_id !== Yii::app()->user->id) {
            switch ($this->view_role) {
                case Access::VIEW_ROLE_FRIEND:
                    $returned .= CHtml::openTag('div', array('class' => 'dark_block', 'style' => 'margin-top: 10px;color: red;'));
                    $returned .= 'Аудиоальбом доступен только друзьям';
                    $returned .= CHtml::closeTag('div');
                    break;
                case Access::VIEW_ROLE_ME:
                    $returned .= CHtml::openTag('div', array('class' => 'dark_block', 'style' => 'margin-top: 10px;color: red;'));
                    $returned .= 'Аудиоальбом доступен только владельцу';
                    $returned .= CHtml::closeTag('div');
                    break;
            }
        }

        return $returned;
    }

    public static function getUserAlbums()
    {
        $returned = array();
        $criteria = new CDbCriteria();
        $criteria->scopes = array(
            'own',
            'activatedStatus',
            'deletedStatus',
            'moderDeletedStatus',
            'truncatedStatus',
        );
        $criteria->params = array(
            ':activatedStatus' => 1,
            ':deletedStatus' => 0,
            ':moderDeletedStatus' => 0,
            ':truncatedStatus' => 0,
        );
        /** @var GalleryAlbumAudio[] $albums */
        $albums = self::model()->findAll($criteria);
        foreach($albums as $album)
            $returned[$album->id] = $album->title.' ('.Access::getRoleName($album->view_role).')';

        return $returned;
    }

    public static function getCommunityAlbums($communityId)
    {
        $returned = array();
        $criteria = new CDbCriteria();
        $criteria->addCondition('`t`.community_id = :community_id');
        $criteria->scopes = array(
            'activatedStatus',
            'deletedStatus',
            'moderDeletedStatus',
        );
        $criteria->params = array(
            ':activatedStatus' => 1,
            ':deletedStatus' => 0,
            ':moderDeletedStatus' => 0,
            ':community_id' => $communityId
        );

        /** @var GalleryAlbumAudio[] $albums */
        $albums = self::model()->findAll($criteria);
        foreach($albums as $album)
            $returned[$album->id] = $album->title.' ('.Access::getRoleName($album->view_role, true).')';

        return $returned;
    }

    public function getRateList($limit = 8) {
        $id = 'audio_rating_list_'.$this->id.'_'.$this->rating;
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
            $returned['count'] = RatingItemAudio::model()->count($criteria);

            $criteria->limit = $limit;
            /** @var RatingItemAudio[] $Ratings */
            $Ratings = RatingItemAudio::model()->findAll($criteria);
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
                if(false === CacheEventItemAudio::updateAllByAlbumId($this->id)) {
                    $error = true;
                    MyException::logTxt('[Update] GalleryAlbumAudio -> CacheEventItemAudio '.$this->id);
                }
            }

            if(!$error && null !== $this->oldViewRole && $this->oldViewRole != $this->view_role) {
                /** Если поменяли права доступа альбома, меняем их для аудиозаписей */
                $params = array('view_role' => $this->view_role, 'update_datetime' => DateTimeFormat::format());
                if(false === GalleryAudio::updateAllByAlbumId($this->id, $params)) {
                    $error = true;
                    MyException::logTxt('[Update] GalleryAlbumAudio -> GalleryAudio '.$this->id);
                }
            }

            if(!$error){
                /** Обновляем информацию о заметке */
                $criteria = new CDbCriteria();
                $criteria->addCondition('item_id = :item_id');
                $criteria->params = array(':item_id' => $this->id);

                /** @var ItemInfoAudioAlbum $ItemInfo */
                $ItemInfo = ItemInfoAudioAlbum::model()->find($criteria);
                $ItemInfo->image = $this->image_front;
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
                if(false === CacheEventItemAudio::updateAllByAlbumId($this->id)) {
                    $error = true;
                    MyException::logTxt('[Restore] GalleryAlbumAudio -> CacheEventItemAudio '.$this->id);
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
                /** Создаем информацию о аудиоальбоме */
                $ItemInfo = new ItemInfoAudioAlbum('create');
                $ItemInfo->item_id = $this->id;
                $ItemInfo->user_owner_id = $this->user_id;
                $ItemInfo->album_id = $this->id;
                $ItemInfo->image = $this->image_front;
                $ItemInfo->title = $this->title;
                $ItemInfo->view_role = $this->view_role;
                $ItemInfo->community_alias = $this->community_alias;
                $ItemInfo->community_id = $this->community_id;
                $ItemInfo->is_community = $this->is_community;
                $ItemInfo->gameId = Yii::app()->user->getGameId();
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
                if(false === CacheEventItemAudio::updateAllByAlbumId($this->id)) {
                    $error = true;
                    MyException::logTxt('[Delete] GalleryAlbumAudio -> CacheEventItemAudio '.$this->id);
                }
            }

            if(!$error) {
                $attributes = array(
                    'is_deleted' => $this->is_deleted,
                    'is_moder_deleted' => $this->is_moder_deleted,
                    'deleted_trunc' => $this->deleted_trunc,
                    'user_deleted_id' => $this->user_deleted_id,
                    'update_datetime' => DateTimeFormat::format()
                );
                $params = array(':album_id' => $this->id);

                if(false === GalleryAudio::updateAllByAlbumId($attributes, 'album_id = :album_id', $params)) {
                    $error = true;
                    MyException::logTxt('[Delete] GalleryAlbumAudio -> GalleryAudio '.$this->id);
                }
            }

            if(!$error){
                /** Обновляем информацию о аудиоальбоме */
                $criteria = new CDbCriteria();
                $criteria->addCondition('item_id = :item_id');
                $criteria->params = array(':item_id' => $this->id);

                /** @var ItemInfoAudioAlbum $ItemInfo */
                $ItemInfo = ItemInfoAudioAlbum::model()->find($criteria);
                $ItemInfo->is_deleted = $this->is_deleted;
                $ItemInfo->is_moder_deleted = $this->is_moder_deleted;
                $ItemInfo->deleted_trunc = $this->deleted_trunc;
                $ItemInfo->user_deleted_id = $this->user_deleted_id;
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