<?php
namespace application\modules\gallery\actions\image;
/**
 * Created by JetBrains PhpStorm.
 * User: Николай
 * Date: 13.06.13
 * Time: 13:13
 * To change this template use File | Settings | File Templates.
 *
 * @package application.gallery.actions.image
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
            'truncatedStatus',
        );
        $criteria->params = array(
            ':activatedStatus' => 1,
            ':deletedStatus' => 0,
            ':moderDeletedStatus' => 0,
            ':truncatedStatus' => 0
        );

        $dependency = new \CDbCacheDependency('SELECT MAX(update_datetime) FROM {{cache_event_item}} where item_type = :item_type');
        $dependency->params = array(':item_type' => \ItemTypes::ITEM_TYPE_IMAGE);
        $dependency->reuseDependentData = true;

        $pages = new \CPagination(\GalleryImage::model()
            ->public()
            ->cache(\Yii::app()->paramsWrap->cache->image, $dependency)
            ->count($criteria));
        $pages->pageSize = \Yii::app()->paramsWrap->pageSize->top_image;
        $pages->applyLimit($criteria);

        $GalleryImage = \GalleryImage::model()
            ->public()
            ->cache(\Yii::app()->paramsWrap->cache->image, $dependency)
            ->findAll($criteria);
        $this->controller->render('top', array(
            'models' => $GalleryImage,
            'pages' => $pages
        ));
    }
}