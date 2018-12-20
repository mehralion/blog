<?php $this->beginContent('webroot.themes.'.Yii::app()->theme->name.'.views.www.layouts.main'); ?>
<?php $this->widget('bootstrap.widgets.TbMenu', array(
    'type'=>'tabs', // '', 'tabs', 'pills' (or 'list')
    'stacked'=>false, // whether this is a stacked menu
    'encodeLabel' => false,
    'items'=>array(
        array('label'=>'Заметки', 'url' => array('/event/news/post', 'type' => 'friend')),
        array('label'=>'Фотографии', 'url' => array('/event/news/image', 'type' => 'friend')),
        array('label'=>'Аудиозаписи', 'url' => array('/event/news/audio', 'type' => 'friend')),
        array('label'=>'Видеозаписи', 'url' => array('/event/news/video', 'type' => 'friend')),
        array('label'=>'Комментарии', 'url' => array('/event/comment/friend')),
    ),
)); ?>
<?php echo $content; ?>
<?php $this->endContent(); ?>