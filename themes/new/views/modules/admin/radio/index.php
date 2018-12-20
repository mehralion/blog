<?php
/**
 * Created by PhpStorm.
 * User: Николай
 * Date: 19.11.13
 * Time: 21:20
 * @var TbActiveForm $form
 * @var RadioSettings $model
 * @var Radio $radio
 */  ?>
<style>
    .filters, .settings {
        display: inline-block;
        vertical-align: top;
        width: 300px;
    }
    .filters label {
        width: 80px !important;
    }
    .filters .controls {
        margin-left: 100px !important;
    }
</style>
<?php
$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
    'id'=>'form-report',
    'action' => Yii::app()->createUrl('/admin/radio/report'),
    'type'=>'horizontal',
    'enableAjaxValidation' => false,
)); ?>
<?php $this->widget('bootstrap.widgets.TbDatePicker', array(
    'name' => 'Report[start]',
    'options' => array('format' => 'dd.mm.yyyy', 'autoclose' => true),
    'htmlOptions' => array('placeholder' => 'Начальная дата')
)); ?>
<span style="width: 10px;"></span>
<?php $this->widget('bootstrap.widgets.TbDatePicker', array(
    'name' => 'Report[end]',
    'options' => array('format' => 'dd.mm.yyyy', 'autoclose' => true),
    'htmlOptions' => array('placeholder' => 'Конечная дата')
)); ?>
<span style="width: 10px;"></span>
<div class="m_button" style="vertical-align: top;">
    <input type="submit" value="Скачать файл для оплаты" class="btn1">
</div>
<?php $this->endWidget(); ?>
<ul id="sub-tab" class="nav nav-tabs">
    <li class="active"><a href="tab1">Лог</a></li>
    <li><a href="tab2">Итог</a></li>
    <li><a href="tab3">Лог запросов</a></li>
</ul>

<div id="tab1" class="tab">
    <div class="filters">
        <?php
        $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
            'id'=>'form-dj-log',
            'type'=>'horizontal',
            'enableAjaxValidation' => false,
            'htmlOptions' => array('style' => 'width:800px;')
        )); ?>
        <div class="" style="display: inline-block;vertical-align: top;">
            <?php echo $form->dropDownListRow($radio, 'user_id', CMap::mergeArray(array('' => 'Все'), Chtml::listData(UserDj::model()->findAll(array('group' => 'user_id', 'order' => 'login asc')), 'user_id', 'user.login'))); ?>
        </div>
        <div class="" style="display: inline-block;">
            <?php $this->widget('bootstrap.widgets.TbDatePicker', array(
                'model' => $radio,
                'attribute' => 'date_start',
                'form' => $form,
                'options' => array('format' => 'dd.mm.yyyy', 'autoclose' => true),
                'htmlOptions' => array('placeholder' => 'Начало')
            )); ?>
        </div>
        <div class="" style="display: inline-block;">
            <?php $this->widget('bootstrap.widgets.TbDatePicker', array(
                'model' => $radio,
                'attribute' => 'date_end',
                'form' => $form,
                'options' => array('format' => 'dd.mm.yyyy', 'autoclose' => true),
                'htmlOptions' => array('placeholder' => 'Конец')
            )); ?>
        </div>
        <div class="m_button" style="vertical-align: top;width: 100%;text-align: right;cursor: default;">
            <input type="submit" value="Фильтровать" class="btn1 button">
        </div>
        <?php $this->endWidget(); ?>
    </div>
    <?php $this->widget('bootstrap.widgets.TbGridView', array(
        'id' => 'grid-dj-log',
        'type'=>'striped bordered condensed',
        'dataProvider'=>$radioGrid,
        'template'=>"{pager}\n{items}",
        'rowCssClass' => array('odd parent', 'even parent'),
        'afterAjaxUpdate' => 'js:function(){ $.fancybox.hideLoading(); }',
        'columns'=>array(
            array(
                'header' => 'Логин',
                'type' => 'raw',
                'value' => '$data->user->getFullLogin()',
            ),
            array(
                'header' => 'Начало эфира',
                'type' => 'raw',
                'value' => '$data->start_datetime'
            ),
            array(
                'header' => 'Конец эфира',
                'type' => 'raw',
                'value' => '$data->end_datetime==null?"В Эфире (".date("Y-m-d H:i", strtotime($data->next_update_datetime)).")":$data->end_datetime'
            ),
            array(
                'header'=>'Радио',
                'value' => 'str_replace(array(Radio::RADIO_TYPE_RUSFM, Radio::RADIO_TYPE_OLDFM), array("Рус", "Олд"), $data->radio_type)'
            ),
            array(
                'header'=>'Тип завершения',
                'value' => 'str_replace(array(0, Radio::FINISH_NO_CHECK, Radio::FINISH_OFFLINE), array("", "Вылет по каптче", "Вышел в оффлайн"), $data->finish_type)'
            ),
            array(
                'header'=>'Время в эфире',
                'value'=>'Radio::getEfirTime($data)'
            )
        ),
    )); ?>
