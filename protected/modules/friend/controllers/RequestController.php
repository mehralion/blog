<?php

namespace application\modules\friend\controllers;

/**
 * Class RequestController
 * @package application.friend.controllers
 */
class RequestController extends \FrontController {

    public function filters() {
        return array(
            'accessControl',
        );
    }

    public function accessRules() {
        return array(
            array('deny',
                'users' => array('?'),
            )
        );
    }

    public function actionAdd() {
        $game_id = \Yii::app()->request->getParam('gameId');
        $criteria = new \CDbCriteria();
        $criteria->addCondition('game_id = :game_id');
        $criteria->params = array(':game_id' => $game_id);

        /** @var \User $User */
        $User = \User::model()->find($criteria);
        if (!isset($User))
            \MyException::ShowError(403, 'Персонаж не найден');
        
        if($User->id == \Yii::app()->user->id) {
            \Yii::app()->message->setErrors('error', 'Вы не можете добавить сами себя в друзья');
            \Yii::app()->message->showMessage();
        }

        $criteria = new \CDbCriteria();
        $criteria->addCondition('friend_id = :f_id');
        $criteria->scopes = array('own');
        $criteria->params = array(':f_id' => $User->id);
        /** @var \UserFriend[] $Friend */
        $Friend = \UserFriend::model()->find($criteria);

        $criteria = new \CDbCriteria();
        $criteria->addCondition('(user_id = :u_id and friend_id = :f_id) or (user_id = :f_id and friend_id = :u_id)');
        $criteria->addCondition('reciver_status = :pending');
        $criteria->params = array(
            ':u_id' => \Yii::app()->user->id,
            ':f_id' => $User->id,
            ':pending' => \FriendRequest::STATUS_PENDING
        );

        if (isset($Friend))
            \Yii::app()->message->setText('warning', 'Персонаж уже является вашим другом');
        elseif (isset($FrRequest))
            \Yii::app()->message->setText('warning', 'Вы уже отправляли запрос эту пользователю. Дождитесь ответ!');
        else {
            $FriendModel = new \FriendRequest();
            $FriendModel->user_id = \Yii::app()->user->id;
            $FriendModel->friend_id = $User->id;
            $FriendModel->reciver_status = \FriendRequest::STATUS_PENDING;
            $FriendModel->create_datetime = \DateTimeFormat::format();
            $FriendModel->update_datetime = \DateTimeFormat::format();
            if ($FriendModel->save()) {
                \Yii::app()->message->setText('success', 'Вы отправили запрос к ' . $User->getFullLogin());
                \Yii::app()->message->setOther(array('ok' => true));
            } else
                \Yii::app()->message->setErrors('error', 'Возникла ошибка, повторите позже');
        }

        \Yii::app()->message->showMessage();
    }

    public function actionDelete() {
        $game_id = \Yii::app()->request->getParam('gameId');
        $criteria = new \CDbCriteria();
        $criteria->addCondition('game_id = :game_id');
        $criteria->params = array(':game_id' => $game_id);

        /** @var \User $User */
        $User = \User::model()->find($criteria);
        if (!isset($User))
            \MyException::ShowError(403, 'Персонаж не найден');

        $criteria = new \CDbCriteria();
        $criteria->addCondition('friend_id = :f_id');
        $criteria->scopes = array('own');
        $criteria->params = array(':f_id' => $User->id);
        /** @var \UserFriend $Friend */
        $Friend = \UserFriend::model()->find($criteria);
        if (!isset($Friend))
            \MyException::ShowError(403, 'Персонаж не является вашим другом');

        $error = false;
        $t = \Yii::app()->db->beginTransaction();
        try {
            $criteria = new \CDbCriteria();
            $criteria->addCondition('user_id = :u_id and friend_id = :f_id or user_id = :f_id and friend_id = :u_id');
            $criteria->params = array(
                ':u_id' => \Yii::app()->user->id,
                ':f_id' => $User->id,
            );
            \UserFriend::model()->deleteAll($criteria);

            $criteria->addCondition('reciver_status = :reciver_status');
            $criteria->params = \CMap::mergeArray($criteria->params, array(
                ':reciver_status' => \FriendRequest::STATUS_ACCEPTED
            ));

            /** @var \FriendRequest $FriendRq */
            $FriendRq = \FriendRequest::model()->find($criteria);
            if($FriendRq) {
                $FriendRq->reciver_status = \FriendRequest::STATUS_DELETED;
                if(!$FriendRq->save())
                    $error = true;
            }
            if(!$error) {
                $t->commit();
                \Yii::app()->message->setText('success', 'Друг ' . $User->getFullLogin() . ' был удален');
                \Yii::app()->message->setOther(array('ok' => true));
                \Yii::app()->message->showMessage();
            } else {
                $t->rollback();
            }
        } catch (\Exception $ex) {
            $t->rollback();
        }
    }

