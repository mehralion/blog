<?php

/**
 * Class CommentItem
 *
 * Params
 * @property array $onBefore
 * @property array $onCreate
 * @property array onAfter
 * @property array $onDelete
 * @property integer $is_reported
 * @property integer $user_deleted_id
 * @property integer deleted_trunc
 * @property integer rating
 * @property string user_update_datetime
 *
 * Relations
 * @property User $user
 * @property User $userOwner
 * @property Post $post
 * @property GalleryImage $image
 * @property GalleryVideo $video
 * @property Report $report
 * @property ModerLog $moderLog
 * @property User $userDeleted
 * @property GalleryAlbumAudio $audio
 * @property UserProfile $userProfile
 * @property Community $community
 * @property ItemInfo $info
 *
 * Behaviors
 * @property RightsBehavior $rights
 *
 * Scopes
 * @method CommentItem own()
 * @method CommentItem activatedStatus()
 * @method CommentItem deletedStatus()
 * @method CommentItem moderDeletedStatus()
 *
 * @package application.comment.models
 */
class CommentItem extends BaseCommentItem
{
    /** @var self  */
    public $thisOld = null;

    public $quote = true;

    /**
     * @param string $className
     * @return CommentItem
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
                'condition' => $t.'.user_id = :'.$t.'_user_id',
                'params' => array(':'.$t.'_user_id' => Yii::app()->user->id)
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
     * @return array
     */
    public function rules() {
        return array(
            array('user_owner_id, description, user_id, item_type, create_datetime', 'required'),
            array('item_id, user_id, user_owner_id, item_type, is_activated, is_deleted, is_moder_deleted', 'numerical', 'integerOnly'=>true),
            array('description, update_datetime, user_deleted_datetime', 'safe'),
            array('item_id, user_id, description, item_type, update_datetime, is_activated, is_deleted, is_moder_deleted, user_deleted_datetime', 'default', 'setOnEmpty' => true, 'value' => null),
            array('id, item_id, user_id, user_owner_id, description, item_type, create_datetime, update_datetime, is_activated, is_deleted, is_moder_deleted, user_deleted_datetime', 'safe', 'on'=>'search'),
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
            'userProfile'    => array(self::HAS_ONE, 'UserProfile', array('user_id' => 'user_id'), 'joinType' => 'inner join'),
            'user'           => array(self::BELONGS_TO, 'User', 'user_id', 'joinType' => 'inner join'),
            'userOwner'      => array(self::BELONGS_TO, 'User', 'user_owner_id', 'joinType' => 'inner join'),
            'post'           => array(self::BELONGS_TO, 'Post', 'item_id'),
            'image'          => array(self::BELONGS_TO, 'GalleryImage', 'item_id'),
            'video'          => array(self::BELONGS_TO, 'GalleryVideo', 'item_id'),
            'audio'          => array(self::BELONGS_TO, 'GalleryAlbumAudio','item_id'),
            'community'      => array(self::BELONGS_TO, 'Community', 'item_id'),
            'report'         => array(self::HAS_ONE, 'ReportComment', array('item_id' => 'id')),
            'moderLog'       => array(self::HAS_ONE, 'ModerLogComment', 'item_id'),
            'userDeleted'    => array(self::BELONGS_TO, 'User', 'user_deleted_id', 'joinType' => 'inner join'),
            'canRate'        => array(self::HAS_ONE, 'RatingItemComment', array('item_id' => 'id'), 'scopes' => array('own')),
            'info'           => array(self::HAS_ONE, 'ItemInfo', array('item_id' => 'item_id', 'item_type' => 'item_type'), 'joinType' => 'inner join')
        );
    }


    public function getDeleteUrl()
    {
        $route = '';
        switch($this->item_type) {
            case ItemTypes::ITEM_TYPE_POST:
                $route = '/comment/post/delete';
                break;
            case ItemTypes::ITEM_TYPE_VIDEO:
                $route = '/comment/video/delete';
                break;
            case ItemTypes::ITEM_TYPE_AUDIO_ALBUM:
                $route = '/comment/audio/delete';
                break;
            case ItemTypes::ITEM_TYPE_IMAGE:
                $route = '/comment/image/delete';
                break;
            case ItemTypes::ITEM_TYPE_COMMUNITY:
                $route = '/comment/community/delete';
                break;
        }
        $params = array('id' => $this->id);
        if(!$this->info->is_community)
            $params = CMap::mergeArray($params, array('gameId' => $this->user->game_id));
        else
            $params = CMap::mergeArray($params, array('community_alias' => $this->info->community_alias));

        return Yii::app()->createUrl($route, $params);
    }

