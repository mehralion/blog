<?php
namespace application\modules\community\actions\users;
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
class Update extends CommunityAction
{
    public function run()
    {
        if(!\Yii::app()->community->isModer()) {
            \Yii::app()->message->setErrors('danger', 'Данное действие доступно только для модераторов группы!');
            \Yii::app()->message->showMessage();
        }

        $criteria = new \CDbCriteria();
        $criteria->scopes = array('deletedStatus', 'moderDeletedStatus', 'truncatedStatus', 'own');
        $criteria->addCondition('id = :id');
        $criteria->params = array(
            ':id' => \Yii::app()->community->id,
            ':deletedStatus' => 0,
            ':moderDeletedStatus' => 0,
            ':truncatedStatus' => 0,
        );
        /** @var \Community $model */
        $model = \Community::model()->find($criteria);

        if(!$model) {
            \Yii::app()->message->setErrors('danger', 'Сообщество не найдено');
            \Yii::app()->message->url = \Yii::app()->createUrl('/community/dashboard/own', array('gameId' => \Yii::app()->user->getGameId()));
            \Yii::app()->message->showMessage();
        }
        $error = false;
        $t = \Yii::app()->db->beginTransaction();

        try {
            $moders = \Yii::app()->request->getParam('Moder', array());
            $invites = \Yii::app()->request->getParam('Invite', array());
            if($moders || $invites) {
                foreach($moders as $moder) {
                    if(!isset($moder['id']) && !isset($moder['login']))
                        continue;

                    /** @var \User $User */
                    $User = \User::model()->find('game_id = :game_id', array(':game_id' => $moder['id']));
                    if(!$User)
                        \Yii::app()->message->setErrors('danger', 'Пользователь '.$moder['login'].' не найден.');
                    else {
                        $criteria = new \CDbCriteria();
                        $criteria->addCondition('`t`.community_id = :community_id');
                        $criteria->addCondition('`t`.user_id = :user_id');
                        $criteria->scopes = array('deletedStatus');
                        $criteria->params = array(
                            ':community_id' => \Yii::app()->community->id,
                            ':user_id' => $User->id,
                            ':deletedStatus' => 0
                        );
                        /** @var \CommunityUser $inCommunity */
                        $inCommunity = \CommunityUser::model()->find($criteria);
                        if($inCommunity) {
                            $inCommunity->update_datetime = \DateTimeFormat::format();
                            $inCommunity->user_type = \CommunityUser::TYPE_MODER;
                            if(!$inCommunity->save()) {
                                $error = true;
                                \Yii::app()->message->setErrors('danger', $inCommunity);
                            }
                        }/* else {
                            $criteria = new \CDbCriteria();
                            $criteria->addCondition('`t`.community_id = :community_id');
                            $criteria->addCondition('`t`.user_id = :user_id');
                            $criteria->scopes = array('pending');
                            $criteria->params = array(
                                ':community_id' => \Yii::app()->community->id,
                                ':user_id' => $User->id,
                            );
                            /** @var \CommunityRequest $inRequest Подавал ли запрос на вступление? */
                            /*$inRequest = \CommunityRequest::model()->find($criteria);
                            if(!$inRequest) { //Приглашение пользователю
                                $inRequest = new \CommunityRequest('create');
                                $inRequest->community_id = \Yii::app()->community->id;
                                $inRequest->user_id = $User->id;
                                $inRequest->create_datetime = \DateTimeFormat::format();
                                $inRequest->isInvite = 1;
                                $inRequest->isModer = 1;
                                if(!$inRequest->save()) {
                                    $error = true;
                                    \Yii::app()->message->setErrors('danger', $inRequest);
                                }
                            } elseif(!$inRequest->isInvite) { //Запрос от пользователя
                                $inRequest->request_status = \CommunityRequest::STATUS_SUCCESS;
                                if(!$inRequest->save()) {
                                    $error = true;
                                    \Yii::app()->message->setErrors('danger', $inRequest);
                                }

                                $inCommunity = new \CommunityUser('create');
                                $inCommunity->community_id = \Yii::app()->community->id;
                                $inCommunity->user_id = $User->id;
                                $inCommunity->create_datetime = \DateTimeFormat::format();
                                $inCommunity->user_type = \CommunityUser::TYPE_MODER;
                                if(!$inCommunity->save()) {
                                    $error = true;
                                    \Yii::app()->message->setErrors('danger', $inCommunity);
                                }
                            }
                        }*/
                    }
                }

                foreach($invites as $invite) {
                    if(!isset($invite['id']) && !isset($invite['login']))
                        continue;
                    /** @var \User $User */
                    $User = \User::model()->find('game_id = :game_id', array(':game_id' => $invite['id']));
                    if(!$User)
                        \Yii::app()->message->setErrors('danger', 'Пользователь '.$invite['login'].' не найден.');
                    else {
                        $criteria = new \CDbCriteria();
                        $criteria->addCondition('`t`.community_id = :community_id');
                        $criteria->addCondition('`t`.user_id = :user_id');
                        $criteria->scopes = array('pending');
                        $criteria->params = array(
                            ':community_id' => \Yii::app()->community->id,
                            ':user_id' => $User->id,
                        );
                        /** @var \CommunityRequest $inRequest Подавал ли запрос на вступление? */
                        $inRequest = \CommunityRequest::model()->find($criteria);
                        if(!$inRequest) { //Приглашение пользователю
                            $inRequest = new \CommunityRequest('create');
                            $inRequest->community_id = \Yii::app()->community->id;
                            $inRequest->user_id = $User->id;
                            $inRequest->create_datetime = \DateTimeFormat::format();
                            $inRequest->isInvite = 1;
                            if(!$inRequest->save()) {
                                $error = true;
                                \Yii::app()->message->setErrors('danger', $inRequest);
                            }
                        } elseif(!$inRequest->isInvite) { //Запрос от пользователя
                            $inRequest->request_status = \CommunityRequest::STATUS_SUCCESS;
                            if(!$inRequest->save()) {
                                $error = true;
                                \Yii::app()->message->setErrors('danger', $inRequest);
                            }

                            $inCommunity = new \CommunityUser('create');
                            $inCommunity->community_id = \Yii::app()->community->id;
                            $inCommunity->user_id = $User->id;
                            $inCommunity->update_datetime = \DateTimeFormat::format();
                            $inCommunity->create_datetime = \DateTimeFormat::format();
                            $inCommunity->user_type = \CommunityUser::TYPE_MODER;
                            if(!$inCommunity->save()) {
                                $error = true;
                                \Yii::app()->message->setErrors('danger', $inCommunity);
                            }
                        }
                    }
                }
            }

            if(!$error) {
                $t->commit();
                \Yii::app()->message->setText('success', 'Сообщество сохранено!');
                \Yii::app()->message->url = \Yii::app()->createUrl('/community/request/show', array('community_alias' => \Yii::app()->community->alias));
            } else
                $t->rollback();

        } catch (\Exception $ex) {
            $t->rollback();
            \MyException::log($ex);
        }

        \Yii::app()->message->showMessage();
    }
}