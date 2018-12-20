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
    <?php foreach($buttons as $button): ?>
        <?php echo CHtml::link(
            '<i class="icon" id="'.$button['icon'].'" title="'.$button['title'].'"></i>',
            $button['link'],
            $button['htmlOptions']
        ); ?>
    <?php endforeach; ?>
</div>