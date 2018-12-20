<?php
namespace application\modules\gallery\actions\album\video;
use application\modules\gallery\components\GalleryAction;

/**
 * Created by JetBrains PhpStorm.
 * User: Николай
 * Date: 13.06.13
 * Time: 13:13
 * To change this template use File | Settings | File Templates.
 *
 * @package application.gallery.actions.album
 */
class Show extends GalleryAction
{
    public $viewName = 'video/show';

    public function run()
    {
        $criteriaType = new \CDbCriteria();
        if($this->isCommunity) {
            $criteriaType->addCondition('`t`.community_id = :community_id');
            $criteriaType->params = array(':community_id' => \Yii::app()->community->id);
        } else {
            $criteriaType->addCondition('`t`.user_id = :user_id');
            $criteriaType->params = array(':user_id' => $this->userId);
        }

        $criteria = new \CDbCriteria();
        $criteria->addCondition('`t`.id = :id');
        $criteria->params = array(':id' => \Yii::app()->request->getParam('album_id'));
        $criteria->mergeWith($criteriaType);
        $criteria->mergeWith(\Yii::app()->access->GetCriteriaAccess());

        /** @var \GalleryAlbumVideo $Album */
        $Album = \GalleryAlbumVideo::model()->find($criteria);
        if(!isset($Album)) {
            \Yii::app()->message->setErrors('danger', 'Альбом не найден');
            \Yii::app()->message->showMessage();
        }

        $criteria = new \CDbCriteria();
        $criteria->addCondition('`t`.album_id = :a_id');
        $criteria->scopes = array(
            'activatedStatus',
            'deletedStatus',
            'moderDeletedStatus',
            'truncatedStatus'
        );
        $criteria->params = array(
            ':activatedStatus' => 1,
            ':deletedStatus' => 0,
            ':moderDeletedStatus' => 0,
            ':truncatedStatus' => 0,
            ':a_id' => $Album->id,
        );
        $criteria->mergeWith($criteriaType);
        $criteria->mergeWith(\Yii::app()->access->GetCriteriaAccess());

        $dependency = new \CDbCacheDependency('SELECT MAX(update_datetime) FROM {{cache_event_item}} where item_type = :item_type and album_id = :album_id');
        $dependency->params = array(
            ':item_type' => \ItemTypes::ITEM_TYPE_VIDEO,
            ':album_id' => $Album->id
        );
        $dependency->reuseDependentData = true;

        $pages = new \CPagination(\GalleryVideo::model()
            ->cache(\Yii::app()->paramsWrap->cache->albumVideo, $dependency)
            ->count($criteria));
        $pages->pageSize = \Yii::app()->paramsWrap->pageSize->video;
        $pages->applyLimit($criteria);

        if(!$Album->is_croped)
            \Yii::app()->clientScript->registerPackage('jcrop');
        \Yii::app()->clientScript->registerPackage('video');

        $models = \GalleryVideo::model()->cache(\Yii::app()->paramsWrap->cache->albumVideo, $dependency)->findAll($criteria);

        $this->controller->render($this->viewName, array(
            'models' => $models,
            'pages' => $pages,
            'album' => $Album
        ));
    }
}