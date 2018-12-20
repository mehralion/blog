<?php $this->beginContent('webroot.themes.'.Yii::app()->theme->name.'.views.www.layouts.main'); ?>
<?php $this->widget('bootstrap.widgets.TbMenu', array(
    'type'=>'tabs', // '', 'tabs', 'pills' (or 'list')
    'stacked'=>false, // whether this is a stacked menu
    'encodeLabel' => false,
    'items'=>array(
        array('label'=>'Заметки', 'url' => array('/subscribe/show/post', 'gameId' => \Yii::app()->user->game_id)),
        array('label'=>'Фотографии', 'url' => array('/subscribe/show/image', 'gameId' => \Yii::app()->user->game_id)),
        array('label'=>'Аудиозаписи', 'url' => array('/subscribe/show/audio', 'gameId' => \Yii::app()->user->game_id)),
        array('label'=>'Видеозаписи', 'url' => array('/subscribe/show/video', 'gameId' => \Yii::app()->user->game_id)),
        array('label'=>'Комментарии', 'url' => array('/subscribe/show/comment', 'gameId' => \Yii::app()->user->game_id)),
        array('label'=>'Дискуссии', 'url' => array('/subscribe/show/debate', 'gameId' => \Yii::app()->user->game_id)),
    ),
)); ?>
<?php echo $content; ?>
<?php $this->endContent(); ?>