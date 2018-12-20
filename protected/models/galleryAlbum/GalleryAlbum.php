<?php

Yii::import('application.models._base.BaseGalleryAlbum');
/**
 * Class GalleryAlbum
 *
 * @property User $user
 * @property integer $imageCount
 * @property integer $audioCount
 * @property integer $videoCount
 * @property GalleryImage $imagePreview
 * @property string $image_front
 * @property integer $album_type
 * @property integer $is_croped
 * @property integer $is_moder_deleted
 * @property integer $comment_count
 * @property integer $rating
 * @property integer $is_reported
 * @property integer deleted_trunc
 * @property string user_update_datetime
 * @property integer community_id
 * @property integer is_community
 * @property integer user_deleted_id
 * @property string community_alias
 *
 * @property UserProfile $userProfile
 *
 *
 * @method GalleryAlbum own()
 * @method GalleryAlbum public()
 * @method GalleryAlbum activatedStatus()
 * @method GalleryAlbum deletedStatus()
 *
 * @package application.gallery.models
 */
class GalleryAlbum extends BaseGalleryAlbum
{
    /**
     * @param string $className
     * @return GalleryAlbum
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
            'public' => array(
                'condition' => $t.'.view_role = :'.$t.'_v_role',
                'params' => array(':'.$t.'_v_role' => 1)
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
            'notCommunity' => array(
                'condition' => $t.'.is_community = 0',
            )
        );
    }

    public function rules() {
        return array(
            array('user_id, title, create_datetime', 'required'),
            array('user_id, is_comment, is_activated, is_deleted, view_role', 'numerical', 'integerOnly'=>true),
            array('title, description', 'length', 'max'=>70),
            array('user_deleted_datetime', 'length', 'max'=>45),
            array('image_front, user_id, is_community, community_id', 'unsafe'),
            array('description, is_comment, is_activated, is_deleted, view_role, user_deleted_datetime', 'default', 'setOnEmpty' => true, 'value' => null),
            array('id, user_id, title, description, is_comment, is_activated, is_deleted, create_datetime, update_datetime, view_role, user_deleted_datetime', 'safe', 'on'=>'search'),
        );
    }

    /**
     * @return array
     */
    public function relations() {
        return array(
            'user'           => array(self::BELONGS_TO, 'User', 'user_id', 'joinType' => 'inner join'),
            'userProfile'    => array(self::HAS_ONE, 'UserProfile', array('user_id' => 'user_id'), 'joinType' => 'inner join'),
            'imageCount'     => array(self::STAT, 'GalleryImage', 'album_id', 'condition' => '`t`.is_moder_deleted = 0 and `t`.is_deleted = 0 and `t`.is_activated = 1'),
            'imagePreview'   => array(self::HAS_ONE, 'GalleryImage', array('album_id' => 'id'), 'on' => '`imagePreview`.is_moder_deleted = 0 and `imagePreview`.is_deleted = 0 and `imagePreview`.is_activated = 1'),
            'audioCount'     => array(self::STAT, 'GalleryAudio', 'album_id', 'condition' => '`t`.is_moder_deleted = 0 and `t`.is_deleted = 0 and `t`.is_activated = 1'),
            'videoCount'     => array(self::STAT, 'GalleryVideo', 'album_id', 'condition' => '`t`.is_moder_deleted = 0 and `t`.is_deleted = 0 and `t`.is_activated = 1'),
            'canRate'        => array(self::HAS_ONE, 'RatingItem', 'item_id', 'scopes' => array('own')),
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
            'description' => Yii::t('app', 'Описание'),
            'is_comment' => Yii::t('app', 'Вкл. комментарии'),
            'is_activated' => Yii::t('app', 'Is Activated'),
            'is_deleted' => Yii::t('app', 'Is Deleted'),
            'create_datetime' => Yii::t('app', 'Create Datetime'),
            'view_role' => Yii::t('app', 'Доступ'),
            'user' => null,
            'galleryImages' => null,
            'galleryVideos' => null,
        );
    }


    protected $dirs = array(
        0 => 'image',
        1 => 'audio',
        2 => 'video'
    );
    public function getImage()
    {
        if($this->image_front !== null && $this->image_front != '' && $this->is_croped)
            return CHtml::image(Yii::app()->baseUrl.'/uploads/albums/'.$this->dirs[$this->album_type].'/'.$this->user_id.'/'.$this->image_front, $this->title);
        elseif(isset($this->imagePreview))
            return CHtml::image($this->imagePreview->getImageUrl(), $this->title);
        else
            return CHtml::image('/themes/default/images/album-bg1.jpg', $this->title);
    }
}