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
class Invite extends CommunityAction
{
    public function run()
    {
        $this->controller->layout = 'community/users';

        $criteria = new \CDbCriteria();
        $criteria->scopes = array('requestStatus');
        $criteria->addCondition('`t`.community_id = :community_id');
        $criteria->addCondition('`t`.isInvite = 1');
        $criteria->params = array(
            ':community_id' => \Yii::app()->community->id, ':requestStatus' => \CommunityRequest::STATUS_PENDING
        );

        $pages = new \CPagination(\CommunityRequest::model()
            ->count($criteria));
        $pages->pageSize = \Yii::app()->params['page_size']['community']['users'];
        $pages->applyLimit($criteria);

        /** @var \CommunityRequest[] $models */
        $models = \CommunityRequest::model()->findAll($criteria);

        if(!\Yii::app()->request->isAjaxRequest)
            $this->controller->render('invite', array(
                'models' => $models,
                'pages' => $pages
            ));
        else {
            \Yii::app()->message->setOther(array(
                'content' => $this->controller->renderPartial('short/invite', array(
                        'models' => $models,
                        'pages' => $pages
                    ), true)
            ));
            \Yii::app()->message->showMessage();
        }
    }
}