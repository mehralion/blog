<?php
namespace application\modules\gallery\actions\video;
/**
 * Created by JetBrains PhpStorm.
 * User: Николай
 * Date: 13.06.13
 * Time: 13:13
 * To change this template use File | Settings | File Templates.
 *
 * @package application.gallery.actions.video
 */
class Top extends \CAction
{
    public function run()
    {
        $criteria = new \CDbCriteria();
        $criteria->with = array(
            'user',
            'canRate',
            'info' => array(
                'scopes' => array('deletedStatus', 'truncatedStatus', 'moderDeletedStatus'),
                'params' => array(':deletedStatus' => 0, ':truncatedStatus' => 0, ':moderDeletedStatus' => 0),
            )
        );
        $criteria->order = '`t`.rating desc, `t`.create_datetime desc';
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
            ':truncatedStatus' => 0
        );

        $dependency = new \CDbCacheDependency('SELECT MAX(update_datetime) FROM {{cache_event_item}} where item_type = :item_type');
        $dependency->params = array(':item_type' => \ItemTypes::ITEM_TYPE_VIDEO);
        $dependency->reuseDependentData = true;

        $pages = new \CPagination(\GalleryVideo::model()
            ->public()
            ->cache(\Yii::app()->paramsWrap->cache->video, $dependency)
            ->count($criteria));
        $pages->pageSize = \Yii::app()->paramsWrap->pageSize->video;
        $pages->applyLimit($criteria);

        $GalleryVideo = \GalleryVideo::model()
            ->public()
            ->cache(\Yii::app()->paramsWrap->cache->video, $dependency)
            ->findAll($criteria);
        $this->controller->render('top', array(
            'models' => $GalleryVideo,
            'pages' => $pages
        ));
    }
}