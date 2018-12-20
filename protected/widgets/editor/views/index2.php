<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Николай
 * Date: 22.06.13
 * Time: 9:22
 * To change this template use File | Settings | File Templates.
 *
 * @var EditorWidget $this
 */
?>
<ul class="text_editor" id="<?php echo $this->uniq; ?>">
    <li class="editor">
        <?php echo $this->form->textArea($this->model, $this->attributeName, $this->htmlOptions); ?>
    </li>
    <li>
        <ul class="buttons editorButtons">
            <li id="table" rel="tooltip" class="popover_btn ebtn" title="[table],[th],[/th],[tr],[td],[/td],[/tr],[/table]"><?php echo CHtml::image(Yii::app()->theme->baseUrl.'/images/editor/table.png'); ?></li>
            <li id="b" rel="tooltip" class="ebtn" title="[b]Текст болдом[/b]"><?php echo CHtml::image(Yii::app()->theme->baseUrl.'/images/editor/b.png'); ?></li>
            <li id="i" rel="tooltip" class="ebtn" title="[i]Наклонный текст[/i]"><?php echo CHtml::image(Yii::app()->theme->baseUrl.'/images/editor/i.png'); ?></li>
            <li id="u" rel="tooltip" class="ebtn" title="[u]Подчеркнутый текст[/u]"><?php echo CHtml::image(Yii::app()->theme->baseUrl.'/images/editor/u.png'); ?></li>
            <li id="color" rel="tooltip" class="ebtn" title='[color="Цвет"]Цветной текст[/color]'><?php echo CHtml::image(Yii::app()->theme->baseUrl.'/images/editor/color.png'); ?></li>
            <li id="hide" rel="tooltip" class="ebtn" title='[hide="Текст спойлера"]Спрятанный текст[/hide]'><?php echo CHtml::image(Yii::app()->theme->baseUrl.'/images/editor/hide.png'); ?></li>
            <li id="info" rel="tooltip" class="ebtn" title="[info]Логин персонажа[/info]"><?php echo CHtml::image(Yii::app()->theme->baseUrl.'/images/editor/info.png'); ?></li>
            <li id="link" rel="tooltip" class="ebtn" title='[link="Ссылка"]Текст ссылки[/link]'><?php echo CHtml::image(Yii::app()->theme->baseUrl.'/images/editor/link.png'); ?></li>
            <li id="image" rel="tooltip" class="ebtn" title="[image]Ссылка на картинку[/image]"><?php echo CHtml::image(Yii::app()->theme->baseUrl.'/images/editor/pic.png'); ?></li>
            <li id="smile" rel="tooltip" class="popover_btn ebtn" title='[smile="смайл"]'><?php echo CHtml::image(Yii::app()->theme->baseUrl.'/images/editor/smile.png'); ?></li>
            <li id="quote" rel="tooltip" class="ebtn" title='[quote="Логин персонажа (необязательно)"]Текст цитаты[/quote]'><?php echo CHtml::image(Yii::app()->theme->baseUrl.'/images/editor/quote.png'); ?></li>
            <li id="audio" rel="tooltip" class="ebtn" title="[mp3]Прямая ссылка на аудиозапись[/mp3]"><?php echo CHtml::image(Yii::app()->theme->baseUrl.'/images/editor/audio.png'); ?></li>
        </ul>
    </li>
    <?php if($this->button): ?>
    <li class="buttons" style="text-align: center;margin-top: 20px;margin-bottom: 20px;">
        <div class="m_button">
            <input type="submit" class="btn2" value="Добавить">
        </div>
    </li>
    <?php endif; ?>
</ul>
<?php $this->widget('application.widgets.colorPicker2.ColorPicker2Widget', array(
    'selector' => '#'.$this->uniq.' li#color',
    'editor' => $this->editor
)); ?>