    public function getModerDeleteUrl()
    {
        $route = '';
        switch($this->item_type) {
            case ItemTypes::ITEM_TYPE_POST:
                $route = '/moder/comment/delete_post';
                break;
            case ItemTypes::ITEM_TYPE_VIDEO:
                $route = '/moder/comment/delete_video';
                break;
            case ItemTypes::ITEM_TYPE_AUDIO_ALBUM:
                $route = '/moder/comment/delete_album_audio';
                break;
            case ItemTypes::ITEM_TYPE_IMAGE:
                $route = '/moder/comment/delete_image';
                break;
            case ItemTypes::ITEM_TYPE_COMMUNITY:
                $route = '/moder/comment/delete_community';
                break;
        }
        $params = array('id' => $this->id);
        if(!$this->info->is_community)
            $params = CMap::mergeArray($params, array('gameId' => $this->user->game_id));
        else
            $params = CMap::mergeArray($params, array('community_alias' => $this->info->community_alias));

        return Yii::app()->createUrl($route, $params);
    }

    public function getReportUrl()
    {
        $route = '';
        switch($this->item_type) {
            case ItemTypes::ITEM_TYPE_POST:
                $route = '/moder/report/comment_post';
                break;
            case ItemTypes::ITEM_TYPE_VIDEO:
                $route = '/moder/report/comment_video';
                break;
            case ItemTypes::ITEM_TYPE_AUDIO_ALBUM:
                $route = '/moder/report/comment_album_audio';
                break;
            case ItemTypes::ITEM_TYPE_IMAGE:
                $route = '/moder/report/comment_image';
                break;
            case ItemTypes::ITEM_TYPE_COMMUNITY:
                $route = '/moder/report/comment_community';
                break;
        }
        $params = array('id' => $this->id);
        if(!$this->info->is_community)
            $params = CMap::mergeArray($params, array('gameId' => $this->user->game_id));
        else
            $params = CMap::mergeArray($params, array('community_alias' => $this->info->community_alias));

        return Yii::app()->createUrl($route, $params);
    }

    private $_itemsController = array(
        ItemTypes::ITEM_TYPE_POST => array(
            'controller' => 'post',
            'viewRoute' => '/post/index/show',
            'param' => 'id',
            'report' => 'comment_post',
            'moder_delete' => 'delete_post',
            'moder_accept' => 'accept_post',
            'moder_restore' => 'restore_post',
        ),
        ItemTypes::ITEM_TYPE_AUDIO_ALBUM => array(
            'controller' => 'audio',
            'viewRoute' => '/gallery/album/show_audio',
            'param' => 'album_id',
            'report' => 'comment_album_audio',
            'moder_delete' => 'delete_album_audio',
            'moder_accept' => 'accept_album_audio',
            'moder_restore' => 'restore_album_audio',
        ),
        ItemTypes::ITEM_TYPE_VIDEO => array(
            'controller' => 'video',
            'viewRoute' => '/gallery/video/show',
            'param' => 'id',
            'report' => 'comment_video',
            'moder_delete' => 'delete_video',
            'moder_accept' => 'accept_video',
            'moder_restore' => 'restore_video',
        ),
        ItemTypes::ITEM_TYPE_IMAGE => array(
            'controller' => 'image',
            'viewRoute' => '/gallery/image/show',
            'param' => 'id',
            'report' => 'comment_image',
            'moder_delete' => 'delete_image',
            'moder_accept' => 'accept_image',
            'moder_restore' => 'restore_image',
        ),
        ItemTypes::ITEM_TYPE_COMMUNITY => array(
            'controller' => 'community',
            'viewRoute' => '/community/request/show',
            'param' => 'id',
            'report' => 'comment_community',
            'moder_delete' => 'delete_community',
            'moder_accept' => 'accept_community',
            'moder_restore' => 'restore_community',
        ),
    );

