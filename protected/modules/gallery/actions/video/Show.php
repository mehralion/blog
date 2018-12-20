<?php
namespace application\modules\gallery\actions\video;
use application\modules\gallery\components\GalleryAction;

/**
 * Created by JetBrains PhpStorm.
 * User: Николай
 * Date: 13.06.13
 * Time: 13:13
 * To change this template use File | Settings | File Templates.
 *
 * @package application.gallery.actions.video
 */
class Show extends GalleryAction
{
    public $viewName = 'show';

    public function run()
    {
        $criteriaType = new \CDbCriteria();
        if($this->isCommunity) {
            $criteriaType->addCondition('`t`.community_id = :community_id');
            $criteriaType->params = array(':community_id' => \Yii::app()->community->id);
        }

        $criteria = new \CDbCriteria();
        $criteria->scopes = array(
            'truncatedStatus',
        );
        $criteria->addCondition('`t`.is_deleted = 0 or (`t`.is_deleted = 1 and `t`.user_id = :user_id)');
        $criteria->addCondition('`t`.is_moder_deleted = 0 or (`t`.is_moder_deleted = 1 and `t`.user_id = :user_id)');
        $criteria->params = array(
            ':user_id' => \Yii::app()->user->id,
            ':truncatedStatus' => 0,
        );

        $criteria->with = array(
            'user',
            'canRate',
            'album',
            'info' => array(
                'scopes' => array('deletedStatus', 'truncatedStatus', 'moderDeletedStatus'),
                'params' => array(':deletedStatus' => 0, ':truncatedStatus' => 0, ':moderDeletedStatus' => 0),
            )
        );
        $criteria->mergeWith($criteriaType);
        $criteria->mergeWith(\Yii::app()->access->GetCriteriaAccess());

        $criteriaAll = clone $criteria;
        $criteria->addCondition('`t`.id = :id');
        $criteria->params = \CMap::mergeArray($criteria->params, array(':id' => \Yii::app()->request->getParam('id')));

        $dependency = new \CDbCacheDependency('SELECT MAX(update_datetime) FROM {{cache_event_item}} where item_type = :item_type');
        $dependency->params = array(':item_type' => \ItemTypes::ITEM_TYPE_VIDEO);

        /** @var \GalleryVideo $Video */
        $Video = \GalleryVideo::model()->cache(\Yii::app()->paramsWrap->cache->video, $dependency)->find($criteria);
        if(!isset($Video)) {
            \Yii::app()->message->setErrors('danger', 'Видеозапись не найдена');
            \Yii::app()->message->showMessage();
        }

        $criteriaAll->addCondition('`t`.album_id = :album_id');
        $criteriaAll->scopes = \CMap::mergeArray($criteriaAll->scopes, array(
            'deletedStatus',
            'moderDeletedStatus'
        ));
        $criteriaAll->params = \CMap::mergeArray($criteriaAll->params, array(
            ':album_id' => $Video->album_id,
            ':deletedStatus' => 0,
            ':moderDeletedStatus' => 0,
        ));

        $dependency = new \CDbCacheDependency('SELECT max(update_datetime) FROM {{cache_event_item}} where album_id = :album_id and item_type = :item_type');
        $dependency->params = array(
            ':album_id' => $Video->album_id,
            ':item_type' => \ItemTypes::ITEM_TYPE_VIDEO
        );

        /** @var \GalleryVideo $Video */
        $Videos = \GalleryVideo::model()->cache(\Yii::app()->paramsWrap->cache->video, $dependency)->findAll($criteriaAll);

        $this->controller->render($this->viewName, array(
            'model' => $Video,
            'videos' => $Videos
        ));
    }
}