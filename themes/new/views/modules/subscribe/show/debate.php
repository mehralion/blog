<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Николай
 * Date: 07.06.13
 * Time: 18:29
 * To change this template use File | Settings | File Templates.
 *
 * @var CPagination $pages
 * @var EventComment[] $models
 */
/* @var $models EventComment[] */
$this->breadcrumbs = array(
    'Подписки - Дискуссии',
);
?>
<?php 
foreach($models as $model): ?>
    <?php
    $added = '';
    if($model->info->is_community)
        $added = 'community.';
    $this->renderPartial('webroot.themes.'.Yii::app()->theme->name.'.views.modules.subscribe.common.item.'.$added.$model->item_type.'_type', array(
        'model' => $model
    )); ?>
<?php endforeach; ?>
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
    var send = false;
    $(function(){
        $(document.body).on('click', '.show_comment', function(event){
            if(send)
                return;
            
            var $self = $(this);
            var $newBlock = $self.parent().find('.new');
            var id = $self.attr('block_id');
            if(!$self.attr('get')) {
                $.ajax({
                    url:$self.attr('link'),
                    dataType:'json',
                    beforeSend:function(){
                        send = true;
                        return validate.beforeValidate();
                    },
                    success:function(response) {
                        $self.attr('get', true);
                        send = false;
                        validate.afterValidate2({});
                        if(response.content !== undefined)
                            $('#'+id).html(response.content);
                        
                        if($newBlock.length && response.ok !== undefined)
                            $newBlock.remove();
                        
                        showBlock($self, id);
                    }
                });
            }
            
            if(!send)
                showBlock($self, id);
        });
    });
    function showBlock($self, id) {
        if($self.attr('position') == '0')
                $self.html('Закрыть').attr('position', 1);
            else
                $self.html('Просмотреть').attr('position' , 0);

            $('#'+id).toggle();
    }
</script>