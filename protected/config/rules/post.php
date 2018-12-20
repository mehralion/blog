<?php
/**
 * Created by PhpStorm.
 * User: Николай
 * Date: 01.02.14
 * Time: 21:03
 */

return array(
    //Posts
    '~<gameId:\d+>/profile/posts' => array('/post/profile/index'),
    '~<gameId:\d+>/show/posts' => array('/user/show/posts'),

    //Post
    '~<gameId:\d+>/post_<id:\d+>_<comment_id:\d+>' => array('/post/index/show'),
    '~<gameId:\d+>/post_<id:\d+>' => array('/post/index/show'),
);