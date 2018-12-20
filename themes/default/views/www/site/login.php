<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Николай
 * Date: 10.06.13
 * Time: 18:22
 * To change this template use File | Settings | File Templates.
 */?>
<div style="color: red;text-align: center;">Блоги закрыты на несколько часов в связи с введением нового функционала.</div>
<?php $this->widget('bootstrap.widgets.TbAlert', array(
    'block'=>true, // display a larger alert block?
    'fade'=>true, // use transitions?
    'closeText'=> false, // close link text - if set to false, no close link is displayed
    'alerts'=>array( // configurations per alert type
        'error'=>array('block'=>true, 'fade'=>true, 'closeText'=> false), // success, info, warning, error or danger
    ),
)); ?>
<?php /** @var TbActiveForm $form */
$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
    'id'=>'verticalForm',
    'htmlOptions'=>array('class'=>'well', 'style' => 'margin:0 auto; width: 400px;'),
)); ?>

<?php echo $form->textFieldRow($model, 'login', array('class'=>'span3', 'labelOptions' => array('label' => false))); ?>
<?php echo $form->passwordFieldRow($model, 'password', array('class'=>'span3', 'labelOptions' => array('label' => false))); ?>
<div class="buttons">
    <?php $this->widget('bootstrap.widgets.TbButton', array('buttonType'=>'submit', 'label'=>'Войти')); ?>
</div>

<?php $this->endWidget(); ?>