<?php
/**
 * @var \application\modules\trunc\controllers\ShowController $this
 */
?>
<?php $this->beginContent('webroot.themes.'.Yii::app()->theme->name.'.views.www.layouts.main'); ?>
<?php
$postLabel = 'Заметки';
$imageLabel = 'Фотографии';
$audioLabel = 'Аудиозаписи';
$videoLabel = 'Видеозаписи';
$commentLabel = 'Комментарии';
?>
<?php $this->widget('bootstrap.widgets.TbMenu', array(
    'type'=>'tabs', // '', 'tabs', 'pills' (or 'list')
    'stacked'=>false, // whether this is a stacked menu
    'encodeLabel' => false,
    'items'=>array(
        array('label'=>$postLabel, 'url' => array('/community/trunc/post', 'community_alias' => Yii::app()->community->alias)),
        array('label'=>$imageLabel, 'url' => array('/community/trunc/image', 'community_alias' => Yii::app()->community->alias)),
        array('label'=>$audioLabel, 'url' => array('/community/trunc/audio', 'community_alias' => Yii::app()->community->alias)),
        array('label'=>$videoLabel, 'url' => array('/community/trunc/video', 'community_alias' => Yii::app()->community->alias)),
        array('label'=>$commentLabel, 'url' => array('/community/trunc/comment', 'community_alias' => Yii::app()->community->alias)),
        array(
            'label'=>'Очистить все',
            'url' => array('/community/trunc/delete', 'community_alias' => Yii::app()->community->alias),
            'linkOptions' => array(
                'confirm' => "Вы уверены, что хотите очистить корзину? \n Все материалы в корзине будут удалены навсегда."
            )
        ),
    ),
)); ?>
<?php echo $content; ?>
<?php $this->endContent(); ?>