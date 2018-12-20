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
class Add extends GalleryAction
{
    public $viewName = 'form';

    public function run()
    {
        if($this->isCommunity && !\Yii::app()->community->inCommunity()) {
            \Yii::app()->message->setErrors('danger', 'Вы не состоите в этом сообществе');
            \Yii::app()->message->showMessage();
        }

        $criteria = new \CDbCriteria();
        $criteria->addCondition('`t`.id = :id');
        $criteria->scopes = array(
            'activatedStatus',
            'deletedStatus',
            'moderDeletedStatus',
            'truncatedStatus'
        );
        $criteria->params = array(
            ':id' => \Yii::app()->request->getParam('album_id'),
            ':activatedStatus' => 1,
            ':deletedStatus' => 0,
            ':moderDeletedStatus' => 0,
            ':truncatedStatus' => 0
        );

        /** @var \GalleryAlbumVideo $Album */
        $Album = \GalleryAlbumVideo::model()->find($criteria);
        if (!isset($Album)) {
            \Yii::app()->message->setErrors('danger', 'Альбом не найден');
            \Yii::app()->message->showMessage();
        } elseif(!$this->isCommunity && $Album->user_id != \Yii::app()->user->id) {
            \Yii::app()->message->setErrors('danger', 'Вы не можете добавлять аудизаписи в чужой альбом');
            \Yii::app()->message->showMessage();
        }

        $Video = new \GalleryVideo('create');
        $post = \Yii::app()->request->getParam('GalleryVideo');
        if(!empty($post)) {
            $Video->attributes = $post;
            $Video->user_id = \Yii::app()->user->id;
            $Video->album_id = $Album->id;
            $Video->view_role = $Album->view_role;
            $Video->is_community = $Album->is_community;
            $Video->community_id = $Album->community_id;
            $Video->community_alias = $Album->community_alias;

            if($Video->create()) {
                \Yii::app()->message->setText('success', 'Видео добавлено');
                \Yii::app()->message->url = \Yii::app()->request->getUrlReferrer();
            } else
                \Yii::app()->message->setErrors('danger', $Video);
            
            \Yii::app()->message->showMessage();
        } else
            $this->controller->renderPartial($this->viewName, array(
                'model' => $Video,
                'canEdit' => true,
                'update' => false,
            ), false, true);
    }
}