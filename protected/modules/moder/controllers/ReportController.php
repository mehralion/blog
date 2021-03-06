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
class ReportController extends \FrontController
{
    public function filters()
    {
        return array(
            'accessControl',
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
            'comment'                => '\application\modules\moder\actions\report\Comment',
            'image'                  => '\application\modules\moder\actions\report\Image',
            'video'                  => '\application\modules\moder\actions\report\Video',
            'post'                   => '\application\modules\moder\actions\report\Post',
            'audio'                  => '\application\modules\moder\actions\report\AudioAlbum',
            'community'              => '\application\modules\moder\actions\report\Community',
            'comment_post'           => '\application\modules\moder\actions\report\comment\Post',
            'comment_image'          => '\application\modules\moder\actions\report\comment\Image',
            'comment_video'          => '\application\modules\moder\actions\report\comment\Video',
            'comment_community'      => '\application\modules\moder\actions\report\comment\Community',
            'comment_album_audio'    => '\application\modules\moder\actions\report\comment\AudioAlbum',
        );
    }
}