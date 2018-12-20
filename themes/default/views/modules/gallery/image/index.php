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
    'Фотоальбомы' => Yii::app()->createUrl('/gallery/album/index', array('gameId' => Yii::app()->userOwn->game_id)),
    $album->title
);

?>
<div class="buttons" style="padding-left: 6px;">
    <div class="m_button">
        <span class="btn2" id="add_image">Загрузить фотографию</span>
    </div>
    <div class="m_button">
        <?php echo CHtml::link(
            'Редактировать альбом',
            Yii::app()->createUrl('/gallery/album/update', array('id' => Yii::app()->request->getParam('id'))
            ),
            array('class' => 'fancybox.ajax btn1', 'id' => 'edit_album_btn')); ?>
    </div>
    <div class="m_button">
        <?php echo CHtml::link(
            'Удалить альбом',
            Yii::app()->createUrl('/gallery/album/delete', array('id' => Yii::app()->request->getParam('id'))),
            array('class' => 'fancybox.ajax btn1 del')); ?>
    </div>
</div>
<div class="hidden block">
    <?php $this->renderPartial('webroot.themes.'.Yii::app()->theme->name.'.views.modules.gallery.image.form_add', array(
        'model' => $new,
        'album_id' => $album->id
    ), false, false); ?>
</div>
<ul class="image">
    <?php foreach($models as $model): ?>
        <li>
                <?php $this->renderPartial('webroot.themes.'.Yii::app()->theme->name.'.views.modules.gallery.common.image', array(
                    'model' => $model
                )); ?>
            <div class="buttons">
                <?php
                    echo CHtml::link(
                        '<i class="icon" id="edit" title="Редактировать"></i>',
                        Yii::app()->createUrl('/gallery/image/update', array('id' => $model->id)),
                        array('class' => 'edit fancybox.ajax', 'title' => 'Редактировать')
                    );
                echo '<span style="width:10px;display: inline-block"></span>';
                echo CHtml::link(
                    '<i class="icon" id="del" title="Удалить"></i>',
                    Yii::app()->createUrl('/gallery/image/delete', array('id' => $model->id)),
                    array('title' => 'Удалить', 'confirm' => 'Вы уверены, что хотите удалить эту фотографию?')
                );
                ?>
            </div>
        </li>
    <?php endforeach; ?>
</ul>
<?php if(empty($models)): ?>
    <div class="event_empty">Список пуст</div>
<?php endif; ?>
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
        $(document.body).on('click', '.edit', function(event){
            event.preventDefault();
            var $self = $(this);
            $self.fancybox({
                type: 'ajax',
                openEffect  :'none',
                closeEffect :'none',
                autoWidth   : true,
                autoHeight  : true,
                afterClose  : function(){
                    if($('.sp-container').exists())
                        $('.sp-container').remove();
                },
                afterShow   : function() {
                    setTimeout(function(){
                        $.fancybox.update();
                    }, 1500);
                }
            });
        });
        $(document.body).on('click', '.del', function(event){
            if(confirm('Вы уверены, что хотите удалить альбом?'))
                return true;
            else
                return false;
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
        $('.edit').trigger('click');
    });
</script>