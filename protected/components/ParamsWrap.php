<?php

/**
 * Class ParamsWrap
 *
 * @property ParamsPageSize $pageSize
 * @property ParamsCache $cache
 * @property ParamsIcon $icons
 */
class ParamsWrap extends CApplicationComponent
{
    private $_params = array();

    public function init()
    {
        $this->_params = array(
            'pageSize' => new ParamsPageSize(),
            'cache' => new ParamsCache(),
            'icons' => new ParamsIcon()
        );
    }

    public function __get($name)
    {
        return isset($this->_params[$name]) ? $this->_params[$name] : 0;
    }
}

/**
 * Class ParamsPageSize
 *
 * @property integer $album
 * @property integer $image
 * @property integer $video
 * @property integer $comment
 * @property integer $post
 * @property integer $friend
 * @property integer $top_video
 * @property integer $top_image
 * @property integer $top_user
 * @property integer $ld
 * @property integer $community_user
 * @property integer $community_index
 */
class ParamsPageSize
{
    private $_vars = array(
        'album' => 20,
        'image' => 20,
        'video' => 20,
        'comment' => 20,
        'post' => 20,
        'friend' => 20,
        'top_video' => 18,
        'top_image' => 18,
        'top_user' => 18,
        'ld' => 20,
        'community_user' => 20,
        'community_index' => 18
    );

    public function __get($name)
    {
        return isset($this->_vars[$name]) ? $this->_vars[$name] : 0;
    }
}

/**
 * Class ParamsCache
 *
 * @property integer $post
 * @property integer $listRate
 * @property integer $comment
 * @property integer $moderLog
 * @property integer $report
 * @property integer $friends
 * @property integer $eventComment
 * @property integer $eventNews
 * @property integer $albumAudio
 * @property integer $albumImage
 * @property integer $albumVideo
 * @property integer $image
 * @property integer $video
 * @property integer $subscribe
 * @property integer $community
 */
class ParamsCache
{
    private $_vars = array(
        'post' => 86400,
        'listRate' => 86400,
        'comment' => 86400,
        'moderLog' => 86400,
        'report' => 86400,
        'friends' => 86400,
        'eventComment' => 86400,
        'eventNews' => 86400,
        'albumAudio' => 86400,
        'albumImage' => 86400,
        'albumVideo' => 86400,
        'image' => 86400,
        'video' => 86400,
        'subscribe' => 86400,
        'community' => 86400,
    );

    public function __get($name)
    {
        return isset($this->_vars[$name]) ? $this->_vars[$name] : 86400;
    }
}

/**
 * Class ParamsIcon
 *
 * @property string $edit
 * @property string $delete
 * @property string $grid_delete
 * @property string $grid_accept
 * @property string $grid_view
 */
class ParamsIcon
{
    private $_vars = array(
        'edit' => 'edit16.png',
        'delete' => 'close16.png',
        'grid_delete' => 'no.png',
        'grid_accept' => 'ok.png',
        'grid_view' => 'album.png'
    );

    public function __get($name)
    {
        return isset($this->_vars[$name]) ? $this->_vars[$name] : null;
    }
}

