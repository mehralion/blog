<?php
namespace application\modules\community\actions\request;
use application\modules\community\components\CommunityAction;

/**
 * Created by JetBrains PhpStorm.
 * User: Николай
 * Date: 13.06.13
 * Time: 13:13
 * To change this template use File | Settings | File Templates.
 *
 * @package application.gallery.actions.image
 */
class Decline extends CommunityAction
{
    public function run()
    {
        $model = \Yii::app()->community->getModel();
        if($model->is_moder_deleted || $model->is_deleted || $model->deleted_trunc) {
            \Yii::app()->message->setErrors('danger', 'Сооьщество удалено');
            \Yii::app()->message->url = \Yii::app()->request->urlReferrer;
            \Yii::app()->message->showMessage();
        }

        $Request = $model->inRequest;

        if($model->inCommunity) {
            \Yii::app()->message->setErrors('danger', 'Вы уже состоите в этом сообществе');
            \Yii::app()->message->url = \Yii::app()->createUrl('/community/request/show', array('community_alias' => $model->alias));
            \Yii::app()->message->showMessage();
        }

        if($Request->isInvite)
            $Request->request_status = \CommunityRequest::STATUS_FAIL; //отказался от приглашения
        else
            $Request->request_status = \CommunityRequest::STATUS_CANCEL; //отозвал свой запрос

        if(!$Request->save())
            \Yii::app()->message->setErrors('danger', $Request);
        else {
            if($Request->isInvite)
                \Yii::app()->message->setText('success', 'Вы успешно отказались от приглашения');
            else
                \Yii::app()->message->setText('success', 'Вы успешно отозвали свой запрос');

            \Yii::app()->message->url = \Yii::app()->request->urlReferrer;
        }

        \Yii::app()->message->showMessage();
    }
}