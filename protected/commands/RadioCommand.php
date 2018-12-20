<?php
class RadioCommand extends CConsoleCommand
{
	public function run($args)
	{
        echo "Radio info: \n";
        print_r(Yii::app()->radio);
        echo "\n";

        $criteria = new CDbCriteria();
        $criteria->addCondition('radio_type = :radio_type');
        $criteria->order = 'create_datetime desc';
        $criteria->params = array(':radio_type' => Radio::RADIO_TYPE_RUSFM);
        /** @var RadioTrack $LastRus */
        $LastRus = RadioTrack::model()->find($criteria);

        echo "Track Updater. Rus track title: ".Yii::app()->radio->getRusTitle()."\n";
        if(!$LastRus || $LastRus->title != Yii::app()->radio->getRusTitle()) {
            echo "New track\n";
            $LastRus = new RadioTrack();
            $LastRus->radio_type = Radio::RADIO_TYPE_RUSFM;
            $LastRus->title = Yii::app()->radio->getRusTitle();
            $LastRus->create_datetime = date('Y-m-d H:i:s', time());
            if(!$LastRus->save()) {
                print_r($LastRus->getErrors());
            }
        }

        $criteria->params = array(':radio_type' => Radio::RADIO_TYPE_OLDFM);
        /** @var RadioTrack $LastOld */
        $LastOld = RadioTrack::model()->find($criteria);

        echo "Track Updater. Old track title: ".Yii::app()->radio->getOldTitle()."\n";
        if(!$LastOld || $LastOld->title != Yii::app()->radio->getOldTitle()) {
            echo "New track\n";
            $LastOld = new RadioTrack();
            $LastOld->radio_type = Radio::RADIO_TYPE_OLDFM;
            $LastOld->title = Yii::app()->radio->getOldTitle();
            $LastOld->create_datetime = date('Y-m-d H:i:s', time());
            if(!$LastOld->save()) {
                print_r($LastOld->getErrors());
            }
        }

        $criteria = new CDbCriteria();
        $criteria->addCondition('user_id = :user_id');

        $oldInfo = Yii::app()->radio->getOldInfo();
        if($oldInfo !== false)
            $this->checkUser($oldInfo->description, Radio::RADIO_TYPE_OLDFM);

        $rusInfo = Yii::app()->radio->getRusInfo();
        if($rusInfo !== false) {
            $this->checkUser($rusInfo->description, Radio::RADIO_TYPE_RUSFM);
        }

        $old = false;
        $rus = false;

        $criteria = new CDbCriteria();
        $criteria->addCondition('is_online = 1');
        /** @var Radio[] $models */
        $models = Radio::model()->findAll($criteria);
        foreach($models as $model) {
            if($model->radio_type == Radio::RADIO_TYPE_OLDFM)
                $old = true;
            elseif($model->radio_type == Radio::RADIO_TYPE_RUSFM)
                $rus = true;

            $userRadioStreamName = $model->getUserRadio();
            if(Yii::app()->radio->{$userRadioStreamName} === null || Yii::app()->radio->{$userRadioStreamName}->description != $model->user->game_id) {
                /**
                 * Числиться в онлайне, а стрима нет. Либо ушел, либо вырубило
                 * Выставляем статусы в базе
                 * Отправляем в Oldbk на отключение
                 */
                $model->off(Radio::FINISH_OFFLINE);

                $Log = new \LogRadio();
                $Log->owner_user_id = $model->user_id;
                $Log->log_level = \Log::LEVEL_1;
                $Log->description1 = "Закончил эфир";
                $Log->custom_id = $model->radio_type;
                $Log->create_datetime = date('Y-m-d H:i:s', time());
                $Log->save();
                continue;
            }
            $nextUpdate = strtotime($model->next_update_datetime) + Radio::getMaxTime();
            if($nextUpdate <= time()) { //Вырубаем пользователя, он пропустил проверку
                /**
                 * Выставляем статусы в базе
                 * Отправляем в Oldbk на отключение
                 * Вырубаем стрим
                 */
                $model->off(Radio::FINISH_NO_CHECK);

                $Log = new \LogRadio();
                $Log->owner_user_id = $model->user_id;
                $Log->log_level = \Log::LEVEL_1;
                $Log->description1 = "Пропуск проверки";
                $Log->custom_id = $model->radio_type;
                $Log->create_datetime = date('Y-m-d H:i:s', time());
                $Log->save();
                continue;
            }

            if(strtotime($model->next_update_datetime) <= time() && !$model->is_send_link) { //Отправляем урл для проверки
                $model->is_send_link = 1;
                $model->alias = md5(time());
                $model->save();

                Yii::app()->radio->sendLink($model->user->login, $model->alias);

                $Log = new \LogRadio();
                $Log->owner_user_id = $model->user_id;
                $Log->log_level = \Log::LEVEL_1;
                $Log->description1 = "Отправили линк в чат";
                $Log->custom_id = $model->radio_type;
                $Log->create_datetime = date('Y-m-d H:i:s', time());
                $Log->save();
            }

            Yii::app()->curl->run('http://capitalcity.oldbk.com/friends.php?key=246y426514256135y4315y1&radio='.$model->radio_type.'&user='.$model->user->game_id);
        }

        if(!$old)
            Yii::app()->radio->streamOff(Radio::RADIO_TYPE_OLDFM);
        if(!$rus)
            Yii::app()->radio->streamOff(Radio::RADIO_TYPE_RUSFM);
	}

    private function checkUser($userId, $radio)
    {
        $criteria = new \CDbCriteria();
        $criteria->addCondition('game_id = :game_id');
        $criteria->params = array(':game_id' => $userId);
        /** @var \User $User */
        $User = \User::model()->find($criteria);
        if(!$User && \ApiUser::checkUser(null, null, $userId) !== false)
            $User = \User::model()->find($criteria);
        if(!$User) {
            Yii::app()->radio->streamOff($radio);
            \MyException::logTxt('Не удалось добавить dj '.$userId, 'dj');
        } else {
            $Dj = UserDj::model()->find('user_id = :user_id', array(':user_id' => $User->id));
            if(!$Dj) {
                Yii::app()->radio->streamOff($radio);
                \MyException::logTxt('Не удалось добавить dj '.$userId, 'dj');
            } else {
                /** @var Radio $model */
                $model = Radio::model()->find('user_id = :user_id and is_online = 1', array(':user_id' => $User->id));
                if(!$model) {
                    $Radio = new \Radio();
                    $Radio->user_id = $User->id;
                    $Radio->radio_type = $radio;
                    $Radio->is_online = 1;
                    $Radio->start_datetime = date('Y-m-d H:i:s', time());
                    $Radio->next_update_datetime = \Radio::getNextUpdate();
                    $Radio->alias = md5(time().$Radio->next_update_datetime);
                    $Radio->save();

                    $Log = new \LogRadio();
                    $Log->owner_user_id = $Radio->user_id;
                    $Log->log_level = \Log::LEVEL_1;
                    $Log->description1 = "Вышел в эфир";
                    $Log->custom_id = $model->radio_type;
                    $Log->create_datetime = date('Y-m-d H:i:s', time());
                    $Log->save();
                }
            }
        }
    }
}
