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
class Community extends \CAction
{
    public function run()
    {
        $criteria = new \CDbCriteria();
        $criteria->scopes = array('own', 'any');
        $criteria->with = array(
            'ownerCommunity' => array(
                'scopes' => array('truncatedStatus', 'deletedStatus', 'moderDeletedStatus'),
                'params' => array(':truncatedStatus' => 0, ':deletedStatus' => 0, ':moderDeletedStatus' => 0)
            )
        );

        $pages = new \CPagination(\SubscribeCommunity::model()
            ->count($criteria));
        $pages->pageSize = \Yii::app()->paramsWrap->pageSize->friend;
        $pages->applyLimit($criteria);

        /** @var \SubscribeCommunity[] $models */
        $models = \SubscribeCommunity::model()->findAll($criteria);

        $this->controller->render('community', array(
            'models' => $models,
            'pages' => $pages
        ));
    }
}