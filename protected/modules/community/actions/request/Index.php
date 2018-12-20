<?php
namespace application\modules\community\actions\request;
/**
 * Created by JetBrains PhpStorm.
 * User: Николай
 * Date: 13.06.13
 * Time: 13:13
 * To change this template use File | Settings | File Templates.
 *
 * @package application.gallery.actions.image
 */
class Index extends \CAction
{
    public function run()
    {
        $this->controller->community = false;
        $criteria = new \CDbCriteria();
        $criteria->with = array('hasCommunity');

        /** @var \CommunityCategory[] $models */
        $models = \CommunityCategory::model()->findAll($criteria);

        $this->controller->render('index', array('models' => $models));
    }
}