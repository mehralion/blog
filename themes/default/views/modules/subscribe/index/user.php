<?php
/**
 * Created by PhpStorm.
 * User: Николай
 * Date: 17.11.13
 * Time: 23:40
 *
 * @var SubscribeUser[] $models
 * @var Pager $pages
 */
?>
    <ul class="friend">
        <?php foreach ($models as $model): ?>
            <li>
                <?php $this->renderPartial('webroot.themes.' . Yii::app()->theme->name . '.views.modules.subscribe.common.user', array(
                        'model' => $model,
                    )); ?>
            </li>
        <?php endforeach; ?>
    </ul>
<?php if (empty($models)): ?>
    <div class="event_empty">
        К сожалению, у Вас пока нет подписок.
        Для того, чтобы подписаться, зайдите на страницу блога пользователя и нажмите "Подписаться".
    </div>
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
        $(document.body).on('click', '.subscribe_update', function(event){
            event.preventDefault();
            var $self = $(this);
            $.fancybox.open({
                type:'ajax',
                openEffect:'none',
                closeEffect:'none',
                href:$self.attr('href')
            });
        });

        $(document.body).on('click', '.subscribe_delete', function(event){
            event.preventDefault();
            var $self = $(this);
            if(confirm('Вы точно хотите удалить подписку?')) {
                $.ajax({
                    url:$self.attr('href'),
                    dataType:'json',
                    beforeSend:function(){
                        validate.beforeValidate();
                    },
                    success:function(response){
                        validate.afterValidate2(response);
                    }
                });
            }
        });
    });
</script>