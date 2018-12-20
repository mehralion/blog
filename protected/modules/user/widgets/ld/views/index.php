<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Николай
 * Date: 04.06.13
 * Time: 20:41
 * To change this template use File | Settings | File Templates.
 */?>
<div class="dark_block">
    Личное дело:
<?php $this->widget('bootstrap.widgets.TbGridView', array(
    'id' => 'grid-log',
    'type'=>'striped bordered condensed',
    'dataProvider'=>$dataProvider,
    'template'=>"{pager}\n{items}",
    'columns'=>array(
        array(
            'type' => 'raw',
            'value' => '$data->getDescriptionString()'
        ),
        array(
            'type' => 'raw',
            'value' => '$data->moder_reason'
        ),
        array(
            'name'=>'create_datetime',
            'header'=>'Дата',
            'value' => 'date(Yii::app()->params["siteTimeFormat"], strtotime($data->create_datetime))'
        ),
    ),
)); ?>

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
</div>

<script>
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
</script>