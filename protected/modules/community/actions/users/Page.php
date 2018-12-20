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
class Page extends CommunityAction
{
    public function run()
    {
        $criteria = new \CDbCriteria();
        $criteria->scopes = array('deletedStatus');
        $criteria->addCondition('`t`.community_id = :community_id');
        $criteria->params = array(':community_id' => \Yii::app()->community->id, ':deletedStatus' => 0);

        $pages = new \CPagination(\CommunityUser::model()
            ->count($criteria));
        $pages->pageSize = \Yii::app()->params['page_size']['community']['users'];
        $pages->applyLimit($criteria);

        /** @var \CommunityUser[] $models */
        $models = \CommunityUser::model()->findAll($criteria);

        \Yii::app()->message->setOther(array(
            'content' => $this->controller->renderPartial('short/users', array('models' => $models, 'pages' => $pages), true)
        ));

        \Yii::app()->message->showMessage();
    }
}