<?php
namespace application\modules\gallery\actions\image;
use application\modules\gallery\components\GalleryAction;

/**
 * Created by JetBrains PhpStorm.
 * User: Николай
 * Date: 13.06.13
 * Time: 13:13
 * To change this template use File | Settings | File Templates.
 *
 * @package application.gallery.actions.image
 */
class Update extends GalleryAction
{
    public $viewName = 'form_update';

    public function run()
    {
        if($this->isCommunity && !\Yii::app()->community->inCommunity()) {
            \Yii::app()->message->setErrors('danger', 'Вы не состоите в сообществе этой фотографии');
            \Yii::app()->message->showMessage();
        }

        $criteria = new \CDbCriteria();
        $criteria->addCondition('`t`.id = :id');
        $criteria->with = array(
            'album' => array(
                'scopes' => array('deletedStatus', 'moderDeletedStatus', 'truncatedStatus'),
                'params' => array(
                    ':deletedStatus' => 0,
                    ':moderDeletedStatus' => 0,
                    ':truncatedStatus' => 0
                )
            )
        );
        $criteria->scopes = array(
            'own',
            'activatedStatus',
            'deletedStatus',
            'moderDeletedStatus',
            'truncatedStatus',
        );
        $criteria->params = array(
            ':id' => \Yii::app()->request->getParam('id'),
            ':activatedStatus' => 1,
            ':deletedStatus' => 0,
            ':moderDeletedStatus' => 0,
            ':truncatedStatus' => 0,
        );
        /** @var \GalleryImage $Image */
        $Image = \GalleryImage::model()->find($criteria);
        if(!isset($Image)) {
            \Yii::app()->message->setErrors('danger', 'Фотография не найдена');
            \Yii::app()->message->showMessage();
        }

        $Image->scenario = 'updateImage';
        $post = \Yii::app()->request->getParam('GalleryImage');
        if(!empty($post)) {
            if(isset($post['album_id']) && $post['album_id'] != $Image->album_id) {
                $criteria = new \CDbCriteria();
                $criteria->scopes = array(
                    'own',
                    'activatedStatus',
                    'deletedStatus',
                    'moderDeletedStatus',
                );
                $criteria->addCondition('`t`.id = :album_id');
                $criteria->params = array(
                    ':activatedStatus' => 1,
                    ':deletedStatus' => 0,
                    ':moderDeletedStatus' => 0,
                    ':album_id' => $post['album_id'],
                );
                /** @var \GalleryAlbumImage $Album */
                $Album = \GalleryAlbumImage::model()->find($criteria);
                if(!$Album)
                    $post['album_id'] = $Image->album_id;
                else {
                    $Image->view_role = $Album->view_role;
                    $Image->community_alias = $Album->community_alias;
                }
            }

            $Image->attributes = $post;
            $Image->user_update_datetime = \DateTimeFormat::format();
            if($Image->mUpdate())
                \Yii::app()->message->setText('success', 'Фотография обновлена');
            else
                \Yii::app()->message->setErrors('danger', $Image);

            \Yii::app()->message->url = \Yii::app()->request->getUrlReferrer();
            \Yii::app()->message->showMessage();
        } else {
            $this->controller->renderPartial($this->viewName, array(
                'model' => $Image,
            ), false, true);
        }
    }
}