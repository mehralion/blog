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
    'Состою в сообществе'
);
?>
<ul class="top community" style="margin-top: 10px;">
    <?php foreach($models as $model): ?>
        <li>
        <?php $this->renderPartial('webroot.themes.'.Yii::app()->theme->name.'.views.modules.community.common.short', array(
            'model' => $model
        )); ?>
        </li>
    <?php endforeach; ?>
</ul>
<?php if(empty($models)): ?>
    <div class="event_empty">Список пуст</div>
<?php endif; ?>