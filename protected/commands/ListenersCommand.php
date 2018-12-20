<?php
class ListenersCommand extends CConsoleCommand
{
	public function run($args)
	{
        $listeners = Yii::app()->radio->getListeners(Radio::RADIO_TYPE_RUSFM);
        if(empty($listeners)) {
            return false;
        }

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

        $exclude_ids = [];
        foreach ($listeners as $listener) {
            $t = \Yii::app()->db->beginTransaction();
            try {
                $criteria = new \CDbCriteria();
                $criteria->addCondition('dj_id = :dj_id');
                $criteria->addCondition('ended_at = :ended_at');
                $criteria->addCondition('ip = :ip');
                $criteria->params = array(
                    ':dj_id'    => $user_id,
                    ':ended_at' => 0,
                    ':ip'       => $listener['ip'],
                );

                /** @var \RadioUser $RadioUser */
                $RadioUser = \RadioUser::model()->find($criteria);
                if(!$RadioUser) {
                    $start = new \DateTime();
                    $RadioUser = new \RadioUser();
                    $RadioUser
                        ->setDjId($user_id)
                        ->setRadioType($radio_type)
                        ->setStartedAt($start->getTimestamp())
                        ->setIp($listener['ip'])
                        ->setAgent($listener['user-agent'])
                        ->setDuration($listener['duration'])
                        ->setRadioSessionId($radio_session_id)
                        ->save();
                } else {
                    $RadioUser->setDuration($listener['duration'])
                        ->save();
                }

                $t->commit();

                $exclude_ids[] = $RadioUser->getId();
            } catch (\Exception $ex) {
                $t->rollback();
            }
        }


        $criteria = new \CDbCriteria();
        $criteria->addCondition('ended_at = :ended_at');
        if($exclude_ids) {
            $criteria->addNotInCondition('id', $exclude_ids);
        }
        $criteria->params[':ended_at'] = 0;

        $end_time = (new DateTime())->getTimestamp();
        RadioUser::model()->updateAll(['ended_at' => $end_time], $criteria);
	}
}
