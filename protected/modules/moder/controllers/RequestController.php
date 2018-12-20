<?php
namespace application\modules\moder\controllers;
/**
 * Created by JetBrains PhpStorm.
 * User: Nick Nikitchenko
 * Skype: quietasice
 * E-mail: quietasice123@gmail.com
 * Date: 15.07.13
 * Time: 17:42
 * To change this template use File | Settings | File Templates.
 *
 * @package application.moder.controllers
 */
class RequestController extends \FrontController
{
    public function filters()
    {
        return array(
            'accessControl',
        );
    }

    public function accessRules()
    {
        return array(
            array('deny',
                'users' => array('?'),
            )
        );
    }

    private $_rapUrl = 'http://capitalcity.oldbk.com/rap_api.php?key=SADq12saA';
    private $_ignore = 'http://capitalcity.oldbk.com/list_clans.php';
    private $_clans  = 'https://oldbk.com/api/clans_webs.php';

    public function actionRap()
    {
        $result = \Yii::app()->curl->run($this->_rapUrl);
        if(false === $result)
            $this->redirect(\Yii::app()->request->getUrlReferrer());

        $urls = \CJSON::decode($result);
        $this->getList($urls, \RapUrl::TYPE_RAP);

        $result = \Yii::app()->curl->run($this->_ignore);
        if(false === $result)
            $this->redirect(\Yii::app()->request->getUrlReferrer());

        $urls = explode(',', $result);
        $this->getList($urls, \RapUrl::TYPE_IGNORE);

        $result = \Yii::app()->curl->run($this->_clans);
        if(false === $result)
            $this->redirect(\Yii::app()->request->getUrlReferrer());

        $urls = array();
        $links = \CJSON::decode($result);
        foreach($links as $item)
            $urls[] = str_replace(array('\\', 'http://', 'www.', '/'), '', $item['homepage']);

        $this->getList($urls, \RapUrl::TYPE_CLANS);
    }

    private function getList($urls, $type)
    {
        $criteria = new \CDbCriteria();
        $criteria->addCondition('value = :value');
        $criteria->addCondition('type = :type');

        foreach($urls as $value) {
            $criteria->params = array(':value' => $value, ':type' => $type);
            $Link = \RapUrl::model()->find($criteria);
            if(isset($Link))
                continue;

            $model = new \RapUrl();
            $model->type = $type;
            $model->value = $value;
            $model->save();
        }
    }

    private $_smiles = 'http://capitalcity.oldbk.com/smiles.php';
    public function actionSmiles()
    {
        $result = \Yii::app()->curl->run($this->_smiles);
        if(false === $result)
            $this->redirect(\Yii::app()->request->getUrlReferrer());

        if(!preg_match_all('/s\("(.+?)"\)/i', $result, $out))
            $this->redirect(\Yii::app()->request->getUrlReferrer());

        $criteria = new \CDbCriteria();
        $criteria->addCondition('smile = :smile');
        foreach($out[1] as $item) {
            $criteria->params = array(':smile' => $item);
            $model = \Smiles::model()->find($criteria);
            if(null !== $model)
                continue;

            $url = 'https://i.oldbk.com/i/smiles/'.$item.'.gif';
            $img = \Yii::app()->basePath.'/../smiles/'.$item.'.gif';
            file_put_contents($img, file_get_contents($url));

            $model = new \Smiles();
            $model->smile = $item;
            $model->access = \Smiles::ACCESS_PUBLIC;
            $model->save();
        }
    }
}