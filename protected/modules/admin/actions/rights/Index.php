<?php
namespace application\modules\admin\actions\rights;
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
        $criteria = new \CDbCriteria();
        $criteria->with = array('rights_type', 'user');
        $UserProvider = new \CActiveDataProvider('Rights', array(
            'criteria' => $criteria,
            'pagination'=>array(
                'pageSize' => 50,
            ),
        ));

        \Yii::app()->clientScript->registerScriptFile(\Yii::app()->baseUrl.'/js/chosen/chosen.jquery.min.js');
        \Yii::app()->clientScript->registerCssFile(\Yii::app()->baseUrl.'/js/chosen/chosen.min.css');

        $this->controller->render('index', array('provider' => $UserProvider, 'model' => new \Rights()));
    }
}