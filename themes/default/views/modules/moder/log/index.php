<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Николай
 * Date: 07.07.13
 * Time: 15:36
 * To change this template use File | Settings | File Templates.
 *
 * @var ModerLog $model
 * @var TbActiveForm $form
 */ ?>
<style>
    .filter label.control-label {
        width: 115px;
    }
    .filter .controls{
        margin-left: 150px;
    }
</style>
<div class="filter">
    <?php
    $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
        'id'=>'form-moder-log',
        'type'=>'horizontal',
        'enableAjaxValidation' => false,
    )); ?>
    <?php echo $form->radioButtonListInlineRow($model, 'item_type', CMap::mergeArray(array('' => 'Все'),ModerLog::getItemTypes()))?>
    <div class="" style="display: inline-block;vertical-align: top;">
        <?php echo $form->dropDownListRow($model, 'moder_id', CMap::mergeArray(array('' => 'Все'), User::model()->getModerList())); ?>
        <?php echo $form->dropDownListRow($model, 'operation_type', CMap::mergeArray(array('' => 'Все'), ModerLog::getOperationTypes())); ?>
    </div>
    <div class="" style="display: inline-block;vertical-align: top;">
        <div class="control-group">
            <?php echo $form->label($model, 'date_start', array('class' => 'control-label')) ?>
            <div class="controls">
                <?php $this->widget('bootstrap.widgets.TbDatePicker', array(
                    'model' => $model,
                    'attribute' => 'date_start',
                    'form' => $form,
                    'options' => array('format' => 'dd.mm.yyyy')
                )); ?>
            </div>
        </div>
        <div class="control-group">
            <?php echo $form->label($model, 'date_end', array('class' => 'control-label')) ?>
            <div class="controls">
                <?php $this->widget('bootstrap.widgets.TbDatePicker', array(
                    'model' => $model,
                    'attribute' => 'date_end',
                    'form' => $form,
                    'options' => array('format' => 'dd.mm.yyyy')
                )); ?>
            </div>
        </div>
    </div>
    <?php echo $form->checkBoxRow($model, 'is_last'); ?>
    <?php $this->endWidget(); ?>
</div>
<?php $this->widget('bootstrap.widgets.TbGridView', array(
    'id' => 'grid-log',
    'type'=>'striped bordered condensed',
    'dataProvider'=>$model->search(),
    'template'=>"{pager}\n{items}",
    //'selectionChanged' => 'js:function(id) { openLog(id, $.fn.yiiGridView.getSelection(id)); }',
    'rowCssClass' => array('odd parent', 'even parent'),
    'columns'=>array(
        array(
            'header' => 'Описание',
            'type' => 'raw',
            'value' => '$data->getDescriptionString()',
        ),
        array(
            'header' => 'Владелец',
            'type' => 'raw',
            'value' => '$data->owner->getFullLogin()'
        ),
        array(
            'name' => 'moder_reason',
            'type' => 'raw',
            'value' => 'StringHelper::doLink($data->moder_reason, "ссылка")'
        ),
        array(
            'name'=>'create_datetime',
            'header'=>'Дата',
            'value' => 'date(Yii::app()->params["siteTimeFormat"], strtotime($data->create_datetime))'
        ),
        array(
            'class'=>'ext.GridButtons.Buttons',
            'htmlOptions'=>array('style'=>'width: 50px'),
            'buttons' => array(
                'restore' => array(
                    'options' => array(
                        'class' => 'restore'
                    ),
                    'url' => '$data->getRestoreUrl()',
                    'label' => '$data->item_type == ItemTypes::ITEM_TYPE_SILENCE?"Снять":"Восстановить";',
                    'visible' => '$data->visibleRestore()'
                ),
                'history' => array(
                    'label' => '"История"',
                    'options' => array(
                        'class' => 'history',
                    ),
                    'url' => 'Yii::app()->createUrl("/moder/log/log", array("id" => $data->id))',
                    'visible' => "$model->is_last"
                )
            ),
            'template' => '{restore} {history}'
        ),
    ),
)); ?>
<script>
    $(function(){
        $(document.body).on('change', '#form-moder-log', function(){
            $.fn.yiiGridView.update('grid-log', {
                data: $(this).serialize()
            });
            return false;
        });
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
        $(document.body).on('click', '.restore', function(event){
            event.preventDefault();
            var $self = $(this);
            $.fancybox.open({
                type:'ajax',
                openEffect:'none',
                closeEffect:'none',
                href:$self.attr('href')
            });
        });
        $(document.body).on('click', 'a.history', function(event){
            event.preventDefault();
            var row=$(this).closest('tr');
            var id = $(this).attr('rel');
            if($('#subgrid_'+id).exists()) {
                $('#subgrid_'+id).toggle();
                return;
            }
            /*if(arrayList.in_array(id) !== false) {
                if($('#subgrid_'+id).exists())
                    $('#subgrid_'+id).toggle();
                else {
                    addSubGrid(row, arrayList[id], id);
                }
            } else {*/
                $.ajax({
                    url:$(this).attr('href'),
                    data:{
                        'ModerLog[item_type]':$('#ModerLog_item_type').val(),
                        'ModerLog[moder_id]':$('#ModerLog_moder_id').val(),
                        'ModerLog[operation_type]':$('#ModerLog_operation_type').val(),
                        'ModerLog[date_start]':$('#ModerLog_date_start').val(),
                        'ModerLog[date_end]':$('#ModerLog_date_end').val(),
                        'ModerLog[group]':$('#ModerLog_group').val()
                    },
                    success:function(response){
                        addSubGrid(row, response, id);
                        arrayList[id] = response;
                    }
                });
            //}

        })
    });

    var arrayList = [];
    function addSubGrid(row, grid, id) {
        var tr = $('<tr>', {'id':'subgrid_'+id, 'class':'sub'});
        $('<td>', {'colspan':5, 'html':grid}).appendTo(tr);
        row.after(tr);
    }
</script>