<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Nick Nikitchenko
 * Skype: quietasice
 * E-mail: quietasice123@gmail.com
 * Date: 04.07.13
 * Time: 17:38
 * To change this template use File | Settings | File Templates.
 *
 * @var ItemButtonsWidget $this
 */ ?>

<div class="materialButton">
    <?php
    if($this->quote)
        echo CHtml::link('<i class="icon" id="quote" title="Цитировать"></i>', '#', array('class' => 'set_quote', 'data-content' => '[quote="'.$model->user->login.'"]'."\n".$model->description."\n".'[/quote]'));
    ?>
    <?php if ($this->edit && null !== $this->editLink): ?>
        <?php echo CHtml::link(
            '<i class="icon" id="edit" title="Редактировать"></i>',
            $this->editLink,
            array('class' => 'update item')
        ); ?>
    <?php endif; ?>
    <?php if($this->delete && null !== $this->deleteLink): ?>
        <?php echo CHtml::link(
            '<i class="icon" id="del" title="Удалить"></i>',
            $this->deleteLink,
            array('confirm' => $this->deleteText)
        ) ?>
    <?php endif; ?>
    <?php if($this->moderDelete && null !== $this->deleteModerLink): ?>
        <?php echo CHtml::link(
            '<i class="icon" id="del" title="Удалить"></i>',
            $this->deleteModerLink,
            array(
                //'confirm' => "Вы уверены, что хотите удалить эту заметку? \n",
                'class' => 'moder_delete fancybox.ajax'
            )
        ) ?>
    <?php endif; ?>
    <?php if($this->report && null !== $this->reportLink): ?>
        <?php echo CHtml::link(
            '<i class="icon" id="report" title="Пожаловаться"></i>',
            $this->reportLink
        ); ?>
    <?php endif; ?>
</div>