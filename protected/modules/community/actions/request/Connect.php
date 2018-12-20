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
class Connect extends CommunityAction
{
    public function run()
    {
        if(\Yii::app()->community->inCommunity()) {
            \Yii::app()->message->setOther(array('ok' => true));
            \Yii::app()->message->setErrors('danger', 'Вы уже состоите в этом сообществе');
            \Yii::app()->message->showMessage();
        }

        $model = \Yii::app()->community->getModel();
        if($model->is_deleted || $model->is_moder_deleted || $model->deleted_trunc) {
            \Yii::app()->message->setErrors('danger', 'Сообщество удалено');
            \Yii::app()->message->url = \Yii::app()->request->urlReferrer;
            \Yii::app()->message->showMessage();
        }

        $error = false;
        $t = \Yii::app()->db->beginTransaction();
        try {
            switch ($model->view_role) {
                case \Community::TYPE_PUBLIC:
                    if(\Yii::app()->community->inRequest() || \Yii::app()->community->inInvite()) {
                        $model->inRequest->request_status = \CommunityRequest::STATUS_SUCCESS;
                        if(!$model->inRequest->save()) {
                            $error = true;
                            \Yii::app()->message->setErrors('danger', $model->inRequest);
                        }
                    }

                    $inCommunity = new \CommunityUser();
                    $inCommunity->community_id = \Yii::app()->community->id;
                    $inCommunity->user_id = \Yii::app()->user->id;
                    $inCommunity->user_type = \CommunityUser::TYPE_USER;
                    $inCommunity->update_datetime = \DateTimeFormat::format();
                    $inCommunity->create_datetime = \DateTimeFormat::format();
                    if(!$inCommunity->save()) {
                        $error = true;
                        \Yii::app()->message->setErrors('danger', $inCommunity);
                    } else {
                        \Yii::app()->message->setOther(array('ok' => true));
                        \Yii::app()->message->setText('success', 'Вы успешно вступили в сообщество '.\Yii::app()->community->title);
                        \Yii::app()->message->url = \Yii::app()->createUrl('/community/request/show', array('community_alias' => \Yii::app()->community->alias));
                    }

                    break;

                case \Community::TYPE_MODER:
                    if(\Yii::app()->community->inInvite()) {
                        $model->inRequest->request_status = \CommunityRequest::STATUS_SUCCESS;
                        if(!$model->inRequest->save()) {
                            $error = true;
                            \Yii::app()->message->setErrors('danger', $model->inRequest);
                        }

                        $inCommunity = new \CommunityUser();
                        $inCommunity->community_id = \Yii::app()->community->id;
                        $inCommunity->user_id = \Yii::app()->user->id;
                        $inCommunity->user_type = \CommunityUser::TYPE_USER;
                        $inCommunity->update_datetime = \DateTimeFormat::format();
                        $inCommunity->create_datetime = \DateTimeFormat::format();
                        if(!$inCommunity->save()) {
                            $error = true;
                            \Yii::app()->message->setErrors('danger', $inCommunity);
                        } else {
                            \Yii::app()->message->setOther(array('ok' => true));
                            \Yii::app()->message->setText('success', 'Вы успешно вступили в сообщество '.\Yii::app()->community->title);
                            \Yii::app()->message->url = \Yii::app()->createUrl('/community/request/show', array('community_alias' => \Yii::app()->community->alias));
                        }
                    } elseif(!\Yii::app()->community->inRequest()) {
                        $Request = new \CommunityRequest('create');
                        $Request->community_id = \Yii::app()->community->id;
                        $Request->user_id = \Yii::app()->user->id;
                        $Request->create_datetime = \DateTimeFormat::format();
                        if(!$Request->save()) {
                            $error = true;
                            \Yii::app()->message->setErrors('danger', $Request);
                        } else {
                            \Yii::app()->message->setOther(array('ok' => true));
                            \Yii::app()->message->setText('success', 'Вы успешно отправили заявку на вступление в сообщество '.\Yii::app()->community->title);
                            \Yii::app()->message->url = \Yii::app()->createUrl('/community/request/show', array('community_alias' => \Yii::app()->community->alias));
                        }
                    } else {
                        \Yii::app()->message->setOther(array('ok' => true));
                        \Yii::app()->message->setText('danger', 'Ваша заявка уже на рассмотрении!');
                    }

                    break;

                case \Community::TYPE_INVITE:
                    $admin = \Yii::app()->community->getAdministrator();
                    \Yii::app()->message->setOther(array('content' => "Вступить в сообщество можно только по приглашению администратора. <br>
                    Найдите персонажа {$admin} в игре и попросите выслать Вам приглашение."));
                    break;
            }

            if(!$error)
                $t->commit();
            else
                $t->rollback();

        } catch (\Exception $ex) {
            $t->rollback();
            \MyException::log($ex);
        }

        \Yii::app()->message->showMessage();
    }
}