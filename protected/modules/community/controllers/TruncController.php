<?php
namespace application\modules\community\controllers;

/**
 * Class AlbumController
 *
 * @package application.gallery.controllers
 *
 */
class TruncController extends \FrontController
{
    public $layout = 'community/trunc';

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
                'actions'=>array('post', 'audio', 'image', 'video', 'comment'),
                'users' => array('?'),
            )
        );
    }


    public function actionPost()
    {
        $criteria = new \CDbCriteria();
        $criteria->addCondition('`t`.is_deleted = 1 or `t`.is_moder_deleted = 1');
        $criteria->addCondition('`t`.community_id = :community_id');
        $criteria->scopes = array(
            'truncatedStatus',
        );
        $criteria->params = \CMap::mergeArray($criteria->params, array(
            ':truncatedStatus' => 0,
            ':community_id' => \Yii::app()->community->id,
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
        $criteria->addCondition('`t`.community_id = :community_id');
        $criteria->scopes = array(
            'truncatedStatus',
        );
        $criteria->params = \CMap::mergeArray($criteria->params, array(
            ':truncatedStatus' => 0,
            ':community_id' => \Yii::app()->community->id
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
        $criteria->addCondition('`t`.community_id = :community_id');
        $criteria->scopes = array(
            'truncatedStatus',
        );
        $criteria->params = \CMap::mergeArray($criteria->params, array(
            ':truncatedStatus' => 0,
            ':community_id' => \Yii::app()->community->id
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
        $criteria->addCondition('`t`.community_id = :community_id');
        $criteria->scopes = array(
            'truncatedStatus',
        );
        $criteria->params = \CMap::mergeArray($criteria->params, array(
            ':truncatedStatus' => 0,
            ':community_id' => \Yii::app()->community->id
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
        $criteria->addCondition('`info`.is_community = 1');
        $criteria->scopes = array('truncatedStatus');
        $criteria->params = array(
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

    public function actionDelete()
    {
        $t = \Yii::app()->db->beginTransaction();
        try {
            $attributes = array(
                'deleted_trunc' => 1,
                'update_datetime' => \DateTimeFormat::format()
            );
            $conditionComments = '(info.is_community = 1 and info.community_id = :community_id and (t.user_deleted_id = :user_id or t.user_id = :user_id) and t.is_deleted = 1 and t.deleted_trunc = 0)';
            $conditionItems = 'is_community = 1 and community_id = :community_id and user_id = :user_id and (is_deleted = 1 or is_moder_deleted = 1) and deleted_trunc = 0';
            $params = array(':user_id' => \Yii::app()->user->id, ':community_id' => \Yii::app()->community->id);

            $idsPots = array();
            $idsImage = array();
            $idsAudioAlbum = array();
            $idsVideo = array();
            $idsCommunity = array();

            /** @var \CommentItem[] $Comments */
            $Comments = \CommentItem::model()->findAll($conditionComments, $params);
            foreach($Comments as $model) {
                switch ($model->item_type) {
                    case \ItemTypes::ITEM_TYPE_AUDIO_ALBUM:
                        $idsAudioAlbum[] = $model->item_id;
                        break;

                    case \ItemTypes::ITEM_TYPE_IMAGE:
                        $idsImage[] = $model->item_id;
                        break;

                    case \ItemTypes::ITEM_TYPE_POST:
                        $idsPots[] = $model->item_id;
                        break;

                    case \ItemTypes::ITEM_TYPE_VIDEO:
                        $idsVideo[] = $model->item_id;
                        break;
                }
            }

            /** @var \Post[] $Posts */
            $Posts = \Post::model()->findAll($conditionItems, $params);
            foreach($Posts as $model) {
                if(!in_array($model->id, $idsPots))
                    $idsPots[] = $model->id;
            }

            /** @var \GalleryImage[] $Images */
            $Images = \GalleryImage::model()->findAll($conditionItems, $params);
            foreach($Images as $model) {
                if(!in_array($model->id, $idsImage))
                    $idsImage[] = $model->id;
            }

            /** @var \GalleryVideo[] $Videos */
            $Videos = \GalleryVideo::model()->findAll($conditionItems, $params);
            foreach($Videos as $model) {
                if(!in_array($model->id, $idsVideo))
                    $idsVideo[] = $model->id;
            }

            /** @var \GalleryAudio[] $Audio */
            $Audio = \GalleryAudio::model()->findAll($conditionItems, $params);
            foreach($Audio as $model) {
                if(!in_array($model->id, $idsAudioAlbum))
                    $idsAudioAlbum[] = $model->album_id;
            }

            /** @var \Community[] $Community */
            $Community = \Community::model()->with('info')->findAll($conditionItems, $params);
            foreach($Community as $model) {
                if(!in_array($model->id, $idsCommunity))
                    $idsCommunity[] = $model->id;
            }


            \CommentItem::model()->updateAll($attributes, $conditionComments, $params);
            \GalleryImage::model()->updateAll($attributes, $conditionItems, $params);
            \GalleryVideo::model()->updateAll($attributes, $conditionItems, $params);
            \GalleryAudio::model()->updateAll($attributes, $conditionItems, $params);
            \Post::model()->updateAll($attributes, $conditionItems, $params);
            \Community::model()->with('info')->updateAll($attributes, $conditionItems, $params);

            if(!empty($idsPots))
                \CacheEventItemPost::model()->updateAll(
                    array('update_datetime' => \DateTimeFormat::format(\Yii::app()->params['dbTimeFormat'])),
                    'item_id in(:in) and item_type = :item_type',
                    array(':in' => "'".implode("','",$idsPots)."'", ':item_type' => \ItemTypes::ITEM_TYPE_POST)
                );
            if(!empty($idsImage))
                \CacheEventItemImage::model()->updateAll(
                    array('update_datetime' => \DateTimeFormat::format(\Yii::app()->params['dbTimeFormat'])),
                    'item_id in(:in) and item_type = :item_type',
                    array(':in' => "'".implode("','",$idsImage)."'", ':item_type' => \ItemTypes::ITEM_TYPE_IMAGE)
                );
            if(!empty($idsVideo))
                \CacheEventItemVideo::model()->updateAll(
                    array('update_datetime' => \DateTimeFormat::format(\Yii::app()->params['dbTimeFormat'])),
                    'item_id in(:in) and item_type = :item_type',
                    array(':in' => "'".implode("','",$idsVideo)."'", ':item_type' => \ItemTypes::ITEM_TYPE_VIDEO)
                );
            if(!empty($idsAudioAlbum))
                \CacheEventItemAudio::model()->updateAll(
                    array('update_datetime' => \DateTimeFormat::format(\Yii::app()->params['dbTimeFormat'])),
                    'album_id in(:in) and item_type = :item_type',
                    array(':in' => "'".implode("','",$idsAudioAlbum)."'", ':item_type' => \ItemTypes::ITEM_TYPE_AUDIO_ALBUM)
                );
            if(!empty($idsCommunity))
                \CacheEventItemCommunity::model()->updateAll(
                    array('update_datetime' => \DateTimeFormat::format(\Yii::app()->params['dbTimeFormat'])),
                    'item_id in(:in) and item_type = :item_type',
                    array(':in' => "'".implode("','",$idsCommunity)."'", ':item_type' => \ItemTypes::ITEM_TYPE_COMMUNITY)
                );

            $t->commit();
            \Yii::app()->message->setText('success', 'Корзина очищена');
        } catch (\Exception $ex) {
            $t->rollback();
            \MyException::log($ex);
        }

        $this->redirect(\Yii::app()->request->getUrlReferrer());
    }
}