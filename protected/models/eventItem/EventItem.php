<?php

/**
 * Class EventItem
 *
 * @property User $user
 * @property Post $post
 * @property GalleryAlbum $albumInfo
 * @property GalleryImage $image
 * @property GalleryImage[] $imageAll
 * @property GalleryAudio[] $audioAll
 * @property GalleryVideo $video
 * @property GalleryVideo[] $videoAll
 * @property CommentItem[] $commentCount
 *
 * @package application.event.models
 */
class EventItem extends BaseEventItem
{

    /**
     * @param string $className
     * @return EventItem
     */
    public static function model($className=__CLASS__) {
        return parent::model($className);
    }

    /**
     * @return array
     */
    public function relations() {
        return array(
            'user'       => array(self::BELONGS_TO, 'User', 'user_id', 'joinType' => 'inner join'),
            'post'       => array(self::BELONGS_TO, 'Post', 'item_id', 'joinType' => 'inner join'),
            'image'      => array(self::BELONGS_TO, 'GalleryImage', 'item_id', 'joinType' => 'inner join'),
            'video'      => array(self::BELONGS_TO, 'GalleryVideo', 'item_id', 'joinType' => 'inner join'),
            'albumInfo'  => array(self::BELONGS_TO, 'GalleryAlbum', 'album_id', 'joinType' => 'inner join'),
            'imageAll'   => array(self::HAS_MANY, 'GalleryImage', array('user_id' => 'user_id', 'album_id' => 'album_id'), 'joinType' => 'inner join'),
            'audioAll'   => array(self::HAS_MANY, 'GalleryAudio', array('user_id' => 'user_id', 'album_id' => 'album_id'), 'joinType' => 'inner join'),
            'videoAll'   => array(self::HAS_MANY, 'GalleryVideo', array('user_id' => 'user_id', 'album_id' => 'album_id'), 'joinType' => 'inner join'),
        );
    }
}