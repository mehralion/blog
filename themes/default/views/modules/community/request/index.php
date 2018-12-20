<?php
/**
 * Created by PhpStorm.
 * User: Николай
 * Date: 02.01.14
 * Time: 9:40
 *
 * @var CommunityCategory[] $models
 */
$this->breadcrumbs = array(
    'Сообщества',
);
?>

<div id="categoryList">
    <?php
    if($this->beginCache('all_community', array('dependency' => Community::getTbDependency()))): ?>
        <?php foreach($models as $model): ?>
            <?php $this->renderPartial('themePath.views.modules.community.common.category', array('model' => $model)); ?>
        <?php endforeach; ?>
        <?php $this->endCache(); endif; ?>
</div>
<?php if(empty($models)): ?>
    <div class="event_empty">Список пуст</div>
<?php endif; ?>