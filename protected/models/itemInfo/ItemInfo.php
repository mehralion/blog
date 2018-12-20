<?php

/**
 * @property integer $user_owner_id
 * @property integer $community_id
 * @property integer $is_community
 * @property integer user_deleted_id
 * @property string community_alias
 * @property string gameId
 * @property string is_parent_delete
 */
class ItemInfo extends BaseItemInfo
{
    /**
     * @param string $className
     * @return ItemInfo
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
            'deletedStatus' => array(
                'condition' => $t.'.is_deleted = :deletedStatus',
            ),
            'moderDeletedStatus' => array(
                'condition' => $t.'.is_moder_deleted = :moderDeletedStatus',
            ),
            'truncatedStatus' => array(
                'condition' => $t.'.deleted_trunc = :truncatedStatus',
            ),
            'activatedStatus' => array(
                'condition' => $t.'.is_activated = :activatedStatus',
            )
        );
    }

    public function rules() {
        return array(
            array('item_id, item_type, view_role', 'required'),
            array('item_id, item_type, album_id, video_type, rating, is_deleted, is_moder_deleted, deleted_trunc, view_role, comment_count', 'numerical', 'integerOnly'=>true),
            array('title, url', 'length'),
            array('image, file_name, file_ext', 'length', 'max'=>255),
            array('title, url, image, album_id, video_type, file_name, file_ext, rating, is_deleted, is_moder_deleted, deleted_trunc, comment_count', 'default', 'setOnEmpty' => true, 'value' => null),
            array('item_id, item_type, title, url, image, album_id, video_type, file_name, file_ext, rating, is_deleted, is_moder_deleted, deleted_trunc, view_role, comment_count, create_datetime, update_datetime, update_relation_datetime', 'safe', 'on'=>'search'),
        );
    }

    public function getShowLink()
    {
        switch ($this->item_type) {
            case ItemTypes::ITEM_TYPE_POST:
                if($this->is_community)
                    return array(
                        'route' => '/community/post/show',
                        'params' => array('community_alias' => $this->community_alias, 'id' => $this->item_id)
                    );
                else
                    return array(
                        'route' => '/post/index/show',
                        'params' => array('id' => $this->item_id, 'gameId' => $this->gameId)
                    );
                break;
            case ItemTypes::ITEM_TYPE_IMAGE:
                if($this->is_community)
                    return array(
                        'route' => '/community/image/show',
                        'params' => array('community_alias' => $this->community_alias, 'id' => $this->item_id)
                    );
                else
                    return array(
                        'route' => '/gallery/image/show',
                        'params' => array('id' => $this->item_id, 'gameId' => $this->gameId)
                    );
                break;
            case ItemTypes::ITEM_TYPE_VIDEO:
                if($this->is_community)
                    return array(
                        'route' => '/community/video/show',
                        'params' => array('community_alias' => $this->community_alias, 'id' => $this->item_id)
                    );
                else
                    return array(
                        'route' => '/gallery/video/show',
                        'params' => array('id' => $this->item_id, 'gameId' => $this->gameId)
                    );
                break;
            case ItemTypes::ITEM_TYPE_AUDIO_ALBUM:
                if($this->is_community)
                    return array(
                        'route' => '/community/album/audio_show',
                        'params' => array('community_alias' => $this->community_alias, 'album_id' => $this->album_id)
                    );
                else
                    return array(
                        'route' => '/user/show/show_audio',
                        'params' => array('album_id' => $this->album_id, 'gameId' => $this->gameId)
                    );
                break;
        }
    }
}