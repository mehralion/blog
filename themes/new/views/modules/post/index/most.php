<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Николай
 * Date: 06.06.13
 * Time: 16:08
 * To change this template use File | Settings | File Templates.
 *
 * @uses CPagination $pages
 * @uses Post[] $models
 * @var CPagination $pages
 * @var Post[] $models
 */ ?>

<?php foreach($models as $model): ?>
    <?php $view = 'webroot.themes.'.Yii::app()->theme->name.'.views.modules.post.common.post_out_view';
    if($model->is_community)
        $view = 'webroot.themes.'.Yii::app()->theme->name.'.views.modules.community.post.common.post_out_view';
    $this->renderPartial($view, array('model' => $model)); ?>
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