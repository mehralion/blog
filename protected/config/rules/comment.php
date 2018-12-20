<?php
/**
 * Created by PhpStorm.
 * User: Николай
 * Date: 01.02.14
 * Time: 21:00
 */

return array(
    '~<gameId:\d+>/moder/comment_<id:\d+>/delete/post' => array('/moder/comment/delete_post'),
    '~<gameId:\d+>/moder/comment_<id:\d+>/delete/image' => array('/moder/comment/delete_image'),
    '~<gameId:\d+>/moder/comment_<id:\d+>/delete/video' => array('/moder/comment/delete_video'),
    '~<gameId:\d+>/moder/comment_<id:\d+>/delete/album/audio' => array('/moder/comment/delete_album_audio'),
    '~<gameId:\d+>/moder/comment_<id:\d+>/delete/community' => array('/moder/comment/delete_community'),

    '~<gameId:\d+>/moder/comment_<id:\d+>/accept/post' => array('/moder/comment/accept_post'),
    '~<gameId:\d+>/moder/comment_<id:\d+>/accept/image' => array('/moder/comment/accept_image'),
    '~<gameId:\d+>/moder/comment_<id:\d+>/accept/video' => array('/moder/comment/accept_video'),
    '~<gameId:\d+>/moder/comment_<id:\d+>/accept/album/audio' => array('/moder/comment/accept_album_audio'),
    '~<gameId:\d+>/moder/comment_<id:\d+>/accept/community' => array('/moder/comment/accept_community'),

    '~<gameId:\d+>/moder/comment_<id:\d+>/restore/post' => array('/moder/comment/restore_post'),
    '~<gameId:\d+>/moder/comment_<id:\d+>/restore/image' => array('/moder/comment/restore_image'),
    '~<gameId:\d+>/moder/comment_<id:\d+>/restore/video' => array('/moder/comment/restore_video'),
    '~<gameId:\d+>/moder/comment_<id:\d+>/restore/album/audio' => array('/moder/comment/restore_album_audio'),
    '~<gameId:\d+>/moder/comment_<id:\d+>/restore/community' => array('/moder/comment/restore_community'),

    '~<gameId:\d+>/moder/comment_<id:\d+>/report/post' => array('/moder/report/comment_post'),
    '~<gameId:\d+>/moder/comment_<id:\d+>/report/image' => array('/moder/report/comment_image'),
    '~<gameId:\d+>/moder/comment_<id:\d+>/report/video' => array('/moder/report/comment_video'),
    '~<gameId:\d+>/moder/comment_<id:\d+>/report/album/audio' => array('/moder/report/comment_album_audio'),
    '~<gameId:\d+>/moder/comment_<id:\d+>/report/community' => array('/moder/report/comment_community'),
);