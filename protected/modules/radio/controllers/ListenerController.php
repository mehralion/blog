<?php
namespace application\modules\radio\controllers;
class ListenerController extends \FrontController
{
    public function actionAuth()
    {
        header('icecast-auth-user:1');
    }

	public function actionAdd()
    {
        $user_id = 0;
        $radio_session_id = 0;
        $radio_type = \Radio::RADIO_TYPE_RUSFM_D;

        $criteria = new \CDbCriteria();
        $criteria->addCondition('is_online = 1');
        $criteria->addCondition('radio_type = :radio_type');
        $criteria->params = array(':radio_type' => \Radio::RADIO_TYPE_RUSFM);
        /** @var \Radio $DjOnline */
        $DjOnline = \Radio::model()->find($criteria);
        if($DjOnline) {
            $user_id            = $DjOnline->user_id;
            $radio_type         = $DjOnline->radio_type;
            $radio_session_id   = $DjOnline->id;
        }

        $criteria = new \CDbCriteria();
        $criteria->addCondition('dj_id = :dj_id');
        $criteria->addCondition('ended_at = :ended_at');
        $criteria->addCondition('duration = :duration');
        $criteria->addCondition('ip = :ip');
        $criteria->params = array(
            ':dj_id'    => $user_id,
            ':ended_at' => 0,
            ':duration' => 0,
            ':ip'       => \Yii::app()->request->getParam('ip'),
        );
        $t = \Yii::app()->db->beginTransaction();
        try {
            /** @var \RadioUser $RadioUser */
            $RadioUser = \RadioUser::model()->find($criteria);
            if(!$RadioUser) {
                $start = new \DateTime();
                $Model = new \RadioUser();
                $Model
                    ->setDjId($user_id)
                    ->setRadioType($radio_type)
                    ->setStartedAt($start->getTimestamp())
                    ->setClient(\Yii::app()->request->getParam('client'))
                    ->setIp(\Yii::app()->request->getParam('ip'))
                    ->setAgent(\Yii::app()->request->getParam('agent'))
                    ->setDuration(\Yii::app()->request->getParam('duration'))
                    ->setRadioSessionId($radio_session_id)
                    ->save();
            } else
                \RadioUser::model()->updateCounters(array('counter' => 1), 'id = :id', array(':id' => $RadioUser->getId()));

            $t->commit();
        } catch (\Exception $ex) {
            $t->rollback();
        }

        header('icecast-auth-user: OK');
        \Yii::app()->end();
    }

    public function actionRemove()
    {
        $criteria = new \CDbCriteria();
        $criteria->addCondition('client = :client');
        $criteria->addCondition('ended_at = :ended_at');
        $criteria->addCondition('duration = :duration');
        $criteria->params = array(
            ':client'   => \Yii::app()->request->getParam('client'),
            ':ended_at' => 0,
            ':duration' => 0
        );

        $t = \Yii::app()->db->beginTransaction();
        try {
            /** @var \RadioUser $Listener */
            $Listener = \RadioUser::model()->find($criteria);
            if(!$Listener)
                throw new \Exception();

            $time = new \DateTime();
            $Listener
                ->setEndedAt($time->getTimestamp())
                ->setDuration(\Yii::app()->request->getParam('duration'));
            if($Listener->getIp() != \Yii::app()->request->getParam('ip'))
                $Listener->setIpChange(\Yii::app()->request->getParam('ip'));

            $Listener->save();

            $t->commit();
        } catch (\Exception $ex) {
            $t->rollback();
        }
    }
}