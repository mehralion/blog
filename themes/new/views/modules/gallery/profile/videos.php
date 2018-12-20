<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Николай
 * Date: 12.06.13
 * Time: 12:48
 * To change this template use File | Settings | File Templates.
 *
 * @var GalleryVideo[] $models
 * @var CPagination $pages
 */
$this->breadcrumbs = array(
    'Мои видео',
);

?>
<?php $this->widget('bootstrap.widgets.TbButton', array(
    'label'=>'Добавить видео',
    'url' => Yii::app()->createUrl('/gallery/video/add'),
    'type'=>'primary', // null, 'primary', 'info', 'success', 'warning', 'danger' or 'inverse'
    'size'=>'small', // null, 'large', 'small' or 'mini'
    'htmlOptions' => array(
        'id' => 'add_video',
        'class' => 'fancybox.ajax',
    )
)); ?>
<ul id="video">
    <?php foreach($models as $model): ?>
        <li class="item border">
            <?php $this->renderPartial('webroot.themes.'.Yii::app()->theme->name.'.views.modules.gallery.common.video', array(
                'model' => $model,
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
        $(document.body).on('click', '#add_video', function(event){
            event.preventDefault();
            var $self = $(this);
            $self.fancybox({
                openEffect  :'none',
                closeEffect :'none'
            });
        });
        $('#add_video').trigger('click');
        $('.fancybox-media').fancybox({
            openEffect  : 'none',
            closeEffect : 'none',
            helpers : {
                media : {}
            }
        });
    });
</script>