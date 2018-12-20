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
class Report extends \CAction
{
    public function run()
    {
        \Yii::import('ext.csv.ECSVExport');

        $ReportPost = \Yii::app()->request->getParam('Report');

        $_started_at = strtotime($ReportPost['start'].' 00:00:00');
        $_ended_at = strtotime($ReportPost['end'].' 23:59:59');

        $total_connect = 0;
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

            $list = array(
                'game_id' => trim($Dj->user->game_id),
                'login' => trim(iconv('utf8', 'windows-1251', $Dj->user->login)),
                'bank' => trim($Dj->game_bank),
                'price' => 0,
                'total' => $sum,
                'finish_shtraf' => 0,
                'hoursInSession' => 0
            );
            $criteria = new \CDbCriteria();
            $criteria->addCondition('user_id = :user_id');
            $criteria->addCondition('is_online = 0');
            $criteria->params = array(':user_id' => $Dj->user_id);
            if($ReportPost) {
                if($ReportPost['start']) {
                    $criteria->addCondition('start_datetime >= :start_datetime');
                    $criteria->params = \CMap::mergeArray(
                        $criteria->params,
                        array(':start_datetime' => date('Y-m-d H:i:s', strtotime($ReportPost['start'].' 00:00:00')))
                    );
                }

                if($ReportPost['end']) {
                    $criteria->addCondition('start_datetime < :end_datetime');
                    $criteria->params = \CMap::mergeArray(
                        $criteria->params,
                        array(':end_datetime' => date('Y-m-d H:i:s', strtotime($ReportPost['end'].' 23:59:59')))
                    );
                }
            }

            /** @var \Radio[] $Radios */
            $Radios = \Radio::model()->findAll($criteria);
            foreach($Radios as $item) {
                if($item->finish_type == \Radio::FINISH_NO_CHECK && strtotime($item->end_datetime) > strtotime('2014-05-01 00:00:00'))
                    $list['finish_shtraf']++;

                $list['hoursInSession'] += $item->sum_hours;
            }

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

            /** @var int $connect_count */
            $connect_count = \RadioUser::model()->count($criteria);

            $total_connect += $connect_count;
            $list['user_count'] = $connect_count;

            $userList[$Dj->user_id] = $list;
        }

        $total_price = 0;
        foreach($userList as $userId => $values) {
            $userList[$userId]['price'] = \Radio::getPrice($values['finish_shtraf'], $values['hoursInSession'], $values['total']);
            unset($userList[$userId]['finish_shtraf']);
            unset($userList[$userId]['hoursInSession']);
            unset($userList[$userId]['total']);

            $total_price += $userList[$userId]['price'];
        }
        $t = 0;
        //$diff = (2500 - $total_price) / $total_connect;
        foreach($userList as $userId => $values) {
            $userList[$userId]['price'] += 0.2 * $values['user_count'];
            $t += $userList[$userId]['price'];

            unset($userList[$userId]['user_count']);
        }

        $csv = new \ECSVExport($userList);
        $csv->setDelimiter(';');
        $csv->setEnclosure('"');
        $csv->includeColumnHeaders = false;
        $content = $csv->toCSV();
        \Yii::app()->getRequest()->sendFile('report_'.date('Y-m-d').'.csv', $content, "text/csv", false);
        \Yii::app()->end();
    }
}