<?php $this->beginContent('webroot.themes.'.Yii::app()->theme->name.'.views.www.layouts.main'); ?>
<?php $this->widget('bootstrap.widgets.TbMenu', array(
    'type'=>'tabs', // '', 'tabs', 'pills' (or 'list')
    'stacked'=>false, // whether this is a stacked menu
    'encodeLabel' => false,
    'items'=>array(
        array('label'=>'Пользователи', 'url' => array('/admin/user/index'), 'visible' => Yii::app()->user->isAdmin()),
        array('label'=>'Радио', 'url' => array('/admin/radio/index')),
        array('label'=>'Права доступа', 'url' => array('/admin/rights/index'), 'visible' => Yii::app()->user->isAdmin()),
    ),
)); ?>
<?php echo $content; ?>
<?php $this->endContent(); ?>