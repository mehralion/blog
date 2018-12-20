<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Николай
 * Date: 05.06.13
 * Time: 14:00
 * To change this template use File | Settings | File Templates.
 *
 * @var GalleryAlbum[] $models
 * @var CPagination $pages
 */
$this->breadcrumbs = array(
    'Альбомы',
);
?>

<?php $this->widget('bootstrap.widgets.TbButton', array(
    'label'=>'Добавить альбом',
    'type'=>'primary', // null, 'primary', 'info', 'success', 'warning', 'danger' or 'inverse'
    'size'=>'small', // null, 'large', 'small' or 'mini'
    'url' => Yii::app()->createUrl('/gallery/album/add'),
    'htmlOptions' => array(
        'id' => 'add_album',
        'class' => 'fancybox.ajax'
    )
)); ?>
<ul id="album">
    <?php foreach($models as $model): ?>
        <li class="album border">
            <?php $this->renderPartial('webroot.themes.'.Yii::app()->theme->name.'.views.modules.gallery.common.album', array(
                'model' => $model,
                'route' => '/gallery/profile/images'
            )); ?>
        </li>
    <?php endforeach; ?>
</ul>
<? $this->widget('ext.pagination.Pager', array(
    'internalPageCssClass' => 'btn',
    'pages' => $pages,
    'header' => '',
    'selectedPageCssClass' => 'active',
    'htmlOptions' => array(
        'class' => 'btn-group pagination',
    )
)); ?>
<script>
    $(function(){
        $(document.body).on('click', '#add_album', function(event){
            event.preventDefault();
            var $self = $(this);
            $self.fancybox({
                openEffect  : 'none',
                closeEffect : 'none',
                href        : $self.attr('href')
            });
        });
        $('#add_album').trigger('click')
    });
</script>