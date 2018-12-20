<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Николай
 * Date: 17.09.13
 * Time: 17:21
 * To change this template use File | Settings | File Templates.
 */
$comment = require(dirname(__FILE__).'/rules/comment.php');
$community = require(dirname(__FILE__).'/rules/community.php');
$album = require(dirname(__FILE__).'/rules/album.php');
$post = require(dirname(__FILE__).'/rules/post.php');
$image = require(dirname(__FILE__).'/rules/image.php');
$video = require(dirname(__FILE__).'/rules/video.php');
$audio = require(dirname(__FILE__).'/rules/audio.php');
$blog = require(dirname(__FILE__).'/rules/blog.php');
$other = require(dirname(__FILE__).'/rules/other.php');
$radio = require(dirname(__FILE__).'/rules/radio.php');

$return = array();
$return = CMap::mergeArray($return, $comment);
$return = CMap::mergeArray($return, $community);
$return = CMap::mergeArray($return, $album);
$return = CMap::mergeArray($return, $post);
$return = CMap::mergeArray($return, $image);
$return = CMap::mergeArray($return, $video);
$return = CMap::mergeArray($return, $audio);
$return = CMap::mergeArray($return, $blog);
$return = CMap::mergeArray($return, $radio);
$return = CMap::mergeArray($return, $other);

return $return;