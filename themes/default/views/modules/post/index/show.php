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
$this->renderPartial('webroot.themes.'.Yii::app()->theme->name.'.views.modules.post.common.post_inner_view', array(
    'model' => $model,
    'pollId' => $pollId
));
    $this->widget('application.modules.comment.widgets.commentList.CommentListWidget', array(
        'item_type' => ItemTypes::ITEM_TYPE_POST,
        'model' => $model,
        'url' => Yii::app()->createUrl('/comment/post/add', array('id' => $model->id, 'gameId' => $model->user->game_id))
    ));
?>