    public function actionAccept() {
        $criteria = new \CDbCriteria();
        $criteria->addCondition('id = :id');
        $criteria->params = array(':id' => \Yii::app()->request->getParam('id'));
        $criteria->scopes = array('own', 'pending');

        /** @var \FriendRequest $FrRequest */
        $FrRequest = \FriendRequest::model()->find($criteria);
        if (!empty($FrRequest)) {
            $t = \Yii::app()->db->beginTransaction();
            $error = false;
            try {
                $FrRequest->update_datetime = \DateTimeFormat::format();
                $FrRequest->reciver_status = \FriendRequest::STATUS_ACCEPTED;
                if (!$FrRequest->save())
                    $error = true;

                if(!$error) {
                    $UserFr = new \UserFriend();
                    $UserFr->user_id = \Yii::app()->user->id;
                    $UserFr->friend_id = $FrRequest->user_id;
                    $UserFr->create_datetime = \DateTimeFormat::format();
                    if(!$UserFr->save())
                        $error = true;
                }

                if(!$error) {
                    $UserFr = new \UserFriend();
                    $UserFr->user_id = $FrRequest->user_id;
                    $UserFr->friend_id = \Yii::app()->user->id;
                    $UserFr->create_datetime = \DateTimeFormat::format();
                    if(!$UserFr->save())
                        $error = true;
                }

                if (!$error) {
                    $t->commit();
                    \Yii::app()->message->setText('success', 'Вы подтвердили дружбу');
                    \Yii::app()->message->setOther(array('ok' => true));
                } else {
                    $t->rollback();
                    \Yii::app()->message->setErrors('error', 'Возниклки проблемы, повторите позже');
                }
            } catch (\Exception $ex) {
                $t->rollback();
                \MyException::log($ex);
                \Yii::app()->message->setErrors('error', 'Возникли проблемы, повторите позже');
            }
        } else
            \Yii::app()->message->setErrors('error', 'Запрос в друзья не найден');

        \Yii::app()->message->showMessage();
    }

    public function actionFail() {
        $criteria = new \CDbCriteria();
        $criteria->addCondition('id = :id');
        $criteria->params = array(':id' => \Yii::app()->request->getParam('id'));
        $criteria->scopes = array('own', 'pending');

        /** @var \FriendRequest[] $FrRequest */
        $FrRequest = \FriendRequest::model()->findAll($criteria);
        if (!empty($FrRequest)) {
            $t = \Yii::app()->db->beginTransaction();
            $error = false;
            try {
                foreach ($FrRequest as $friend) {
                    $friend->update_datetime = \DateTimeFormat::format();
                    $friend->reciver_status = \FriendRequest::STATUS_FAIL;
                    if (!$friend->save()) {
                        $error = true;
                        break;
                    }
                }

                if (!$error) {
                    $t->commit();
                    \Yii::app()->message->setText('success', 'Вы отказали в дружбе');
                    \Yii::app()->message->setOther(array('ok' => true));
                } else {
                    $t->rollback();
                    \Yii::app()->message->setErrors('error', 'Возниклки проблемы, повторите позже');
                }
            } catch (\Exception $ex) {
                $t->rollback();
                \MyException::log($ex);
                \Yii::app()->message->setErrors('error', 'Возникли проблемы, повторите позже');
            }
        } else
            \Yii::app()->message->setErrors('error', 'Запрос в друзья не найден');

        \Yii::app()->message->showMessage();
    }

    public function actionCancel() {
        $criteria = new \CDbCriteria();
        $criteria->addCondition('id = :id and user_id = :user_id');
        $criteria->params = array(
            ':id' => \Yii::app()->request->getParam('id'),
            ':user_id' => \Yii::app()->user->id
        );
        $criteria->scopes = array('pending');

        /** @var \FriendRequest[] $FrRequest */
        $FrRequest = \FriendRequest::model()->findAll($criteria);
        if (isset($FrRequest)) {
            $t = \Yii::app()->db->beginTransaction();
            $error = false;
            try {
                foreach ($FrRequest as $friend) {
                    $friend->update_datetime = \DateTimeFormat::format();
                    $friend->reciver_status = \FriendRequest::STATUS_CANCEL;
                    if (!$friend->save()) {
                        $error = true;
                        break;
                    }
                }

                if (!$error) {
                    $t->commit();
                    \Yii::app()->message->setText('success', 'Вы успешно отозвали свой запрос');
                    \Yii::app()->message->setOther(array('ok' => true));
                } else {
                    $t->rollback();
                    \Yii::app()->message->setErrors('error', 'Возниклки проблемы, повторите позже');
                }
            } catch (\Exception $ex) {
                $t->rollback();
                \MyException::log($ex);
                \Yii::app()->message->setErrors('error', 'Возникли проблемы, повторите позже');
            }
        } else
            \Yii::app()->message->setErrors('error', 'Заявка не найдена');

        \Yii::app()->message->showMessage();
    }

}
