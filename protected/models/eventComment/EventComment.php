<?php

/**
 * Class EventComment
 *
 * @property User $user
 * @property User $userOwner
 * @property Post $post
 * @property GalleryImage $image
 * @property GalleryVideo $video
 * @property CommentItem $comment
 * @property ItemInfo $info
 * @property GalleryAlbumAudio $audio
 *
 * @method EventComment notMe()
 *
 * @package application.event.models
 */
class EventComment extends BaseEventComment
{

    /**
     * @param string $className
     * @return EventComment
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
            'notMe' => array(
                'condition' => $t.'.user_id != :'.$t.'_user_id',
                'params' => array(':'.$t.'_user_id' => Yii::app()->user->id)
            )
        );
    }

    /**
     * @return array
     */
    public function relations() {
        return array(
            'user'       => array(self::BELONGS_TO, 'User', 'user_id', 'joinType' => 'inner join'),
            'userOwner'  => array(self::BELONGS_TO, 'User', 'user_owner_id', 'joinType' => 'inner join'),
            'post'       => array(self::BELONGS_TO, 'Post', 'item_id'),
            'image'      => array(self::BELONGS_TO, 'GalleryImage', 'item_id'),
            'video'      => array(self::BELONGS_TO, 'GalleryVideo', 'item_id'),
            'audio'      => array(self::BELONGS_TO, 'GalleryAlbumAudio', 'item_id'),
            'comment'    => array(self::BELONGS_TO, 'CommentItem', 'comment_id', 'joinType' => 'inner join'),
            'info'       => array(self::HAS_ONE, 'ItemInfo', array('item_id' => 'item_id', 'item_type' => 'item_type'), 'joinType' => 'inner join')
        );
    }
}