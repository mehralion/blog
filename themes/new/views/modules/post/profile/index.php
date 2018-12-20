<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Николай
 * Date: 05.06.13
 * Time: 19:35
 * To change this template use File | Settings | File Templates.
 *
 * @var Post[] $models
 * @var CPagination $pages
 */
$this->breadcrumbs = array(
    'Заметки'
);
?>
<div class="buttons">
    <i class="icon" id="post_icon"></i>
    <div class="m_button">
        <?php echo CHtml::link('Добавить заметку', Yii::app()->createUrl('/post/profile/add'), array('id' => 'add_post', 'class' => 'fancybox.ajax btn2')); ?>
    </div>
</div>
<div class="post" style="margin-top: 10px;">
    <?php foreach($models as $model): ?>
        <?php $this->renderPartial('webroot.themes.'.Yii::app()->theme->name.'.views.modules.post.common.post_out_view', array(
            'model' => $model
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

<script>
    $(function(){
        $(document.body).on('click', '#add_post', function(event){
            event.preventDefault();
            var $self = $(this);
            $self.fancybox({
                openEffect:'none',
                closeEffect:'none',
                href:$self.attr('href'),
                afterClose  : function(){
                    if($('.sp-container').exists())
                        $('.sp-container').remove();
                }
            });
        });
        $('#add_post').trigger('click');
    });
</script>