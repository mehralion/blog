<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Николай
 * Date: 06.06.13
 * Time: 16:08
 * To change this template use File | Settings | File Templates.
 *
 * @var Post[] $models
 * @var CPagination $pages
 */
$this->breadcrumbs = array(
    Yii::app()->community->title => Yii::app()->createUrl('/community/request/show', array('community_alias' => Yii::app()->community->alias)),
    'Заметки',
);
?>
<?php if(Yii::app()->community->inCommunity()): ?>
<div class="buttons" style="margin-bottom: 10px;">
    <i class="icon" id="post_icon"></i>
    <div class="m_button">
        <?php echo CHtml::link(
            'Добавить заметку',
            Yii::app()->createUrl('/community/post/add', array('community_alias' => Yii::app()->community->alias)),
            array('id' => 'add_post', 'class' => 'fancybox.ajax btn2')
        ); ?>
    </div>
</div>
<?php endif; ?>
<div class="post">
    <?php foreach($models as $model): ?>
        <?php $this->renderPartial('common/post_out_view', array(
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
            $.fancybox({
                type        : 'ajax',
                openEffect  : 'none',
                closeEffect : 'none',
                href        : $self.attr('href'),
                afterClose  : function(){
                    if($('.sp-container').exists())
                        $('.sp-container').remove();
                }
            });
        });
    });
</script>