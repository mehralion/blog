<style>
    #container {
        min-height: 500px;
    }
</style>
<?php
/** @var TbActiveForm $form */
$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
    'id'=>'form-report',
    'action' => Yii::app()->createUrl('/admin/rights/add'),
    'type'=>'inline',
    'enableAjaxValidation' => false,
)); ?>
<?php echo $form->textFieldRow($model, 'user_id', array('placeholder' => 'Пользователь...', 'labelOptions' => array('label' => false))); ?>
<?php //echo $form->dropDownListRow($model, 'user_id', CHtml::listData(User::model()->findAll(array('order' => 'login asc')), 'id', 'login'), array('labelOptions' => array('label' => false))) ?>
<?php echo $form->dropDownListRow($model, 'item_id', CHtml::listData(RightsType::model()->findAll(array('order' => 'item_name asc')), 'id', 'item_name'), array('labelOptions' => array('label' => false))) ?>
    <div class="m_button" style="vertical-align: top;">
        <input type="submit" value="Добавить" class="btn1">
    </div>
<?php $this->endWidget(); ?>
<?php $this->widget('bootstrap.widgets.TbGridView', array(
    'id' => 'user-access-log',
    'type'=>'striped bordered condensed',
    'dataProvider'=>$provider,
    'template'=>"{pager}\n{items}",
    'rowCssClass' => array('odd parent', 'even parent'),
    'columns'=>array(
        array(
            'header' => 'Логин',
            'type' => 'raw',
            'value' => '$data->user->getFullLogin()',
        ),
        array(
            'header' => 'Доступ',
            'type' => 'raw',
            'value' => '$data->rights_type->item_name'
        ),
        array(
            'class'=>'ext.GridButtons.Buttons',
            'htmlOptions'=>array('style'=>'width: 50px'),
            'deleteButtonUrl' => 'Yii::app()->createUrl("/admin/rights/delete", array("user_id" => $data->user_id, "item_id" => $data->item_id))',
            'template' => '{delete}'
        ),
    ),
)); ?>
<script>
    $(function(){
        $( "#Rights_user_id" ).autocomplete({
            source: function( request, response ) {
                $.ajax({
                    url: "<?php echo Yii::app()->createUrl('/site/users'); ?>",
                    dataType: "json",
                    data: {search: request.term},
                    success: function( data ) {
                        response( $.map( data, function( item ) {
                            return {
                                value: item.login
                            }
                        }));
                    }
                });
            },
            minLength: 2
        });
    });
</script>