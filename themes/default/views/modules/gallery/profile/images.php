<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Николай
 * Date: 05.06.13
 * Time: 14:00
 * To change this template use File | Settings | File Templates.
 *
 * @var GalleryAlbum $album
 * @var GalleryImage[] $models
 * @var CPagination $pages
 */

$this->breadcrumbs = array(
    'Альбомы' => Yii::app()->createUrl('/gallery/profile/albums'),
    $album->title
);

?>
<?php $this->widget('bootstrap.widgets.TbButton', array(
    'label'=>'Редактировать альбом',
    'type'=>'success', // null, 'primary', 'info', 'success', 'warning', 'danger' or 'inverse'
    'size'=>'small', // null, 'large', 'small' or 'mini'
    'url' => Yii::app()->createUrl('/gallery/album/update', array('id' => Yii::app()->request->getParam('id'))),
    'htmlOptions' => array(
        'id' => 'edit_album_btn',
        'class' => 'fancybox.ajax'
    )
)); ?>
<?php $this->widget('bootstrap.widgets.TbButton', array(
    'label'=>'Удалить альбом',
    'type'=>'danger', // null, 'primary', 'info', 'success', 'warning', 'danger' or 'inverse'
    'size'=>'small', // null, 'large', 'small' or 'mini'
    'url' => Yii::app()->createUrl('/gallery/album/delete', array('id' => Yii::app()->request->getParam('id'))),
    'htmlOptions' => array(
        'style' => 'margin-left:10px;'
    )
)); ?>
<?php $this->widget('bootstrap.widgets.TbButton', array(
    'label'=>'Загрузить фото',
    'type'=>'primary', // null, 'primary', 'info', 'success', 'warning', 'danger' or 'inverse'
    'size'=>'small', // null, 'large', 'small' or 'mini'
    'htmlOptions' => array(
        'id' => 'add_image',
        'style' => 'margin-left:10px;'
    )
)); ?>
<div class="hidden block">
    <?php $this->renderPartial('webroot.themes.'.Yii::app()->theme->name.'.views.modules.gallery.image.form_add', array(
        'model' => $new
    ), false, false); ?>
</div>
<ul id="image">
    <?php foreach($models as $model): ?>
        <li class="item border">
            <?php $this->renderPartial('webroot.themes.'.Yii::app()->theme->name.'.views.modules.gallery.common.image', array(
                'model' => $model
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
        $(document.body).on('click', '#add_image', function(){
            $('.hidden.block').toggle();
        });
        $(document.body).on('click', '#image a.various', function(event){
            event.preventDefault();
            var $self = $(this);
            $self.fancybox({
                type: 'ajax',
                openEffect  :'none',
                closeEffect :'none',
                href        : '<?php echo Yii::app()->createUrl('/gallery/image/update'); ?>?id='+$self.attr("rel")
            });
        });
        $(document.body).on('click', '#edit_album_btn', function(event){
            event.preventDefault();
            var $self = $(this);
            $self.fancybox({
                openEffect  :'none',
                closeEffect :'none',
                href        : $self.attr("href")
            });
        });
        $('#edit_album_btn').trigger('click');
        $('#image a.various').trigger('click');
    });
</script>