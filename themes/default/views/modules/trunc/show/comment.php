<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Николай
 * Date: 08.10.13
 * Time: 17:49
 * To change this template use File | Settings | File Templates.
 *
 * @var CommentItem[] $models
 * @var CPagination $pages
 */

$this->breadcrumbs = array(
    'Корзина - Комментарии',
);

foreach($models as $model):
    $model->quote = false; ?>
    <?php $this->renderPartial('webroot.themes.'.Yii::app()->theme->name.'.views.modules.comment.common.item', array(
        'model' => $model,
        'quote' => false
    )); ?>
<?php endforeach; ?>
<?php if(empty($models)): ?>
    <div class="event_empty">Список пуст</div>
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