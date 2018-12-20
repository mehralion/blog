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
class Update extends GalleryAction
{
    public $viewName = 'form';

    public function run()
    {
        if($this->isCommunity && !\Yii::app()->community->inCommunity()) {
            \Yii::app()->message->setErrors('danger', 'Вы не состоите в сообществе этой видеозаписи');
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
            'truncatedStatus'
        );
        $criteria->params = array(
            ':id' => \Yii::app()->request->getParam('id'),
            ':activatedStatus' => 1,
            ':deletedStatus' => 0,
            ':moderDeletedStatus' => 0,
            ':truncatedStatus' => 0
        );
        /** @var \GalleryVideo $Video */
        $Video = \GalleryVideo::model()->find($criteria);
        if(!isset($Video)) {
            \Yii::app()->message->setErrors('danger', 'Видеозапись не найдена');
            \Yii::app()->message->showMessage();
        }

        $Video->scenario = 'updateVideo';
        $post = \Yii::app()->request->getParam('GalleryVideo');
        if(!empty($post)) {
            if(isset($post['album_id']) && $post['album_id'] != $Video->album_id) {
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
                /** @var \GalleryAlbumVideo $Album */
                $Album = \GalleryAlbumVideo::model()->find($criteria);
                if(!$Album)
                    $post['album_id'] = $Video->album_id;
                else {
                    $Video->view_role = $Album->view_role;
                    $Video->community_alias = $Album->community_alias;
                }
            }

            $Video->attributes = $post;
            $Video->user_update_datetime = \DateTimeFormat::format();
            if($Video->mUpdate())
                \Yii::app()->message->setText('success', 'Видео обновлено');
            else
                \Yii::app()->message->setErrors('danger', $Video);

            \Yii::app()->message->url = \Yii::app()->request->getUrlReferrer();
            \Yii::app()->message->showMessage();
        } else {
            $this->controller->renderPartial($this->viewName, array(
                'model' => $Video,
                'canEdit' => false,
                'update' => true,
            ), false, true);
        }
    }
}