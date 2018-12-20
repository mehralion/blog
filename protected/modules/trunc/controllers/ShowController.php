<?php
namespace application\modules\trunc\controllers;

/**
 * Class AlbumController
 *
 * @package application.gallery.controllers
 *
 */
class ShowController extends \FrontController
{
    public function filters()
    {
        return array(
            'accessControl',
            //'ajaxOnly',
        );
    }

    public function accessRules()
    {
        return array(
            array('deny',
                'actions'=>array('post', 'audio', 'image', 'video', 'comment', 'community'),
                'users' => array('?'),
            )
        );
    }


    public function actionPost()
    {
        $criteria = new \CDbCriteria();
        $criteria->addCondition('`t`.is_deleted = 1 or `t`.is_moder_deleted = 1');
        $criteria->scopes = array(
            'own',
            'truncatedStatus',
            'notCommunity',
        );
        $criteria->params = \CMap::mergeArray($criteria->params, array(
            ':truncatedStatus' => 0,
        ));
        $criteria->with =  array(
            'user' => array('with' => array('userProfile')),
        );

        $pages = new \CPagination(\Post::model()->count($criteria));
        $pages->pageSize = \Yii::app()->paramsWrap->pageSize->post;
        $pages->applyLimit($criteria);

        $models = \Post::model()->findAll($criteria);

        $this->render('post', array(
            'models' => $models,
            'pages' => $pages
        ));
    }

    public function actionAudio()
    {
        $criteria = new \CDbCriteria();
        $criteria->addCondition('`t`.is_deleted = 1 or `t`.is_moder_deleted = 1');
        $criteria->scopes = array(
            'own',
            'truncatedStatus',
            'notCommunity',
        );
        $criteria->params = \CMap::mergeArray($criteria->params, array(
            ':truncatedStatus' => 0,
        ));
        $criteria->with =  array(
            'user' => array('with' => array('userProfile')),
        );

        $pages = new \CPagination(\GalleryAudio::model()->count($criteria));
        $pages->pageSize = \Yii::app()->paramsWrap->pageSize->post;
        $pages->applyLimit($criteria);

        $models = \GalleryAudio::model()->findAll($criteria);

        $this->render('audio', array(
            'models' => $models,
            'pages' => $pages
        ));
    }

    public function actionVideo()
    {
        $criteria = new \CDbCriteria();
        $criteria->addCondition('`t`.is_deleted = 1 or `t`.is_moder_deleted = 1');
        $criteria->scopes = array(
            'own',
            'truncatedStatus',
            'notCommunity',
        );
        $criteria->params = \CMap::mergeArray($criteria->params, array(
            ':truncatedStatus' => 0,
        ));
        $criteria->with =  array(
            'user' => array('with' => array('userProfile')),
        );

        $pages = new \CPagination(\GalleryVideo::model()->count($criteria));
        $pages->pageSize = \Yii::app()->paramsWrap->pageSize->video;
        $pages->applyLimit($criteria);

        $models = \GalleryVideo::model()->findAll($criteria);

        $this->render('video', array(
            'models' => $models,
            'pages' => $pages
        ));
    }

    public function actionImage()
    {
        $criteria = new \CDbCriteria();
        $criteria->addCondition('`t`.is_deleted = 1 or `t`.is_moder_deleted = 1');
        $criteria->scopes = array(
            'own',
            'truncatedStatus',
            'notCommunity',
        );
        $criteria->params = \CMap::mergeArray($criteria->params, array(
            ':truncatedStatus' => 0,
        ));
        $criteria->with =  array(
            'user' => array('with' => array('userProfile')),
        );

        $pages = new \CPagination(\GalleryImage::model()->count($criteria));
        $pages->pageSize = \Yii::app()->paramsWrap->pageSize->image;
        $pages->applyLimit($criteria);

        $models = \GalleryImage::model()->findAll($criteria);

        $this->render('image', array(
            'models' => $models,
            'pages' => $pages
        ));
    }

    public function actionComment()
    {
        $criteria = new \CDbCriteria();
        $criteria->addCondition('`t`.is_deleted = 1 or `t`.is_moder_deleted = 1');
        $criteria->addCondition('`t`.user_id = :user_id or `t`.user_deleted_id = :user_id');
        $criteria->addCondition('`info`.is_community = 0');
        $criteria->scopes = array('truncatedStatus');
        $criteria->params = array(
            ':user_id' => \Yii::app()->user->id,
            ':truncatedStatus' => 0,
        );
        $criteria->with = \CMap::mergeArray($criteria->with, array(
            'moderLog',
            'report',
            'userDeleted',
            'user' => array('with' => array('userProfile')),
            'info',
        ));

        $pages = new \CPagination(\CommentItem::model()->count($criteria));
        $pages->pageSize = \Yii::app()->paramsWrap->pageSize->comment;
        $pages->applyLimit($criteria);

        $models = \CommentItem::model()->findAll($criteria);

        $this->render('comment', array(
            'models' => $models,
            'pages' => $pages
        ));
    }

    public function actionCommunity()
    {
        $criteria = new \CDbCriteria();
        $criteria->addCondition('`t`.is_deleted = 1 or `t`.is_moder_deleted = 1');
        $criteria->scopes = array(
            'own',
            'truncatedStatus',
        );
        $criteria->params = \CMap::mergeArray($criteria->params, array(
            ':truncatedStatus' => 0,
        ));
        $criteria->with =  array(
            'user' => array('with' => array('userProfile')),
        );

        $pages = new \CPagination(\Post::model()->count($criteria));
        $pages->pageSize = \Yii::app()->paramsWrap->pageSize->post;
        $pages->applyLimit($criteria);

        $models = \Community::model()->findAll($criteria);

        $this->render('community', array(
            'models' => $models,
            'pages' => $pages
        ));
    }
}