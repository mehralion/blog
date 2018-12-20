<?php
namespace application\modules\radio\controllers;
use application\modules\radio\components\MуCaptcha;

class RequestController extends \FrontController
{

    public function actions(){
        return array(
            'captcha'=>array(
                'class' => '\application\modules\radio\components\MуCaptcha',
            ),
        );
    }

    public function actionTest()
    {
        \Yii::app()->radio->streamOff(4);
    }

	public function actionRusfm()
    {
        $dj = "";

        $criteria = new \CDbCriteria();
        $criteria->addCondition('`t`.is_online = 1');
        $criteria->addCondition('`t`.radio_type = :radio_type');
        $criteria->with = array('user');
        $criteria->params = array(':radio_type' => \Radio::RADIO_TYPE_RUSFM);
        /** @var \Radio $Online */
        $Online = \Radio::model()->find($criteria);
        if($Online)
            $dj = $Online->user->login;

        echo "title=".\Yii::app()->radio->getRusTitle()."&dj=".$dj;
    }

    public function actionOldfm()
    {
        $dj = "";

        $criteria = new \CDbCriteria();
        $criteria->addCondition('`t`.is_online = 1');
        $criteria->addCondition('`t`.radio_type = :radio_type');
        $criteria->with = array('user');
        $criteria->params = array(':radio_type' => \Radio::RADIO_TYPE_OLDFM);
        /** @var \Radio $Online */
        $Online = \Radio::model()->find($criteria);
        if($Online)
            $dj = $Online->user->login;

        echo "title=".\Yii::app()->radio->getOldTitle()."&dj=".$dj;
    }

    public function actionRusview()
    {
        $this->renderPartial('rus');
    }

    public function actionOldview()
    {
        $this->renderPartial('old');
    }

    public function actionIndex()
    {
        $RusOnline = null;
        $OldOnline = null;

        $criteria = new \CDbCriteria();
        $criteria->addCondition('`t`.is_online = 1');
        $criteria->with = array('user');
        /** @var \Radio[] $Online */
        $Online = \Radio::model()->findAll($criteria);
        foreach($Online as $model) {
            if($model->radio_type == \Radio::RADIO_TYPE_RUSFM && $RusOnline === null)
                $RusOnline = $model;
            elseif($model->radio_type == \Radio::RADIO_TYPE_OLDFM && $OldOnline === null)
                $OldOnline = $model;
        }

        $criteria = new \CDbCriteria();
        $criteria->addCondition('`t`.radio_type = :radio_type');
        $criteria->params = array(':radio_type' => \Radio::RADIO_TYPE_RUSFM);
        /** @var \UserDj[] $RusFM */
        $RusFM = \UserDj::model()->findAll($criteria);

        $criteria->params = array(':radio_type' => \Radio::RADIO_TYPE_OLDFM);
        /** @var \UserDj[] $OldFM */
        $OldFM = \UserDj::model()->findAll($criteria);

        $criteria = new \CDbCriteria();
        $criteria->addCondition('radio_type = :radio_type');
        $criteria->limit = 10;
        $criteria->order = 'create_datetime desc';
        $criteria->params = array(':radio_type' => \Radio::RADIO_TYPE_RUSFM);
        $LastRus = \RadioTrack::model()->findAll($criteria);

        $criteria->params = array(':radio_type' => \Radio::RADIO_TYPE_OLDFM);
        $LastOld = \RadioTrack::model()->findAll($criteria);

        $this->render('index', array(
            'rusfm' => $RusFM,
            'oldfm' => $OldFM,
            'rusOnline' => $RusOnline,
            'oldOnline' => $OldOnline,
            'lastRus' => $LastRus,
            'lastOld' => $LastOld,
        ));
    }

    public function actionValidate()
    {
        try {
            $criteria = new \CDbCriteria();
            $criteria->addCondition('`t`.alias = :alias');
            $criteria->addCondition('`t`.is_online = 1');
            $criteria->addCondition('`t`.next_update_datetime <= :next_update_datetime');
            $criteria->with = array('user');
            $criteria->params = array(
                ':alias' => \Yii::app()->request->getParam('alias'),
                ':next_update_datetime' => \DateTimeFormat::format()
            );
            /** @var \Radio $Radio */
            $Radio = \Radio::model()->find($criteria);
            if(!$Radio)
                \MyException::ShowError(500, 'Запрос не найден');

            $Radio->scenario = 'captcha';
            $post = \Yii::app()->request->getParam('Radio', array());
            if(isset($post['validation'])) {
                $Radio->validation = $post['validation'];

                /** @var MуCaptcha $captcha */
                $captcha = $this->createAction("captcha");
                $code = $captcha->verifyCode;

                $result = $Radio->validate();

                ob_start();
                var_dump($result);
                $result = ob_get_clean();

                $Log = new \LogRadio();
                $Log->owner_user_id = $Radio->user_id;
                $Log->log_level = \Log::LEVEL_1;
                $Log->description1 = "Ввел каптчу.<br>Отобразили: {$code} Ввел: {$Radio->validation}<br>Результат: {$result}";
                $Log->custom_id = $Radio->radio_type;
                $Log->create_datetime = \DateTimeFormat::format();
                $Log->save();

                if($result) {
                    $Radio->next_update_datetime = \Radio::getNextUpdate();
                    $Radio->is_send_link = 0;
                    $Radio->alias = md5(time());
                    $Radio->save(false);
                    \Yii::app()->message->setText('success', 'Вы успешно прошли проверку');
                    $this->redirect(array('/post/index/index'));
                }
            } else {
                $Log = new \LogRadio();
                $Log->owner_user_id = $Radio->user_id;
                $Log->log_level = \Log::LEVEL_1;
                $Log->description1 = 'Открыл капчту';
                $Log->create_datetime = \DateTimeFormat::format();
                $Log->save();
            }

            $this->render('validate', array('model' => $Radio));
        } catch (\Exception $ex) {
            \MyException::log($ex);
        }
    }

    public function actionControl()
    {
        $this->renderPartial('control');
    }
}