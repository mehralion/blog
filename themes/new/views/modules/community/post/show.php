<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Николай
 * Date: 06.06.13
 * Time: 16:38
 * To change this template use File | Settings | File Templates.
 *
 * @var Post $model
 */

$this->breadcrumbs = array(
    Yii::app()->community->title => Yii::app()->createUrl('/community/request/show', array('community_alias' => $model->community_alias)),
    'Заметки' => Yii::app()->createUrl('/community/post/index', array('community_alias' => $model->community_alias)),
    $model->title
);

$this->renderPartial('common/post_inner_view', array(
    'model' => $model,
    'pollId' => $pollId
));
    $this->widget('application.modules.comment.widgets.commentList.CommentListWidget', array(
        'item_type' => ItemTypes::ITEM_TYPE_POST,
        'model' => $model,
        'url' => Yii::app()->createUrl('/comment/post/add', array('id' => $model->id, 'community_alias' => $model->community_alias))
    ));
?>