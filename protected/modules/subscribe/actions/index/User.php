<?php
namespace application\modules\subscribe\actions\index;
/**
 * Created by JetBrains PhpStorm.
 * User: Николай
 * Date: 13.06.13
 * Time: 13:13
 * To change this template use File | Settings | File Templates.
 *
 * @package application.gallery.actions.image
 */
class User extends \CAction
{
    public function run()
    {
        $criteria = new \CDbCriteria();
        $criteria->scopes = array('own', 'any');
        $criteria->with = array('ownerUser');

        $pages = new \CPagination(\SubscribeUser::model()
            ->count($criteria));
        $pages->pageSize = \Yii::app()->paramsWrap->pageSize->friend;
        $pages->applyLimit($criteria);

        /** @var \SubscribeUser[] $models */
        $models = \SubscribeUser::model()->findAll($criteria);

        $this->controller->render('user', array(
            'models' => $models,
            'pages' => $pages
        ));
    }
}