<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Николай
 * Date: 07.06.13
 * Time: 20:01
 * To change this template use File | Settings | File Templates.
 *
 * @var array $items
 * @var CPagination $pages
 */
$this->breadcrumbs = array(
    'Обновления' => false,
    'Фотографии' => false
);
foreach($items as $user_id => $userItems) {
    $added = $userItems['event']->albumInfo->is_community ? 'community.' : '';
    $this->renderPartial('webroot.themes.'.Yii::app()->theme->name.'.views.modules.event.common.events.item.'.$added.$userItems['event']->item_type.'_type', array(
            'event' => $userItems['event'],
            'items' => $userItems['items']
        ));
}
?>
<?php if(empty($items)): ?>
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