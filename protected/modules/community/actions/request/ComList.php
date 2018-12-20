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
class ComList extends \CAction
{
    public function run()
    {
        $this->controller->community = false;

        $id = \Yii::app()->request->getParam('category_id');

        /** @var \CommunityCategory $model */
        $model = \CommunityCategory::model()->findByPk($id);
        if(!$model)
            \MyException::ShowError(500, 'Категория не найдена');

        $criteria = new \CDbCriteria();
        $criteria->scopes = array('truncatedStatus', 'moderDeletedStatus', 'deletedStatus');
        $criteria->with = array('postCount', 'imageCount', 'videoCount', 'audioCount');
        $criteria->addCondition('`t`.category_id = :category_id');
        $criteria->params = array(':category_id' => $model->id, ':truncatedStatus' => 0, ':deletedStatus' => 0, ':moderDeletedStatus' => 0);

        $dependency = new \CDbCacheDependency('select max(update_datetime) from {{community}}');
        $dependency->reuseDependentData = true;

        $pages = new \CPagination(\Community::model()->cache(\Yii::app()->paramsWrap->cache->community)->count($criteria));
        $pages->pageSize = \Yii::app()->paramsWrap->pageSize->community_index;
        $pages->applyLimit($criteria);

        /** @var \Community[] $models */
        $models = \Community::model()->cache(\Yii::app()->paramsWrap->cache->community)->findAll($criteria);

        $this->controller->render('list', array('models' => $models, 'pages' => $pages, 'category' => $model));
    }
}