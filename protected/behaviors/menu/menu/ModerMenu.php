<?php
/**
 * Class ModerMenu Меню модератора
 * Created by JetBrains PhpStorm.
 * User: Николай
 * Date: 14.06.13
 * Time: 20:08
 * To change this template use File | Settings | File Templates.
 *
 * @package application.behaviors.menu
 */
class ModerMenu
{
    /**
     * @return array
     */
    public function run()
    {
        $radio_status = Yii::app()->user->access(AccessTypes::RADIO_STATUS);
        if(Yii::app()->user->isModer() || Yii::app()->user->isAdmin() || $radio_status) {
            $criteria = new CDbCriteria();
            $criteria->scopes = array('open');
            $ReportCount = Report::model()->count($criteria);
            return array(
                array(
                    'label'=>'МОДЕРАЦИЯ',
                    'linkOptions' => array('class' => 'title'),
                    'linkLabelWrapper' => 'h2',
                    'itemOptions' => array('class' => 'title')
                ),
                array(
                    'label'=>'Жалобы ['.$ReportCount.']',
                    'url'=> array('/moder/post/index'),
                    'visible' => Yii::app()->user->isModer(),
                ),
                array(
                    'label'=>'Админ',
                    'url'=> array('/admin/user/index'),
                    'visible' => Yii::app()->user->isAdmin(),
                ),
                array(
                    'label' => 'Радио',
                    'url' => array('/admin/radio/index'),
                    'visible' => $radio_status
                ),
            );
        } else
            return array();
    }
}