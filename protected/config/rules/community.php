<?php
/**
 * Created by PhpStorm.
 * User: Николай
 * Date: 01.02.14
 * Time: 21:02
 */

return array(
    //Community
    'community/index' => array('/community/request/index'),
    'community/index_<category_id:\d+>' => array('/community/request/list'),
    'community/~<community_alias:\w+>/' => array('/community/request/show', 'urlSuffix' => '/'),
    'community/~<community_alias:\w+>/users/index' => array('/community/users/index'),
    'community/~<community_alias:\w+>/users/moders' => array('/community/users/moders'),
    'community/~<community_alias:\w+>/users/request' => array('/community/users/request'),
    'community/~<community_alias:\w+>/users/invite' => array('/community/users/invite'),
    'community/~<community_alias:\w+>/users/delete/user_<user_id:\d+>' => array('/community/users/delete_user'),
    'community/~<community_alias:\w+>/users/delete/moder_<user_id:\d+>' => array('/community/users/delete_moder'),
    'community/~<community_alias:\w+>/users/delete/request_<user_id:\d+>' => array('/community/users/delete_request'),
    'community/~<community_alias:\w+>/users/delete/invite_<user_id:\d+>' => array('/community/users/delete_invite'),
    'community/~<community_alias:\w+>/users/accept/request_<user_id:\d+>' => array('/community/users/accept_request'),
    'community/~<community_alias:\w+>/report' => array('/moder/report/community'),
    'community/~<community_alias:\w+>/rating' => array('/rating/community/add'),
    'community/~<community_alias:\w+>/trunc/<_a>' => array('/community/trunc/<_a>'),

    //Image
    'community/~<community_alias:\w+>/album/image' => array('/community/album/image'),
    'community/~<community_alias:\w+>/album_<album_id:\d+>/image/show' => array('/community/album/image_show'),
    'community/~<community_alias:\w+>/album/image/add' => array('/community/album/image_add'),
    'community/~<community_alias:\w+>/album/image/update' => array('/community/album/image_update'),
    'community/~<community_alias:\w+>/album/image/delete' => array('/community/album/image_delete'),
    'community/~<community_alias:\w+>/image/<_a>_<id:\d+>' => array('/community/image/<_a>'),

    //Video
    'community/~<community_alias:\w+>/album/video' => array('/community/album/video'),
    'community/~<community_alias:\w+>/album_<album_id:\d+>/video/show' => array('/community/album/video_show'),
    'community/~<community_alias:\w+>/album/video/add' => array('/community/album/video_add'),
    'community/~<community_alias:\w+>/album/video/update' => array('/community/album/video_update'),
    'community/~<community_alias:\w+>/album/video/delete' => array('/community/album/video_delete'),
    'community/~<community_alias:\w+>/video/<_a>_<id:\d+>' => array('/community/video/<_a>'),

    //Audio
    'community/~<community_alias:\w+>/album/audio' => array('/community/album/audio'),
    'community/~<community_alias:\w+>/album_<album_id:\d+>/audio/show' => array('/community/album/audio_show'),
    'community/~<community_alias:\w+>/album/audio/add' => array('/community/album/audio_add'),
    'community/~<community_alias:\w+>/album/audio/update' => array('/community/album/audio_update'),
    'community/~<community_alias:\w+>/album/audio/delete' => array('/community/album/audio_delete'),

    //Post
    'community/~<community_alias:\w+>/post/<_a>' => array('/community/post/<_a>'),
    'community/~<community_alias:\w+>/<_a>' => array('/community/request/<_a>'),

    //event
    'community/~<community_alias:\w+>/event/news/<_a>' => array('/event/news/<_a>'),
    'community/~<community_alias:\w+>/event/comment/<_a>' => array('/event/comment/<_a>'),

    //rating
    'community/~<community_alias:\w+>/rating/comment/add' => array('/community/rating/comment_add'),
    'community/~<community_alias:\w+>/rating/video/add' => array('/community/rating/video_add'),
    'community/~<community_alias:\w+>/rating/audio/add' => array('/community/rating/audio_add'),
    'community/~<community_alias:\w+>/rating/image/add' => array('/community/rating/image_add'),
    'community/~<community_alias:\w+>/rating/post/add' => array('/community/rating/post_add'),
);