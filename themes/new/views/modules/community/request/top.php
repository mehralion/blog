<?php
/**
 * Created by PhpStorm.
 * User: Николай
 * Date: 02.01.14
 * Time: 9:40
 *
 * @var Community[] $models
 */

$this->breadcrumbs = array(
    'Топ сообщества'
);
?>

<ul class="top community">
    <?php foreach($models as $model): ?>
        <li>
            <?php $this->renderPartial('webroot.themes.'.Yii::app()->theme->name.'.views.modules.community.common.short_top', array(
                'model' => $model
            )); ?>
        </li>
    <?php endforeach; ?>
</ul>
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