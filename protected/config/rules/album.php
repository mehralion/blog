<?php
/**
 * Created by PhpStorm.
 * User: Николай
 * Date: 01.02.14
 * Time: 21:03
 */

return array(
    //Album image
    '~<gameId:\d+>/profile/album/image/add' => array('/gallery/album/add_image'),
    '~<gameId:\d+>/profile/album/image/update_<album_id:\d+>' => array('/gallery/album/update_image'),
    '~<gameId:\d+>/profile/album/image/delete_<album_id:\d+>' => array('/gallery/album/delete_image'),
    '~<gameId:\d+>/profile/album/image' => array('/gallery/album/index_image'),
    '~<gameId:\d+>/profile/album/image/show_<album_id:\d+>' => array('/gallery/album/show_image'),

    '~<gameId:\d+>/show/album/image' => array('/user/show/album_image'), //Все альбомы
    '~<gameId:\d+>/show/album/image/show_<album_id:\d+>' => array('/user/show/show_image'), //Внутри альбома

    //Album Video
    '~<gameId:\d+>/profile/album/video/add' => array('/gallery/album/add_video'),
    '~<gameId:\d+>/profile/album/video/update_<album_id:\d+>' => array('/gallery/album/update_video'),
    '~<gameId:\d+>/profile/album/video/delete_<album_id:\d+>' => array('/gallery/album/delete_video'),
    '~<gameId:\d+>/profile/album/video' => array('/gallery/album/index_video'),
    '~<gameId:\d+>/profile/album/video/show_<album_id:\d+>' => array('/gallery/album/show_video'),

    '~<gameId:\d+>/show/album/video' => array('/user/show/album_video'), //Все альбомы
    '~<gameId:\d+>/show/album/video/show_<album_id:\d+>' => array('/user/show/show_video'), //Внутри альбома

    //Album audio
    '~<gameId:\d+>/profile/album/audio/add' => array('/gallery/album/add_audio'),
    '~<gameId:\d+>/profile/album/audio/update_<album_id:\d+>' => array('/gallery/album/update_audio'),
    '~<gameId:\d+>/profile/album/audio/delete_<album_id:\d+>' => array('/gallery/album/delete_audio'),
    '~<gameId:\d+>/profile/album/audio' => array('/gallery/album/index_audio'),
    '~<gameId:\d+>/profile/album/audio/show_<album_id:\d+>' => array('/gallery/album/show_audio'),

    '~<gameId:\d+>/show/album/audio' => array('/user/show/album_audio'), //Все альбомы
    '~<gameId:\d+>/show/album/audio/show_<album_id:\d+>' => array('/user/show/show_audio'),
);