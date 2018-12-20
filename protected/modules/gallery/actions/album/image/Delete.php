<?php
namespace application\modules\gallery\actions\album\image;
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
class Delete extends GalleryAction
{
    public function run()
    {
        if($this->isCommunity) {
            if(!\Yii::app()->community->inCommunity()) {
                \Yii::app()->message->setErrors('danger', 'Вы не состоите в этом сообществе');
                \Yii::app()->message->showMessage();
            }

            $this->successLinkRoute = '/community/album/image';
            $this->successLinkParams = array('community_alias' => \Yii::app()->community->alias);
        } else {
            $this->successLinkRoute = '/gallery/album/index_image';
            $this->successLinkParams = array('gameId' => \Yii::app()->user->getGameId());
        }

        $criteria = new \CDbCriteria();
        $criteria->addCondition('`t`.id = :id');
        $criteria->params = array(':id' => \Yii::app()->request->getParam('album_id'));
        /** @var \GalleryAlbumImage $Album */
        $Album = \GalleryAlbumImage::model()->find($criteria);
        if(!isset($Album))
            \Yii::app()->message->setErrors('danger', 'Альбом не существует!');
        elseif(($this->isCommunity && !\Yii::app()->community->isModer() && !$Album->user_id != \Yii::app()->user->id)
            || (!$this->isCommunity && $Album->user_id != \Yii::app()->user->id))
            \Yii::app()->message->setErrors('danger', 'Вы не можете удалить этот альбом!');
        else {
            $Album->is_deleted = 1;
            $Album->user_deleted_id = \Yii::app()->user->id;
            if($Album->delete())
                \Yii::app()->message->setText('success', 'Альбом был удален!');
            else
                \Yii::app()->message->setErrors('danger', 'Возникли проблемы во время удаления, попробуйте позже!');
        }

        \Yii::app()->message->url = \Yii::app()->createUrl($this->successLinkRoute, $this->successLinkParams);
        \Yii::app()->message->showMessage();
    }
}