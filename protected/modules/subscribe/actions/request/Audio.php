<?php
namespace application\modules\subscribe\actions\request;
/**
 * Created by JetBrains PhpStorm.
 * User: Николай
 * Date: 13.06.13
 * Time: 13:13
 * To change this template use File | Settings | File Templates.
 *
 * @package application.gallery.actions.image
 */
class Audio extends \CAction
{
    public function run()
    {
        $id = \Yii::app()->request->getParam('item_id');
        $criteria = new \CDbCriteria();
        $criteria->addCondition('`t`.id = :id');
        $criteria->scopes = array('truncatedStatus', 'moderDeletedStatus', 'deletedStatus');

        $criteria->params = \CMap::mergeArray($criteria->params, array(
            ':id' => $id,
            ':truncatedStatus' => 0,
            ':moderDeletedStatus' => 0,
            ':deletedStatus' => 0
        ));
        $criteria->with =  array(
            'user' => array('with' => array('userProfile')),
            'canRate',
            'canSubscribe'
        );
        $criteria->mergeWith(\Yii::app()->access->GetCriteriaAccess());

        /** @var \GalleryAlbumAudio $Audio */
        $Audio = \GalleryAlbumAudio::model()->find($criteria);
        if(!isset($Audio)) {
            \Yii::app()->message->setErrors('danger', 'Аудиоальбом не найден');
            \Yii::app()->message->showMessage();
        }

        $criteria = new \CDbCriteria();
        $criteria->addCondition('subscribe_user_id = :subscribe_user_id');
        $criteria->addCondition('item_id = :item_id');
        $criteria->params = array(
            ':item_id' => $Audio->id,
            ':subscribe_user_id' => \Yii::app()->user->id
        );
        /** @var \SubscribeDebateAudio $model */
        $model = \SubscribeDebateAudio::model()->find($criteria);
        if($model && !$model->is_deleted) {
            \Yii::app()->message->setErrors('danger', 'Вы уже подписаны на эту дискуссию!');
            \Yii::app()->message->showMessage();
        } elseif($model && $model->subscribe_user_id == \Yii::app()->user->id) {
            \Yii::app()->message->setErrors('danger', 'Вы не можете подписаться на эту дискуссию!');
            \Yii::app()->message->showMessage();
        }
        
        if(!$model)
            $model = new \SubscribeDebateAudio();

        $model->is_deleted = 0;
        $model->subscribe_user_id = \Yii::app()->user->id;
        $model->item_id = $Audio->id;
        $model->item_title = $Audio->title;
        $model->owner_item_user_id = $Audio->user_id;
        $model->create_datetime = \DateTimeFormat::format();
        $model->update_datetime = \DateTimeFormat::format();
        $model->view_datetime = \DateTimeFormat::format();
        
        $Audio->update_datetime = \DateTimeFormat::format();
        if($model->save() && $Audio->mUpdate()) {
            \Yii::app()->message->setOther(array('ok' => true));
            \Yii::app()->message->setText('success', 'Вы успешно подписались на эту дискуссию');
        } else
            \Yii::app()->message->setErrors('danger', 'Вознилки проблемы, попробуйте позже');

        \Yii::app()->message->showMessage();
    }
}