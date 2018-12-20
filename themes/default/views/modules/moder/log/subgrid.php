<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Nick Nikitchenko
 * Skype: quietasice
 * E-mail: quietasice123@gmail.com
 * Date: 11.07.13
 * Time: 20:23
 * To change this template use File | Settings | File Templates.
 *
 * @var ModerLog $model
 */ ?>


<?php $this->widget('bootstrap.widgets.TbGridView', array(
    'id' => 'sub-grid-log',
    'type'=>'striped bordered condensed',
    'dataProvider'=>$model->search($parentId),
    'template'=>"{items}",
    'enableSorting' => false,
    //'rowCssClass' => array('odd sub', 'even sub'),
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
    });
</script>