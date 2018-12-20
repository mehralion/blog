<?php
/**
 * Created by PhpStorm.
 * User: Николай
 * Date: 18.11.13
 * Time: 0:01
 *
 * @var SubscribeDebate $model
 * @var TbActiveForm $form
 */  ?>

<?php $this->widget('bootstrap.widgets.TbGridView', array(
    'id' => 'grid-subscribe',
    'type'=>'striped bordered condensed',
    'dataProvider'=>$model->search(),
    'template'=>"{pager}\n{items}",
    //'selectionChanged' => 'js:function(id) { openLog(id, $.fn.yiiGridView.getSelection(id)); }',
    'rowCssClass' => array('odd parent', 'even parent'),
    'emptyText' => 'К сожалению, у Вас пока нет подписок на дискуссии.',
    'columns'=>array(
        array(
            'header' => 'Название',
            'type' => 'raw',
            'value' => '$data->getTitle()',
        ),
        array(
            'header' => 'Владелец',
            'type' => 'raw',
            'value' => '$data->owner->getFullLogin()'
        ),
        array(
            'class'=>'ext.GridButtons.Buttons',
            'htmlOptions'=>array('style'=>'width: 50px'),
            'buttons' => array(
                'unsubscribe' => array(
                    'options' => array(
                        'class' => 'unsubscribe'
                    ),
                    'url' => 'Yii::app()->createUrl("/subscribe/request/deletedebate", array("id" => $data->id))',
                    'label' => 'Отписаться',
                    'confirm' => 'Вы уверены, что хотите отписаться?'
                ),
            ),
            'template' => '{unsubscribe}'
        ),
    ),
)); ?>

<script>
    $(function(){

        $(document.body).on('click', '.unsubscribe', function(event){
            event.preventDefault();
            if(confirm('Вы уверены, что хотите отписаться?')) {
                var $self = $(this);
                $.ajax({
                    url:$self.attr('href'),
                    dataType:'json',
                    beforeSend:function(){
                        return validate.beforeValidate();
                    },
                    success:function(){
                        updateGrid();
                    }
                });
            }
        });
    });

    function updateGrid()
    {
        $.fn.yiiGridView.update('grid-subscribe', {
            beforeSend:function(){
                return validate.beforeValidate();
            },
            data: $(this).serialize(),
            complete: function () {
                validate.afterValidate2({});
            }
        });
    }
</script>