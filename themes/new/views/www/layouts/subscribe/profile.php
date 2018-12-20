<?php $this->beginContent('webroot.themes.'.Yii::app()->theme->name.'.views.www.layouts.main'); ?>
<?php $this->widget('bootstrap.widgets.TbMenu', array(
    'type'=>'tabs', // '', 'tabs', 'pills' (or 'list')
    'stacked'=>false, // whether this is a stacked menu
    'encodeLabel' => false,
    'items'=>array(
        array('label'=>'Подписки на блоги', 'url' => array('/subscribe/index/user', 'gameId' => \Yii::app()->user->game_id)),
        array('label'=>'Подписки на сообщества', 'url' => array('/subscribe/index/community', 'gameId' => \Yii::app()->user->game_id)),
        array('label'=>'Подписки на дискуссии', 'url' => array('/subscribe/index/debate', 'gameId' => \Yii::app()->user->game_id)),
    ),
)); ?>
<?php echo $content; ?>
<?php $this->endContent(); ?>