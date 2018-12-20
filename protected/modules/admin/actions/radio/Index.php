<?php
namespace application\modules\admin\actions\radio;
/**
 * Created by JetBrains PhpStorm.
 * User: Николай
 * Date: 13.06.13
 * Time: 13:13
 * To change this template use File | Settings | File Templates.
 *
 * @package application.post.actions.index
 */
class Index extends \CAction
{
    public function run()
    {
        $LogAjax = \Yii::app()->request->getParam('Log');
        $ResultAjax = \Yii::app()->request->getParam('Result');
        $RadioLogAjax = \Yii::app()->request->getParam('RadioLog');

        $_started_at = strtotime($ResultAjax['date_start'].' 00:00:00');
        $_ended_at = strtotime($ResultAjax['date_end'].' 23:59:59');

        /** @var \RadioSettings $RadioSettings */
        $RadioSettings = \RadioSettings::getSettings();

        $criteria = new \CDbCriteria();
        $criteria->order = 'start_datetime desc';
        if($LogAjax) {
            if($LogAjax['user_id']) {
                $criteria->addCondition('user_id = :user_id');
                $criteria->params = array(':user_id' => $LogAjax['user_id']);
            }

            if($LogAjax['date_start']) {
                $criteria->addCondition('start_datetime >= :start_datetime');
                $criteria->params = \CMap::mergeArray(
                    $criteria->params,
                    array(':start_datetime' => date('Y-m-d H:i:s', strtotime($LogAjax['date_start'].' 00:00:00')))
                );
            }

            if($LogAjax['date_end']) {
                $criteria->addCondition('start_datetime < :end_datetime');
                $criteria->params = \CMap::mergeArray(
                    $criteria->params,
                    array(':end_datetime' => date('Y-m-d H:i:s', strtotime($LogAjax['date_end'].' 23:59:59')))
                );
            }
        }
        $RadioProvider = new \CActiveDataProvider('Radio', array(
            'criteria' => $criteria,
            'pagination'=>array(
                'pageSize' => 50,
            ),
        ));

        $criteria = new \CDbCriteria();
        $criteria->addCondition('`t`.log_level >= :log_level');
        $criteria->params = array(':log_level' => \LogRadio::LEVEL_MIN_READ);
        $criteria->order = '`t`.create_datetime desc';
        if($RadioLogAjax) {
            if(isset($RadioLogAjax['date_start'])) {
                $criteria->addCondition('`t`.create_datetime >= :date_start');
                $criteria->params = \CMap::mergeArray($criteria->params, array(
                    ':date_start' => \DateTimeFormat::format(null, strtotime($RadioLogAjax['date_start'].' 00:00:00'))
                ));
            }
            if(isset($RadioLogAjax['date_end'])) {
                $criteria->addCondition('`t`.create_datetime <= :date_end');
                $criteria->params = \CMap::mergeArray($criteria->params, array(
                    ':date_end' => \DateTimeFormat::format(null, strtotime($RadioLogAjax['date_end'].' 23:59:59'))
                ));
            }
        }

        $RadioLogProvider = new \CActiveDataProvider('LogRadio', array(
            'criteria' => $criteria,
            'pagination'=>array(
                'pageSize' => 50,
            ),
        ));

        $userList = array();

        /** @var \UserDj[] $Djs */
        $Djs = \UserDj::model()->findAll();
        foreach($Djs as $Dj) {
            if(array_key_exists($Dj->user_id, $userList)) {
                $userList[$Dj->user_id]['total'] += $Dj->hours_static;
                continue;
            }

            $sum = $Dj->hours_static;

            $criteria = new \CDbCriteria();
            $criteria->select = 'sum(`t`.sum_hours) as sum';
            $criteria->addCondition('user_id = :user_id');
            $criteria->params = array(':user_id' => $Dj->user_id);
            /** @var \Radio $model */
            $model = \Radio::model()->find($criteria);
            if($model)
                $sum += $model->sum;

            $criteria = new \CDbCriteria();
            $criteria->addCondition('duration >= :duration');
            $criteria->addCondition('dj_id = :id');
            $criteria->addCondition('started_at >= :started_at');
            $criteria->addCondition('ended_at <= :ended_at');
            $criteria->params = [
                ':duration' => 30 * 60,
                ':id' => $Dj->user_id,
                ':started_at' => $_started_at,
                ':ended_at' => $_ended_at
            ];
            $user_track_count = \RadioUser::model()->count($criteria);

            $list = array(
                'id' => $Dj->user_id,
                'login' => $Dj->user->getFullLogin(),
                'total' => $sum,
                'coef' => 0,
                'totalHours' => 0,
                'finish_shtraf' => 0,
                'finish_captcha' => 0,
                'user_count' => $user_track_count,
                'rus' => false,
                'old' => false
            );
            $criteria = new \CDbCriteria();
            $criteria->addCondition('user_id = :user_id');
            $criteria->addCondition('is_online = 0');
            if($ResultAjax) {
                if($ResultAjax['date_start']) {
                    $criteria->addCondition('start_datetime >= :start_datetime');
                    $criteria->params = array(':start_datetime' => date('Y-m-d H:i:s', strtotime($ResultAjax['date_start'].' 00:00:00')));
                }

                if($ResultAjax['date_end']) {
                    $criteria->addCondition('start_datetime < :end_datetime');
                    $criteria->params = \CMap::mergeArray(
                        $criteria->params,
                        array(':end_datetime' => date('Y-m-d H:i:s', strtotime($ResultAjax['date_end'].' 23:59:59')))
                    );
                }
            }

            $criteria->params = \CMap::mergeArray(
                $criteria->params,
                array(':user_id' => $Dj->user_id)
            );

            /** @var \Radio[] $Radios */
            $Radios = \Radio::model()->findAll($criteria);
            foreach($Radios as $item) {
                if($item->finish_type == \Radio::FINISH_NO_CHECK && strtotime($item->end_datetime) > strtotime('2014-05-01 00:00:00'))
                    $list['finish_shtraf']++;

                if($item->radio_type == \Radio::RADIO_TYPE_RUSFM)
                    $list['rus'] = true;
                elseif($item->radio_type == \Radio::RADIO_TYPE_OLDFM)
                    $list['old'] = true;

                $list['totalHours'] += $item->sum_hours;

                //$end = strtotime($item->end_datetime);
                //$begin = strtotime($item->start_datetime);

                //$list['totalHours'] += round(($end - $begin) / 3600, 2);
            }

            $userList[$Dj->user_id] = $list;
        }

        foreach($userList as $gameId => $values)
            $userList[$gameId]['coef'] = \Radio::getTax($values['total']);

        $resultGrid = new \CArrayDataProvider($userList, array(
            'pagination' => array(
                'pageSize' => 50
            )
        ));

        $this->controller->render('index', array(
            'model' => $RadioSettings,
            'radio' => new \Radio(),
            'radioGrid' => $RadioProvider,
            'resultGrid' => $resultGrid,
            'radioLogProvider' => $RadioLogProvider,
        ));
    }
}