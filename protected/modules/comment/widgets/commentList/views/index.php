<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Николай
 * Date: 04.06.13
 * Time: 20:41
 * To change this template use File | Settings | File Templates.
 *
 * @var CommentItem[] $models
 * @var string $url
 * @var CommentItem $newModel
 *
 * @uses CommentItem[] $models The user model
 */?>
<? $this->widget('ext.pagination.Pager', array(
    //'cssFile' => '',
    'internalPageCssClass' => 'btn',
    'pages' => $pages,
    'header' => '',
    'selectedPageCssClass' => 'active',
    'htmlOptions' => array(
        'class' => 'btn-group pagination',
    )
)); ?>
<div class="comment_list">
    <?php foreach($models as $model):
        $model->quote = $quote;
        ?>
        <?php $this->render('webroot.themes.'.Yii::app()->theme->name.'.views.modules.comment.common.item', array(
            'model' => $model
        )); ?>
    <?php endforeach; ?>
</div>
<?php if(empty($models)): ?>
        <div class="event_empty">Комментарии отсутствуют</div>
<?php endif; ?>
<? $this->widget('ext.pagination.Pager', array(
    //'cssFile' => '',
    'internalPageCssClass' => 'btn',
    'pages' => $pages,
    'header' => '',
    'selectedPageCssClass' => 'active',
    'htmlOptions' => array(
        'class' => 'btn-group pagination',
    )
)); ?>
<?php if($this->model->rights->canComment())
    $this->widget('application.widgets.editor.EditorWidget', array('url' => $url, 'model' => $newModel, 'attributeName' => 'description')); ?>