</div>
<div id="tab2" class="tab" style="display: none;">
    <div class="filters">
        <?php
        $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
            'id'=>'form-dj-result',
            'type'=>'horizontal',
            'enableAjaxValidation' => false,
            'htmlOptions' => array('style' => 'width:800px;')
        )); ?>
        <div class="" style="display: inline-block;">
            <?php $this->widget('bootstrap.widgets.TbDatePicker', array(
                'model' => $radio,
                'attribute' => 'date_start',
                'form' => $form,
                'options' => array('format' => 'dd.mm.yyyy', 'autoclose' => true),
                'htmlOptions' => array('placeholder' => 'Начало', 'id' => 'Radio_date_start_result')
            )); ?>
        </div>
        <div class="" style="display: inline-block;">
            <?php $this->widget('bootstrap.widgets.TbDatePicker', array(
                'model' => $radio,
                'attribute' => 'date_end',
                'form' => $form,
                'options' => array('format' => 'dd.mm.yyyy', 'autoclose' => true)
            ,
                'htmlOptions' => array('placeholder' => 'Конец', 'id' => 'Radio_date_end_result')
            )); ?>
        </div>
        <div class="m_button" style="vertical-align: top;cursor: default;">
            <input type="submit" value="Фильтровать" class="btn1 button">
        </div>
        <?php $this->endWidget(); ?>
    </div>
    <?php $this->widget('bootstrap.widgets.TbGridView', array(
        'id' => 'grid-dj-result',
        'type'=>'striped bordered condensed',
        'dataProvider'=>$resultGrid,
        'template'=>"{pager}\n{items}",
        'rowCssClass' => array('odd parent', 'even parent'),
        'afterAjaxUpdate' => 'js:function(){ $.fancybox.hideLoading(); }',
        'columns'=>array(
            array(
                'header' => 'Логин',
                'type' => 'raw',
                'value' => '$data["login"];',
            ),
            array(
                'header' => 'Кол-во часов',
                'type' => 'raw',
                'value' => '$data["totalHours"]'
            ),
            array(
                'header' => 'Вылетов по каптче',
                'type' => 'raw',
                'value' => '$data["finish_shtraf"]'
            ),
            array(
                'header'=>'Радио',
                'value' => 'Radio::buildRadio($data)'
            ),
            array(
                'header' => 'Всего часов',
                'value' => '$data["total"]'
            ),
            array(
                'header' => 'Подключений',
                'value' => '$data["user_count"]'
            ),
            array(
                'header' => 'Цена за час',
                'value' => '$data["coef"]'
            )
        ),
    )); ?>
</div>

<div id="tab3" class="tab" style="display: none;">
    <div class="filters">
        <?php
        $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
            'id'=>'form-log-result',
            'type'=>'horizontal',
            'enableAjaxValidation' => false,
            'htmlOptions' => array('style' => 'width:800px;')
        )); ?>
        <div class="" style="display: inline-block;">
            <?php $this->widget('bootstrap.widgets.TbDatePicker', array(
                'name' => 'RadioLog[date_start_result]',
                'form' => $form,
                'options' => array('format' => 'dd.mm.yyyy', 'autoclose' => true),
                'htmlOptions' => array('placeholder' => 'Начало', 'id' => 'RadioLog_date_start_result')
            )); ?>
        </div>
        <div class="" style="display: inline-block;">
            <?php $this->widget('bootstrap.widgets.TbDatePicker', array(
                'name' => 'RadioLog[date_end_result]',
                'form' => $form,
                'options' => array('format' => 'dd.mm.yyyy', 'autoclose' => true)
            ,
                'htmlOptions' => array('placeholder' => 'Конец', 'id' => 'RadioLog_date_end_result')
            )); ?>
        </div>
        <div class="m_button" style="vertical-align: top;cursor: default;">
            <input type="submit" value="Фильтровать" class="btn1 button">
        </div>
        <?php $this->endWidget(); ?>
    </div>
    <?php $this->widget('bootstrap.widgets.TbGridView', array(
        'id' => 'grid-log-result',
        'type'=>'striped bordered condensed',
        'dataProvider'=>$radioLogProvider,
        'template'=>"{pager}\n{items}",
        'rowCssClass' => array('odd parent', 'even parent'),
        'afterAjaxUpdate' => 'js:function(){ $.fancybox.hideLoading(); }',
        'columns'=>array(
            array(
                'header' => 'Персонаж',
                'type' => 'raw',
                'value' => '$data->getUser()',
            ),
            array(
                'header' => 'Описание',
                'type' => 'raw',
                'value' => 'str_replace(array("\n"), array("<br>"), $data->description1);',
            ),
            array(
                'header' => 'Радио',
                'type' => 'raw',
                'value' => '$data->getRadio()',
            ),
            array(
                'header' => 'Время',
                'type' => 'raw',
                'value' => 'date("d.m.Y H:i:s", strtotime($data->create_datetime))'
            ),
        ),
    )); ?>
</div>

<script>
    $(function(){
        $(document.body).on('click', '#sub-tab li a', function(event){
            event.preventDefault();
            var $self = $(this);

            $('#sub-tab li').removeClass('active');
            $('.tab').hide();
            $self.parent().addClass('active');

            $('#'+$self.attr('href')+'.tab').show();
        });

        $(document.body).on('click', '#form-dj-log .button', function(event){
            event.preventDefault();

            var data = {
                'Log[user_id]' : $('#Radio_user_id').val(),
                'Log[date_start]' : $('#Radio_date_start').val(),
                'Log[date_end]' : $('#Radio_date_end').val()
            };
            $.fancybox.showLoading();

            $.fn.yiiGridView.update('grid-dj-log', {data: data});
        });

        $(document.body).on('click', '#form-dj-result .button', function(event){
            event.preventDefault();

            var data = {
                'Result[date_start]' : $('#Radio_date_start_result').val(),
                'Result[date_end]' : $('#Radio_date_end_result').val()
            };
            $.fancybox.showLoading();

            $.fn.yiiGridView.update('grid-dj-result', {data: data});
        });

        $(document.body).on('click', '#form-log-result .button', function(event){
            event.preventDefault();

            var data = {
                'RadioLog[date_start]' : $('#RadioLog_date_start_result').val(),
                'RadioLog[date_end]' : $('#RadioLog_date_end_result').val()
            };
            $.fancybox.showLoading();

            $.fn.yiiGridView.update('grid-log-result', {data: data});
        });
    });
</script>
