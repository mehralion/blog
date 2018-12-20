<?php
/**
 * Created by PhpStorm.
 * User: Николай
 * Date: 19.11.13
 * Time: 20:14
 *
 * @var Post[] $models
 * @var Pager $pages
 */ ?>

    <div class="post" style="margin-top: 10px;">
        <?php foreach($models as $model): ?>
            <?php $this->renderPartial('webroot.themes.'.Yii::app()->theme->name.'.views.modules.post.common.search', array(
                    'model' => $model,
                )); ?>
        <?php endforeach; ?>
    </div>
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