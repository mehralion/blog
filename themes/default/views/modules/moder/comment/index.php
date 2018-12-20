<?php
/**
 * Created by JetBrains PhpStorm.
 * User: nnick
 * Date: 27.06.13
 * Time: 18:44
 * To change this template use File | Settings | File Templates.
 *
 * @param Report $model
 */
?>


<?php $this->widget('bootstrap.widgets.TbGridView', array(
    'type'=>'striped bordered condensed',
    'dataProvider'=>$model->search(),
    'template'=>"{pager}\n{items}",
    'columns'=>array(
        array(
            'name'=>'sender',
            'header'=>'Чья жалоба',
            'type' => 'raw',
            'value' => '$data->sender->getFullLogin()'
        ),
        array(
            'name'=>'owner',
            'header'=>'Чей комментарий',
            'type' => 'raw',
            'value' => '$data->owner->getFullLogin()'
        ),
        array(
            'name'=>'create_datetime',
            'header'=>'Дата жалобы',
            'value' => 'date(Yii::app()->params["siteTimeFormat"], strtotime($data->create_datetime))'
        ),
        array(
            'class'=>'ext.GridButtons.Buttons',
            'htmlOptions'=>array('style'=>'width: 50px'),
            'labelExp' => false,
            'buttons' => array(
                'reset' => array(
                    'url' => 'Yii::app()->createUrl("/moder/comment/reset", array("id" => $data->id))',
                    'label' => 'Отклонить'
                ),
                'view' => array(
                    'options' => array(
                        'class' => 'fancybox.ajax view'
                    ),
                    'url' => 'Yii::app()->createUrl("/preview/comment", array("id" => $data->item_id))',
                ),
                'update' => array(
                    'url' => 'Yii::app()->createUrl("/moder/comment/accept", array("id" => $data->id))',
                    'label' => 'Удалить',
                    'options' => array(
                        'class' => 'fancybox.ajax accept'
                    ),
                )
            ),
            'template' => '{view}{update}{reset}'
        ),
    ),
)); ?>
<script>
    $(function(){
        $(document.body).on('click', '.view', function(event){
            event.preventDefault();
            var $self = $(this);
            $.fancybox.open({
                type:'ajax',
                openEffect:'none',
                closeEffect:'none',
                href:$self.attr('href')
            });
        });
        $(document.body).on('click', '.accept', function(event){
            event.preventDefault();
            var $self = $(this);
            $.fancybox.open({
                type:'ajax',
                openEffect:'none',
                closeEffect:'none',
                href:$self.attr('href')
            });
        });
    });
</script>