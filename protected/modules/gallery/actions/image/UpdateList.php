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
class UpdateList extends \CAction
{
    public function run()
    {
        $post = \Yii::app()->request->getParam('GalleryImage', array());
        $albumId = null;
        foreach($post as $item) {
            if(!isset($item['id']))
                continue;
            $criteria = new \CDbCriteria();
            $criteria->addCondition('`t`.id = :id');
            $criteria->scopes = array(
                'own',
                'activatedStatus',
                'deletedStatus',
                'moderDeletedStatus',
                'truncatedStatus',
            );
            $criteria->params = array(
                ':id' => $item['id'],
                ':activatedStatus' => 1,
                ':deletedStatus' => 0,
                ':moderDeletedStatus' => 0,
                ':truncatedStatus' => 0,
            );
            /** @var \GalleryImage $Image */
            $Image = \GalleryImage::model()->find($criteria);
            if(!isset($Image))
                continue;
            $Image->scenario = 'updateImage';
            $albumId = $Image->album_id;
            $Image->attributes = $item;
            $Image->is_completed = true;
            $Image->user_update_datetime = \DateTimeFormat::format();
            $Image->mUpdate();
        }
        $this->controller->redirect(\Yii::app()->createUrl('/gallery/image/index', array(
            'id' => $albumId,
            'gameId' => \Yii::app()->user->getGameId()
        )));
    }
}