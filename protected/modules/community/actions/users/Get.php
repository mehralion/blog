<?php
namespace application\modules\community\actions\users;
use application\modules\community\components\CommunityAction;

/**
 * Created by JetBrains PhpStorm.
 * User: Николай
 * Date: 13.06.13
 * Time: 13:13
 * To change this template use File | Settings | File Templates.
 *
 * @package application.gallery.actions.image
 */
class Get extends CommunityAction
{
    public function run()
    {
        $returned = array();
        $criteria = new \CDbCriteria();
        $criteria->addCondition('`t`.community_id = :community_id');
        $criteria->scopes = array('deletedStatus', 'norModers');
        $criteria->params = array(':deletedStatus' => 0, ':community_id' => \Yii::app()->community->id);
        $criteria->with = array('user');
        $criteria->addSearchCondition('`user`.login', \Yii::app()->request->getParam('search'));

        /** @var \CommunityUser[] $models */
        $models = \CommunityUser::model()->findAll($criteria);
        foreach($models as $model)
            $returned[] = array(
                'login' => $model->user->login,
                'game_id' => $model->user->game_id
            );

        echo \CJSON::encode($returned);
    }
}