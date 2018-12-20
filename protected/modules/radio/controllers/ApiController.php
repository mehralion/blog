<?php
namespace application\modules\radio\controllers;
class ApiController extends \FrontController
{
	public function actionOn()
    {
        $uid = \Yii::app()->request->getPost('uid');
        $hash = \Yii::app()->request->getPost('hash');
        if(!$uid || !$hash)
            \MyException::ShowError(404,'Страница не найдена');

        $criteria = new \CDbCriteria();
        $criteria->addCondition('id = :id');
        $criteria->params = array(':id' => $uid);
        /** @var \User $User */
        $User = \User::model()->find($criteria);
        if(!$User && \ApiUser::checkUser(null, null, $uid) !== false)
            $User = \User::model()->find($criteria);

        if(!$User) {
            \MyException::logTxt('Не удалось добавить dj '.$uid, 'dj');
            return;
        }

        $Radio = new \Radio();
        $Radio->user_id = $User->id;
        $Radio->radio_type = $User->radio_type;
        $Radio->is_online = 1;
        $Radio->start_datetime = \DateTimeFormat::format();
        $Radio->next_update_datetime = \Radio::getNextUpdate();
        $Radio->alias = md5(time().$Radio->next_update_datetime);
        $Radio->save();
    }

    public function actionOff()
    {
        $uid = \Yii::app()->request->getPost('uid');
        $hash = \Yii::app()->request->getPost('hash');
        if(!$uid || !$hash)
            \MyException::ShowError(404,'Страница не найдена');

        $criteria = new \CDbCriteria();
        $criteria->addCondition('id = :id');
        $criteria->params = array(':id' => $uid);
        /** @var \User $User */
        $User = \User::model()->find($criteria);
        if(!$User && \ApiUser::checkUser(null, null, $uid) !== false)
            $User = \User::model()->find($criteria);

        if(!$User) {
            \MyException::logTxt('Не удалось добавить dj '.$uid, 'dj');
            return;
        }

        $criteria = new \CDbCriteria();
        $criteria->addCondition('is_online = 1');
        $criteria->addCondition('user_id = :user_id');
        $criteria->params = array(':user_id' => $User->id);

        /** @var \Radio $Radio */
        $Radio = \Radio::model()->find($criteria);
        if(!$Radio)
            return;

        $Radio->is_online = 0;
        $Radio->end_datetime = \DateTimeFormat::format();
        $Radio->is_send_link = 0;
        $Radio->finish_type = \Radio::FINISH_SUCCESS;
        $Radio->save();

        \Yii::app()->radio->streamOff($Radio->radio_type);
    }
}