    /**
     * @return string
     */
    public function drawSubDescriptionsTextDeleted()
    {
        $returned = '';
        if(((Yii::app()->user->id != $this->user_id && Yii::app()->user->id != $this->user_deleted_id) && !Yii::app()->user->isModer()) || (!$this->is_moder_deleted && !$this->is_deleted))
            return '';

        $item = $this->_itemsController[$this->item_type];
        $controller = $item['controller'];
        $link = Yii::app()->createUrl($item['viewRoute'], array(
            $item['param'] => $this->item_id,
            'gameId' => $this->userOwner->game_id
        ));

        $returned .= CHtml::openTag('div', array('class' => 'dark_block', 'style' => 'margin-top: 10px;color: red;'));

        if($this->is_moder_deleted) {
            $returned .= 'Комментарий удален модератором '.$this->moderLog->moder->getFullLogin().'<br>
                Причина: '.$this->moderLog->moder_reason.'. ';

            if($this->user_id == Yii::app()->user->id)
                $returned .= CHtml::link(
                    'Удалить навсегда',
                    Yii::app()->createUrl('/comment/'.$controller.'/trunc', array('id' => $this->id, 'gameId' => $this->user->game_id)),
                    array('confirm' => "Вы уверены, что хотите удалить этот комментарий навсегда?")
                );
            $returned .= CHtml::link(
                '(Перейти)',
                $link,
                array('target' => '_blank')
            );
        } elseif($this->is_deleted) {
            if($this->user_deleted_id == Yii::app()->user->id) {
                $returned .= 'Комментарий удален, но вы можете восстановить его '
                    .CHtml::link('Восстановить', Yii::app()->createUrl('/comment/'.$controller.'/reset', array('id' => $this->id, 'gameId' => $this->user->game_id)));
                $returned .= ' / '.CHtml::link(
                        'Удалить навсегда',
                        Yii::app()->createUrl('/comment/'.$controller.'/trunc', array('id' => $this->id, 'gameId' => $this->user->game_id)),
                        array('confirm' => "Вы уверены, что хотите удалить этот комментарий навсегда?")
                    );
            } else {
                $returned .= 'Комментарий удален '.$this->userDeleted->getFullLogin();
                $returned .= ' '.CHtml::link(
                        'Удалить навсегда',
                        Yii::app()->createUrl('/comment/'.$controller.'/trunc', array('id' => $this->id, 'gameId' => $this->user->game_id)),
                        array('confirm' => "Вы уверены, что хотите удалить этот комментарий навсегда?")
                    );
            }
            $returned .= ' '.CHtml::link(
                '(Перейти)',
                $link,
                array('target' => '_blank')
            );
        }
        $returned .= CHtml::closeTag('div');

        return $returned;
    }

    /**
     * @param bool $link
     * @return string
     */
    public function canRate($link = true)
    {
        if($this->user_id == Yii::app()->user->id || !$link)
            return '<span class="icon" id="like"></span>';
        elseif(isset($this->canRate))
            return '<span class="icon" id="like_tuskl"></span>';
        else
            if($this->info->is_community)
                return CHtml::link(
                    '<i class="icon" id="like"></i>',
                    Yii::app()->createUrl('/community/rating/comment_add', array('item_id' => $this->id, 'community_alias' => $this->info->community_alias))
                );
            else
                return CHtml::link(
                    '<i class="icon" id="like"></i>',
                    Yii::app()->createUrl('/rating/comment/add', array('item_id' => $this->id))
                );
    }

    public function getRateList($limit = 8) {
        $id = 'comment_rating_list_'.$this->id.'_'.$this->rating;
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
            $returned['count'] = RatingItemComment::model()->count($criteria);

            $criteria->limit = $limit;
            /** @var RatingItemComment[] $Ratings */
            $Ratings = RatingItemComment::model()->findAll($criteria);
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

            if(!$error && $this->rating > 0) {
                /** Снимаем рейтинг при удалении */
                /** @var UserProfile $UserProfile */
                $UserProfile = UserProfile::model()->find('user_id = :user_id', array(':user_id' => $this->user_id));
                $UserProfile->rating -= $this->rating;
                if(!$UserProfile->save()) {
                    $error = true;
                    Yii::app()->message->setErrors('danger', $UserProfile);
                }

                if($this->info->is_community) {
                    /** @var \Community $Community */
                    $Community = \Community::model()->findByPk($this->info->community_id);
                    $Community->rating -= $this->rating;
                    if(!$Community->mUpdate())
                        $error = true;
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
    public function restore($validate = true, $attributes = null)
    {
        /** @var CDbTransaction $t */
        $t = null;
        if(null === Yii::app()->db->getCurrentTransaction())
            $t = Yii::app()->db->beginTransaction();

        $error = false;
        try {
            $this->user_deleted_id = 0;
            $this->is_deleted = 0;
            $this->update_datetime = DateTimeFormat::format();
            if(!$this->save($validate, $attributes)) {
                $error = true;
                Yii::app()->message->setErrors('danger', $this);
            }

            if(!$error && $this->rating > 0) {
                /** Снимаем рейтинг при удалении */
                /** @var UserProfile $UserProfile */
                $UserProfile = UserProfile::model()->find('user_id = :user_id', array(':user_id' => $this->user_id));
                $UserProfile->rating += $this->rating;
                if(!$UserProfile->save()) {
                    $error = true;
                    Yii::app()->message->setErrors('danger', $UserProfile);
                }

                if($this->info->is_community) {
                    /** @var \Community $Community */
                    $Community = \Community::model()->findByPk($this->info->community_id);
                    $Community->rating += $this->rating;
                    if(!$Community->mUpdate())
                        $error = true;
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