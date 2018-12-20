<?php
namespace application\modules\moder\controllers;
/**
 * Created by JetBrains PhpStorm.
 * User: Nick Nikitchenko
 * Skype: quietasice
 * E-mail: quietasice123@gmail.com
 * Date: 15.07.13
 * Time: 17:42
 * To change this template use File | Settings | File Templates.
 *
 * @package application.moder.controllers
 */
class CommentController extends \FrontController
{
    public function filters()
    {
        return array(
            'accessControl',
            'ajaxOnly + delete',
        );
    }

    public function accessRules()
    {
        return array(
            array('deny',
                'users' => array('?'),
            )
        );
    }

    public function actions()
    {
        return array(
            'reset'                  => '\application\modules\moder\actions\comment\Reset',
            'index'                  => '\application\modules\moder\actions\comment\Index',
            'restore'                => '\application\modules\moder\actions\comment\Restore',
            'delete_post'            => '\application\modules\moder\actions\comment\delete\Post',
            'delete_image'           => '\application\modules\moder\actions\comment\delete\Image',
            'delete_video'           => '\application\modules\moder\actions\comment\delete\Video',
            'delete_album_audio'     => '\application\modules\moder\actions\comment\delete\AudioAlbum',
            'delete_community'       => '\application\modules\moder\actions\comment\delete\Community',
            'accept_post'            => '\application\modules\moder\actions\comment\accept\Post',
            'accept_image'           => '\application\modules\moder\actions\comment\accept\Image',
            'accept_video'           => '\application\modules\moder\actions\comment\accept\Video',
            'accept_album_audio'     => '\application\modules\moder\actions\comment\accept\AudioAlbum',
            'accept_community'       => '\application\modules\moder\actions\comment\accept\Community',
            'restore_post'           => '\application\modules\moder\actions\comment\restore\Post',
            'restore_image'          => '\application\modules\moder\actions\comment\restore\Image',
            'restore_video'          => '\application\modules\moder\actions\comment\restore\Video',
            'restore_album_audio'    => '\application\modules\moder\actions\comment\restore\AudioAlbum',
            'restore_community'      => '\application\modules\moder\actions\comment\restore\Community',
        );
    }
}