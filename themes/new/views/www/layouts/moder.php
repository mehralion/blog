<?php $this->beginContent('webroot.themes.'.Yii::app()->theme->name.'.views.www.layouts.main'); ?>
<?php $this->widget('bootstrap.widgets.TbMenu', array(
    'type'=>'tabs', // '', 'tabs', 'pills' (or 'list')
    'stacked'=>false, // whether this is a stacked menu
    'encodeLabel' => false,
    'items'=>array(
        array('label'=>$this->module->postLabel, 'url' => array('/moder/post/index')),
        array('label'=>$this->module->imageLabel, 'url' => array('/moder/image/index')),
        array('label'=>$this->module->audioLabel, 'url' => array('/moder/audio/index')),
        array('label'=>$this->module->videoLabel, 'url' => array('/moder/video/index')),
        array('label'=>$this->module->commentLabel, 'url' => array('/moder/comment/index')),
        array('label'=>$this->module->communityLabel, 'url' => array('/moder/community/index')),
        array('label'=>'Логи', 'url' => array('/moder/log/index')),
    ),
)); ?>
<?php echo $content; ?>
<?php $this->endContent(); ?>